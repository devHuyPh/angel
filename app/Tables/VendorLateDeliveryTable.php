<?php

namespace App\Tables;

use App\Models\VendorLateDelivery;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Base\Facades\Assets;
use Botble\Table\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class VendorLateDeliveryTable extends TableAbstract
{
    public function setup(): void
    {
        $this->model(VendorLateDelivery::class);

        Assets::addStylesDirectly('vendor/core/plugins/ecommerce/css/ecommerce.css');

        $this->addActions([
            EditAction::make()->route('vendor-late-delivery.edit'),
        ]);
    }

    public function routePrefix(): string
    {
        return 'vendor-late-delivery';
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->addColumn('store_name', function (VendorLateDelivery $item) {
                return e($item->store->name ?? __('Công ty'));
            })
            ->addColumn('order_code', function (VendorLateDelivery $item) {
                if (! $item->order) {
                    return '&mdash;';
                }

                return Html::link(route('orders.edit', $item->order->id), $item->order->code, ['class' => 'fw-semibold']);
            })
            ->addColumn('order_status', function (VendorLateDelivery $item) {
                $status = $item->order?->status;

                if ($status instanceof OrderStatusEnum) {
                    return BaseHelper::clean($status->toHtml());
                }

                if ($status) {
                    return BaseHelper::clean(OrderStatusEnum::make($status)->toHtml());
                }

                return '&mdash;';
            })
            ->addColumn('payment_status', function (VendorLateDelivery $item) {
                if ($item->order?->payment) {
                    return BaseHelper::clean($item->order->payment->status->toHtml());
                }

                return '&mdash;';
            })
            ->editColumn('amount', function (VendorLateDelivery $item) {
                $amount = $item->order?->payment?->amount ?? $item->order?->amount;

                return $amount !== null ? format_price($amount) : '&mdash;';
            })
            ->addColumn('reassign_flag', function (VendorLateDelivery $item) {
                if ((int) $item->status === 1) {
                    return '<span class="badge bg-warning text-dark">' . __('Đã chuyển kho') . '</span>';
                }

                return '&mdash;';
            })
            ->addColumn('order_created_at', function (VendorLateDelivery $item) {
                return $item->order
                    ? BaseHelper::formatDate($item->order->created_at)
                    : '&mdash;';
            })
            ->editColumn('updated_at', function (VendorLateDelivery $item) {
                return BaseHelper::formatDate($item->updated_at);
            })
            ->rawColumns(['order_code', 'order_status', 'payment_status', 'reassign_flag']);

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = VendorLateDelivery::on('mysql')
            ->with(['store', 'order', 'order.payment'])
            ->select('vendor_late_deliveries.*')
            ->orderByDesc('id');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            Column::make('store_name')
                ->title(__('Kho'))
                ->alignStart(),
            Column::make('order_code')
                ->title(__('Mã đơn hàng'))
                ->alignStart()
                ->orderable(false),
            Column::make('order_status')
                ->title(__('Trạng thái'))
                ->alignCenter()
                ->orderable(false)
                ->width(120),
            Column::make('payment_status')
                ->title(__('Trạng thái thanh toán'))
                ->width(140),
            Column::make('amount')
                ->title(__('Giá trị')),
            Column::make('reassign_flag')
                ->title(__('Cờ'))
                ->alignCenter()
                ->orderable(false)
                ->width(100),
            Column::make('order_created_at')
                ->title(__('Ngày tạo'))
                ->width(140),
            Column::make('updated_at')
                ->title(__('Ngày cập nhật'))
                ->width(140),
        ];
    }
}
