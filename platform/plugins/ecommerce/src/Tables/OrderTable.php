<?php

namespace Botble\Ecommerce\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Botble\Ecommerce\Enums\OrderHistoryActionEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Tables\Formatters\PriceFormatter;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class OrderTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Order::class)
            ->addActions([
                EditAction::make()->route('orders.edit'),
                DeleteAction::make()->route('orders.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $isExporting = $this->isExportingToExcel() || $this->isExportingToCSV();

        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('payment_status', function (Order $item) {
                if (! is_plugin_active('payment')) {
                    return '&mdash;';
                }

                return $item->payment->status->label() ? BaseHelper::clean(
                    $item->payment->status->toHtml()
                ) : '&mdash;';
            })
            ->editColumn('payment_method', function (Order $item) {
                if (! is_plugin_active('payment')) {
                    return '&mdash;';
                }

                return BaseHelper::clean($item->payment->payment_channel->label() ?: '&mdash;');
            })
            ->formatColumn('amount', PriceFormatter::class)
            ->editColumn('shipping_amount', function (Order $item) {
                return $item->shipment->exists() ? $item->shipping_amount : '&mdash;';
            })
            ->addColumn('customer_address', function (Order $item) use ($isExporting): string {
                if (! $isExporting) {
                    return '';
                }

                $customerAddress = $this->formatAddress($item->address);

                if ($customerAddress === '-') {
                    $customerAddress = $this->formatAddress($item->billingAddress);
                }

                return $customerAddress;
            })
            ->addColumn('product_details', function (Order $item) use ($isExporting): string {
                if (! $isExporting) {
                    return '';
                }

                return $item->products
                    ->map(function (OrderProduct $product): string {
                        $sku = $product->options['sku'] ?? null;

                        return $product->product_name . ($sku ? ' (' . $sku . ')' : '');
                    })
                    ->implode(PHP_EOL);
            })
            ->addColumn('product_quantities', function (Order $item) use ($isExporting): string {
                if (! $isExporting) {
                    return '';
                }

                return $item->products
                    ->map(fn (OrderProduct $product): int => (int) $product->qty)
                    ->implode(PHP_EOL);
            })
            ->addColumn('product_prices', function (Order $item) use ($isExporting): string {
                if (! $isExporting) {
                    return '';
                }

                return $item->products
                    ->map(fn (OrderProduct $product): string => format_price($product->price))
                    ->implode(PHP_EOL);
            });

        if (EcommerceHelper::isTaxEnabled()) {
            $data = $data->formatColumn('tax_amount', PriceFormatter::class);
        }

        $data = $data
            ->filter(function ($query) {
                if ($keyword = $this->request->input('search.value')) {
                    $keyword = '%' . $keyword . '%';

                    return $query
                        ->whereHas('address', function ($subQuery) use ($keyword) {
                            return $subQuery
                                ->where('name', 'LIKE', $keyword)
                                ->orWhere('email', 'LIKE', $keyword)
                                ->orWhere('phone', 'LIKE', $keyword);
                        })
                        ->orWhereHas('user', function ($subQuery) use ($keyword) {
                            return $subQuery
                                ->where('name', 'LIKE', $keyword)
                                ->orWhere('email', 'LIKE', $keyword)
                                ->orWhere('phone', 'LIKE', $keyword);
                        })
                        ->orWhere('code', 'LIKE', $keyword);
                }

                return $query;
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $with = ['user', 'shipment', 'address'];

        if (is_plugin_active('payment')) {
            $with[] = 'payment';
        }

        if ($this->isExportingToExcel() || $this->isExportingToCSV()) {
            $with[] = 'products';
        }

        $query = $this
            ->getModel()
            ->query()
            ->with($with)
            ->select([
                'id',
                'status',
                'user_id',
                'created_at',
                'amount',
                'tax_amount',
                'shipping_amount',
                'payment_id',
            ])
            ->where('is_finished', 1);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        $columns = [
            IdColumn::make(),
            FormattedColumn::make('user_id')
                ->title(trans('plugins/ecommerce::order.email'))
                ->alignStart()
                ->orderable(false)
                ->renderUsing(function (FormattedColumn $column) {
                    $item = $column->getItem();

                    return sprintf(
                        '%s <br> %s <br> %s',
                        $item->user->name ?: $item->address->name,
                        Html::mailto($item->user->email ?: $item->address->email, obfuscate: false),
                        $item->user->phone ?: $item->address->phone
                    );
                }),
            Column::make('customer_address')
                ->title(trans('plugins/ecommerce::order.address'))
                ->visible(false)
                ->exportable()
                ->orderable(false)
                ->searchable(false),
            Column::formatted('amount')
                ->title(trans('plugins/ecommerce::order.amount')),
            Column::make('product_details')
                ->title('Chi tiết sản phẩm')
                ->visible(false)
                ->exportable()
                ->orderable(false)
                ->searchable(false),
            Column::make('product_quantities')
                ->title('Số lượng')
                ->visible(false)
                ->exportable()
                ->orderable(false)
                ->searchable(false),
            Column::make('product_prices')
                ->title('Giá')
                ->visible(false)
                ->exportable()
                ->orderable(false)
                ->searchable(false),
        ];

        if (is_plugin_active('payment')) {
            $columns = array_merge($columns, [
                Column::make('payment_method')
                    ->name('payment_id')
                    ->title(trans('plugins/ecommerce::order.payment_method'))
                    ->alignStart(),
                Column::make('payment_status')
                    ->name('payment_id')
                    ->title(trans('plugins/ecommerce::order.payment_status_label')),
            ]);
        }

        $columns[] = StatusColumn::make()
            ->alignStart()
            ->renderUsing(function (StatusColumn $column, $value) {
                if (! $value instanceof Enum) {
                    return '';
                }

                $table = $column->getTable();

                if ($table->isExportingToExcel() || $table->isExportingToCSV()) {
                    return $value->label() ?: $value->getValue();
                }

                return $value->toHtml() ?: $value->getValue();
            });

        if (EcommerceHelper::isTaxEnabled()) {
            $columns = array_merge($columns, [
                Column::formatted('tax_amount')
                    ->title(trans('plugins/ecommerce::order.tax_amount')),
            ]);
        }

        $columns = array_merge($columns, [
            Column::make('shipping_amount')
                ->title(trans('plugins/ecommerce::order.shipping_amount')),
        ]);

        return array_merge($columns, [
            CreatedAtColumn::make(),
        ]);
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('orders.create'), 'orders.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('orders.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => OrderStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', OrderStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function getFilters(): array
    {
        $filters = parent::getFilters();

        $filters = array_merge($filters, [
            'customer_name' => [
                'title' => trans('plugins/ecommerce::ecommerce.customer_name'),
                'type' => 'text',
            ],
            'customer_email' => [
                'title' => trans('plugins/ecommerce::ecommerce.customer_email'),
                'type' => 'text',
            ],
            'customer_phone' => [
                'title' => trans('plugins/ecommerce::ecommerce.customer_phone'),
                'type' => 'text',
            ],
            'amount' => [
                'title' => trans('plugins/ecommerce::order.amount'),
                'type' => 'number',
            ],
            'shipping_method' => [
                'title' => trans('plugins/ecommerce::ecommerce.shipping_method'),
                'type' => 'select',
                'choices' => array_filter(ShippingMethodEnum::labels()),
            ],
        ]);

        if (is_plugin_active('payment')) {
            $filters = array_merge($filters, [
                'payment_method' => [
                    'title' => trans('plugins/ecommerce::order.payment_method'),
                    'type' => 'select',
                    'choices' => PaymentMethodEnum::labels(),
                ],
                'payment_status' => [
                    'title' => trans('plugins/ecommerce::order.payment_status_label'),
                    'type' => 'select',
                    'choices' => PaymentStatusEnum::labels(),
                ],
            ]);
        }

        if (is_plugin_active('marketplace')) {
            $filters['store_id'] = [
                'title' => trans('plugins/marketplace::store.forms.store'),
                'type' => 'select-search',
                'choices' => [-1 => theme_option('site_title')] + DB::table('mp_stores')->pluck('name', 'id')->all(),
            ];
        }

        return $filters;
    }

    public function renderTable($data = [], $mergeData = []): View|Factory|Response
    {
        if ($this->isEmpty()) {
            return view('plugins/ecommerce::orders.intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    public function getDefaultButtons(): array
    {
        return array_merge(['export'], parent::getDefaultButtons());
    }

    public function saveBulkChangeItem(Model|Order $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if ($inputKey === 'status' && $inputValue == OrderStatusEnum::CANCELED) {
            /**
             * @var Order $item
             */
            if (! $item->canBeCanceledByAdmin()) {
                throw new Exception(trans('plugins/ecommerce::order.order_cannot_be_canceled'));
            }

            OrderHelper::cancelOrder($item);

            OrderHistory::query()->create([
                'action' => OrderHistoryActionEnum::CANCEL_ORDER,
                'description' => trans('plugins/ecommerce::order.order_was_canceled_by'),
                'order_id' => $item->getKey(),
                'user_id' => Auth::id(),
            ]);

            return $item;
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }

    public function applyFilterCondition(
        Builder|QueryBuilder|Relation $query,
        string $key,
        string $operator,
        ?string $value
    ): Builder|QueryBuilder|Relation {
        switch ($key) {
            case 'customer_name':
                if (! $value) {
                    break;
                }

                return $this->filterByCustomer($query, 'name', $operator, $value);
            case 'customer_email':
                if (! $value) {
                    break;
                }

                return $this->filterByCustomer($query, 'email', $operator, $value);
            case 'customer_phone':
                if (! $value) {
                    break;
                }

                return $this->filterByCustomer($query, 'phone', $operator, $value);
            case 'status':
                if (! OrderStatusEnum::isValid($value)) {
                    return $query;
                }

                break;
            case 'shipping_method':
                if (! $value) {
                    break;
                }

                if (! ShippingMethodEnum::isValid($value)) {
                    return $query;
                }

                break;
            case 'payment_method':
                if (! is_plugin_active('payment') || ! PaymentMethodEnum::isValid($value)) {
                    return $query;
                }

                return $query->whereHas('payment', function ($subQuery) use ($value): void {
                    $subQuery->where('payment_channel', $value);
                });

            case 'payment_status':
                if (! is_plugin_active('payment') || ! PaymentStatusEnum::isValid($value)) {
                    return $query;
                }

                return $query->whereHas('payment', function ($subQuery) use ($value): void {
                    $subQuery->where('status', $value);
                });
            case 'store_id':
                if (! is_plugin_active('marketplace')) {
                    return $query;
                }
                if ($value == -1) {
                    return $query->where(function ($subQuery): void {
                        $subQuery->whereNull('store_id')
                            ->orWhere('store_id', 0);
                    });
                }
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }

    protected function filterByCustomer(
        Builder|QueryBuilder|Relation $query,
        string $column,
        string $operator,
        ?string $value
    ): Builder|QueryBuilder|Relation {
        if ($operator === 'like') {
            $value = '%' . $value . '%';
        } elseif ($operator !== '=') {
            $operator = '=';
        }

        return $query
            ->where(function ($query) use ($column, $operator, $value): void {
                $query
                    ->whereHas('user', function ($subQuery) use ($column, $operator, $value): void {
                        $subQuery->where($column, $operator, $value);
                    })
                    ->orWhereHas('address', function ($subQuery) use ($column, $operator, $value): void {
                        $subQuery->where($column, $operator, $value);
                    });
            });
    }

    protected function formatAddress(?OrderAddress $address): string
    {
        if (! $address) {
            return '-';
        }

        $parts = [];

        $street = trim((string) $address->address);
        if ($street !== '') {
            $parts[] = $street;
        }

        $city = trim((string) $address->city_name);
        if ($city !== '' && ! $this->containsPart($street, $city)) {
            $parts[] = $city;
        }

        $state = trim((string) $address->state_name);
        if (
            $state !== ''
            && ! $this->containsPart($street, $state)
            && ! $this->containsPart($city, $state)
        ) {
            $parts[] = $state;
        }

        if (EcommerceHelper::isZipCodeEnabled()) {
            $zip = trim((string) $address->zip_code);
            if ($zip !== '') {
                $parts[] = $zip;
            }
        }

        if (EcommerceHelper::isUsingInMultipleCountries()) {
            $country = trim((string) $address->country_name);
            if ($country !== '') {
                $parts[] = $country;
            }
        }

        $parts = array_values(array_filter(array_unique($parts)));

        return $parts ? implode(', ', $parts) : '-';
    }

    protected function containsPart(string $haystack, string $needle): bool
    {
        if ($haystack === '' || $needle === '') {
            return false;
        }

        return str_contains(Str::lower($haystack), Str::lower($needle));
    }
}
