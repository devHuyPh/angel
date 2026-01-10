<?php

namespace Botble\Marketplace\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Tables\Formatters\PriceFormatter;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Tables\Traits\ForVendor;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class OrderTable extends TableAbstract
{
    use ForVendor;

    public function setup(): void
    {
        $this
            ->model(Order::class)
            ->addActions(array_filter([
                EditAction::make()->route('marketplace.vendor.orders.edit'),
                MarketplaceHelper::allowVendorDeleteTheirOrders()
                    ? DeleteAction::make()->route('marketplace.vendor.orders.destroy')
                    : null,
            ]));
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

                $status = $item->payment->status ?? null;

                if (! $status instanceof Enum) {
                    return '&mdash;';
                }

                return $status->label() ? BaseHelper::clean(
                    $status->toHtml()
                ) : '&mdash;';
            })
            ->formatColumn('amount', PriceFormatter::class)
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

        $data = $data
            ->filter(function ($query) {
                if ($status = $this->request->input('order_status')) {
                    $query->where('status', $status);
                }

                if ($paymentStatus = $this->request->input('payment_status')) {
                    $query->whereHas('payment', fn($payment) => $payment->where('status', $paymentStatus));
                }

                if ($keyword = $this->request->input('search.value')) {
                    return $query
                        ->whereHas('address', function ($subQuery) use ($keyword) {
                            return $subQuery
                                ->where('name', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('phone', 'LIKE', '%' . $keyword . '%');
                        })
                        ->orWhereHas('user', function ($subQuery) use ($keyword) {
                            return $subQuery
                                ->where('name', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('phone', 'LIKE', '%' . $keyword . '%');
                        })
                        ->orWhere('code', 'LIKE', '%' . $keyword . '%');
                }

                return $query;
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $with = ['user', 'address', 'shipment'];

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
                'payment_id',
            ])
            ->where('is_finished', 1)
            ->where('store_id', auth('customer')->user()->store->id)
            ->latest('change_to_store_at')
            ;

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
                        $item->user->email ?: $item->address->email,
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
                ->title(trans('plugins/marketplace::marketplace.product_details'))
                ->visible(false)
                ->exportable()
                ->orderable(false)
                ->searchable(false),
            Column::make('product_quantities')
                ->title(trans('plugins/marketplace::marketplace.product_quantities'))
                ->visible(false)
                ->exportable()
                ->orderable(false)
                ->searchable(false),
            Column::make('product_prices')
                ->title(trans('plugins/marketplace::marketplace.product_prices'))
                ->visible(false)
                ->exportable()
                ->orderable(false)
                ->searchable(false),
        ];

        if (is_plugin_active('payment')) {
            $columns = array_merge($columns, [
                Column::make('payment_status')
                    ->name('payment_id')
                    ->title(trans('plugins/marketplace::marketplace.payment_status')),
            ]);
        }

        return array_merge($columns, [
            StatusColumn::make()
                ->title(trans('plugins/marketplace::marketplace.order_status'))
                ->renderUsing(function (StatusColumn $column, $value) {
                    $item = $column->getItem();

                    if ($item instanceof \Botble\Ecommerce\Models\Order) {
                        $value = $item->status;
                    }

                    if (! $value instanceof Enum) {
                        return '';
                    }

                    $table = $column->getTable();

                    if ($table->isExportingToExcel() || $table->isExportingToCSV()) {
                        return $value->label() ?: $value->getValue();
                    }

                    return $value->toHtml() ?: $value->getValue();
                }),
            CreatedAtColumn::make(),
        ]);
    }

    public function getDefaultButtons(): array
    {
        return array_merge(['export'], parent::getDefaultButtons());
    }

    public function getFilters(): array
    {
        $filters = parent::getFilters();

        $filters = array_merge($filters, [
            'status' => [
                'title' => trans('plugins/marketplace::marketplace.order_status'),
                'type' => 'select',
                'choices' => OrderStatusEnum::labels(),
            ],
            'payment_status' => [
                'title' => trans('plugins/marketplace::marketplace.payment_status'),
                'type' => 'select',
                'choices' => is_plugin_active('payment') ? PaymentStatusEnum::labels() : [],
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ]);

        return $filters;
    }

    public function applyFilterCondition(
        Builder|QueryBuilder|Relation $query,
        string $key,
        string $operator,
        ?string $value
    ): Builder|QueryBuilder|Relation {
        switch ($key) {
            case 'status':
                if (! OrderStatusEnum::isValid($value)) {
                    return $query;
                }

                break;
            case 'payment_status':
                if (! is_plugin_active('payment') || ! PaymentStatusEnum::isValid($value)) {
                    return $query;
                }

                return $query->whereHas('payment', function ($subQuery) use ($value): void {
                    $subQuery->where('status', $value);
                });
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
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
