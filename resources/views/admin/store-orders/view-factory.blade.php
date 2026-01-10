@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @php
        $status = $order->status ?? 'unknown';
        $statusMap = [
            'completed' => [
                'color' => 'success',
                'text' => trans('core/base::kho.status_completed'),
                'icon' => 'ti ti-circle-check',
            ],
            'pending' => [
                'color' => 'warning',
                'text' => trans('core/base::kho.status_pending'),
                'icon' => 'ti ti-clock-hour-3',
            ],
            'processing' => [
                'color' => 'info',
                'text' => trans('core/base::kho.status_processing'),
                'icon' => 'ti ti-loader-2',
            ],
            'shipping' => [
                'color' => 'info',
                'text' => trans('core/base::kho.status_shipping'),
                'icon' => 'ti ti-truck',
            ],
            'delivered' => [
                'color' => 'info',
                'text' => trans('core/base::kho.status_delivered'),
                'icon' => 'ti ti-package-export',
            ],
            'cancelled' => [
                'color' => 'danger',
                'text' => trans('core/base::kho.status_cancelled'),
                'icon' => 'ti ti-circle-x',
            ],
            'unknown' => [
                'color' => 'secondary',
                'text' => trans('core/base::kho.status_unknown'),
                'icon' => 'ti ti-help',
            ],
        ];
        $statusData = $statusMap[$status] ?? $statusMap['unknown'];

        $paymentStatus = $order->payment_status ?? 'pending';
        $paymentStatusMap = [
            'completed' => ['color' => 'success', 'text' => trans('plugins/ecommerce::order.paid'), 'icon' => 'ti ti-currency-dollar'],
            'pending' => ['color' => 'warning', 'text' => trans('plugins/ecommerce::order.pending_payment'), 'icon' => 'ti ti-clock-hour-3'],
            'failed' => ['color' => 'danger', 'text' => 'Thanh toan that bai', 'icon' => 'ti ti-circle-x'],
            'unknown' => ['color' => 'secondary', 'text' => trans('core/base::kho.status_unknown'), 'icon' => 'ti ti-help'],
        ];
        $paymentStatusData = $paymentStatusMap[$paymentStatus] ?? $paymentStatusMap['unknown'];
    @endphp

    <div id="factory-order-content">
        <div class="row row-cards">
            <div class="col-md-9">
                <x-core::card class="mb-3">
                    <x-core::card.header class="justify-content-between align-items-center">
                        <div>
                            <x-core::card.title class="mb-0">
                                {{ trans('core/base::kho.factory_order_detail') }}
                                @if ($order->transaction_code)
                                    <span class="text-muted">#{{ $order->transaction_code }}</span>
                                @endif
                            </x-core::card.title>
                            <div class="text-muted small">
                                {{ trans('core/base::kho.to_store') }}: {{ $order->toStore->name ?? '---' }}
                                @if ($order->toStore && $order->toStore->storeLevel)
                                    ({{ $order->toStore->storeLevel->name }})
                                @endif
                            </div>
                        </div>

                        <x-core::badge :color="$statusData['color']" class="d-flex align-items-center gap-1">
                            <x-core::icon :name="$statusData['icon']" />
                            {{ $statusData['text'] }}
                        </x-core::badge>
                    </x-core::card.header>

                    <x-core::card.body>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="text-muted small">{{ trans('core/base::kho.order_code') }}</div>
                                <div class="fw-semibold">{{ $order->transaction_code }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">{{ trans('core/base::kho.total_amount') }}</div>
                                <div class="fw-semibold text-success">{{ format_price($order->amount) }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">{{ trans('core/base::tables.created_at') }}</div>
                                <div class="fw-semibold">
                                    {{ $order->created_at ? BaseHelper::formatDateTime($order->created_at) : '---' }}
                                </div>
                            </div>
                        </div>

                        <div class="hr my-3"></div>

                        <h4 class="mb-3">{{ trans('core/base::kho.product_list') }}</h4>

                        @php $productsTotal = 0; @endphp
                        <x-core::table :hover="false" :striped="false" class="order-products-table">
                            <x-core::table.header>
                                <x-core::table.header.cell>{{ trans('core/base::kho.product') }}</x-core::table.header.cell>
                                <x-core::table.header.cell class="text-end" style="width: 140px">
                                    {{ trans('plugins/ecommerce::order.price') }}
                                </x-core::table.header.cell>
                                <x-core::table.header.cell class="text-center" style="width: 40px">x</x-core::table.header.cell>
                                <x-core::table.header.cell class="text-center" style="width: 120px">
                                    {{ trans('core/base::kho.quantity') }}
                                </x-core::table.header.cell>
                                <x-core::table.header.cell class="text-end" style="width: 160px">
                                    {{ trans('core/base::kho.total_amount') }}
                                </x-core::table.header.cell>
                            </x-core::table.header>

                            <x-core::table.body>
                                @forelse ($order->products as $item)
                                    @php
                                        $product = $item->product;
                                        $unitPrice = $product->front_sale_price ?? $product->price ?? 0;
                                        $lineTotal = $unitPrice * (int) $item->qty;
                                        $productsTotal += $lineTotal;
                                    @endphp
                                    <x-core::table.body.row>
                                        <x-core::table.body.cell class="text-start">
                                            <div class="fw-semibold">{{ $product->name ?? trans('core/base::kho.deleted_product') }}</div>
                                            @if ($product && $product->sku)
                                                <div class="text-muted small">
                                                    {{ trans('plugins/ecommerce::order.sku') }}: {{ $product->sku }}
                                                </div>
                                            @endif
                                        </x-core::table.body.cell>
                                        <x-core::table.body.cell class="text-end">
                                            {{ format_price($unitPrice) }}
                                        </x-core::table.body.cell>
                                        <x-core::table.body.cell class="text-center">x</x-core::table.body.cell>
                                        <x-core::table.body.cell class="text-center">
                                            {{ $item->qty }}
                                        </x-core::table.body.cell>
                                        <x-core::table.body.cell class="text-end">
                                            {{ format_price($lineTotal) }}
                                        </x-core::table.body.cell>
                                    </x-core::table.body.row>
                                @empty
                                    <x-core::table.body.row>
                                        <x-core::table.body.cell colspan="5" class="text-center text-muted">
                                            {{ trans('core/base::kho.no_products') }}
                                        </x-core::table.body.cell>
                                    </x-core::table.body.row>
                                @endforelse
                            </x-core::table.body>
                        </x-core::table>

                        <div class="d-flex justify-content-end mt-3">
                            <div class="text-end">
                                <div class="text-muted small">{{ trans('core/base::kho.total_amount') }}</div>
                                <div class="fs-5 fw-semibold">{{ format_price($order->amount ?? $productsTotal) }}</div>
                            </div>
                        </div>
                    </x-core::card.body>

                    <div class="list-group list-group-flush">
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <div class="text-uppercase fw-semibold d-flex align-items-center gap-2">
                                <x-core::icon name="ti ti-arrows-exchange" />
                                {{ trans('core/base::kho.update_status') }}
                            </div>
                            <form
                                action="{{ route('admin.store-orders.factory-update-status', $order->id) }}"
                                method="POST"
                                class="d-flex align-items-center gap-2"
                            >
                                @csrf
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                        {{ trans('core/base::kho.status_pending') }}
                                    </option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                        {{ trans('core/base::kho.status_processing') }}
                                    </option>
                                    <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>
                                        {{ trans('core/base::kho.status_shipping') }}
                                    </option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                        {{ trans('core/base::kho.status_delivered') }}
                                    </option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
                                        {{ trans('core/base::kho.status_completed') }}
                                    </option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                        {{ trans('core/base::kho.status_cancelled') }}
                                    </option>
                                </select>
                                <x-core::button type="submit" color="primary">
                                    {{ trans('core/base::kho.update') }}
                                </x-core::button>
                            </form>
                        </div>
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <div class="text-uppercase fw-semibold d-flex align-items-center gap-2">
                                <x-core::icon name="ti ti-currency-dollar" />
                                {{ trans('core/base::kho.payment_status') }}
                            </div>
                            @if ($paymentStatus === 'completed')
                                <x-core::badge :color="$paymentStatusData['color']" class="d-inline-flex align-items-center gap-1">
                                    <x-core::icon :name="$paymentStatusData['icon']" />
                                    {{ $paymentStatusData['text'] }}
                                </x-core::badge>
                            @else
                                <form
                                    action="{{ route('admin.store-orders.factory-update-payment-status', $order->id) }}"
                                    method="POST"
                                    class="d-flex align-items-center gap-2"
                                >
                                    @csrf
                                    <select name="payment_status" class="form-select">
                                        <option value="pending" {{ $paymentStatus === 'pending' ? 'selected' : '' }}>
                                            {{ trans('plugins/ecommerce::order.pending_payment') }}
                                        </option>
                                        <option value="completed" {{ $paymentStatus === 'completed' ? 'selected' : '' }}>
                                            {{ trans('plugins/ecommerce::order.paid') }}
                                        </option>
                                        <option value="failed" {{ $paymentStatus === 'failed' ? 'selected' : '' }}>
                                            Thanh toan that bai
                                        </option>
                                    </select>
                                    <x-core::button type="submit" color="primary">
                                        {{ trans('core/base::kho.update') }}
                                    </x-core::button>
                                </form>
                            @endif
                        </div>
                        @if ($order->confirm_date)
                            <div class="p-3 border-top text-muted small">
                                <div class="d-flex align-items-center gap-2">
                                    <x-core::icon name="ti ti-calendar-stats" />
                                    {{ trans('core/base::kho.current_status') }}: {{ $statusData['text'] }}
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <x-core::icon name="ti ti-clock" />
                                    {{ BaseHelper::formatDateTime($order->confirm_date) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </x-core::card>

                @if ($order->completed_image)
                    <x-core::card>
                        <x-core::card.header>
                            <x-core::card.title>{{ trans('core/base::kho.completed_image') }}</x-core::card.title>
                        </x-core::card.header>
                        <x-core::card.body class="text-center">
                            <img
                                src="{{ Storage::url($order->completed_image) }}"
                                alt="{{ trans('core/base::kho.completed_image') }}"
                                class="img-fluid rounded"
                            >
                        </x-core::card.body>
                    </x-core::card>
                @endif
            </div>

            <div class="col-md-3">
                <x-core::card class="mb-3">
                    <x-core::card.header>
                        <x-core::card.title>{{ trans('core/base::kho.order_detail') }}</x-core::card.title>
                    </x-core::card.header>
                    <x-core::card.body>
                        <div class="mb-3">
                            <div class="text-muted small">{{ trans('core/base::kho.order_code') }}</div>
                            <div class="fw-semibold">{{ $order->transaction_code }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">{{ trans('core/base::kho.to_store') }}</div>
                            <div class="fw-semibold">{{ $order->toStore->name ?? '---' }}</div>
                            @if ($order->toStore && $order->toStore->storeLevel)
                                <div class="text-muted small">{{ $order->toStore->storeLevel->name }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">{{ trans('core/base::kho.status') }}</div>
                            <x-core::badge :color="$statusData['color']" class="mt-1">
                                {{ $statusData['text'] }}
                            </x-core::badge>
                        </div>
                        <div class="mb-0">
                            <div class="text-muted small">{{ trans('core/base::kho.total_amount') }}</div>
                            <div class="fw-semibold text-success">{{ format_price($order->amount) }}</div>
                        </div>
                    </x-core::card.body>
                </x-core::card>
            </div>
        </div>
    </div>
@endsection





