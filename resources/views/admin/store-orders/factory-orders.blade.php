@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @php
        $statusMap = [
            'completed' => ['color' => 'success', 'text' => trans('core/base::kho.status_completed'), 'icon' => 'ti ti-circle-check'],
            'pending' => ['color' => 'warning', 'text' => trans('core/base::kho.status_pending'), 'icon' => 'ti ti-clock-hour-3'],
            'processing' => ['color' => 'info', 'text' => trans('core/base::kho.status_processing'), 'icon' => 'ti ti-loader-2'],
            'shipping' => ['color' => 'info', 'text' => trans('core/base::kho.status_shipping'), 'icon' => 'ti ti-truck'],
            'delivered' => ['color' => 'info', 'text' => trans('core/base::kho.status_delivered'), 'icon' => 'ti ti-package-export'],
            'cancelled' => ['color' => 'danger', 'text' => trans('core/base::kho.status_cancelled'), 'icon' => 'ti ti-circle-x'],
            'unknown' => ['color' => 'secondary', 'text' => trans('core/base::kho.status_unknown'), 'icon' => 'ti ti-help'],
        ];

        $paymentStatusMap = [
            'completed' => ['color' => 'success', 'text' => trans('plugins/ecommerce::order.paid'), 'icon' => 'ti ti-currency-dollar'],
            'pending' => ['color' => 'warning', 'text' => trans('plugins/ecommerce::order.pending_payment'), 'icon' => 'ti ti-clock-hour-3'],
            'failed' => ['color' => 'danger', 'text' => 'Thanh toan that bai', 'icon' => 'ti ti-circle-x'],
            'unknown' => ['color' => 'secondary', 'text' => trans('core/base::kho.status_unknown'), 'icon' => 'ti ti-help'],
        ];

        $groups = [
            [
                'title' => trans('plugins/ecommerce::order.paid'),
                'orders' => $paidOrders,
                'badge' => 'success',
            ],
            [
                'title' => trans('plugins/ecommerce::order.pending_payment'),
                'orders' => $unpaidOrders,
                'badge' => 'warning',
            ],
        ];
    @endphp

    <div id="factory-orders-list">
        <div class="row row-cards">
            <div class="col-12">
                <x-core::card class="mb-3">
                    <x-core::card.header class="justify-content-between align-items-center flex-wrap gap-2">
                        <x-core::card.title class="d-flex align-items-center gap-2 mb-0">
                            <x-core::icon name="ti ti-package" />
                            {{ trans('core/base::kho.orders_from_factory') }}
                        </x-core::card.title>
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <input
                                type="text"
                                name="transaction_code"
                                value="{{ $search ?? '' }}"
                                class="form-control"
                                placeholder="{{ trans('core/base::kho.order_code') }}"
                            >
                            <x-core::button type="submit" color="primary">
                                {{ trans('core/base::kho.search') }}
                            </x-core::button>
                            @if ($search)
                                <a class="btn btn-outline-secondary" href="{{ route('admin.store-orders.factory-orders') }}">
                                    {{ trans('core/base::kho.reset') }}
                                </a>
                            @endif
                        </form>
                    </x-core::card.header>
                </x-core::card>

                @foreach ($groups as $group)
                    @php
                        $orders = $group['orders'];
                    @endphp
                    <x-core::card class="mb-3">
                        <x-core::card.header class="justify-content-between align-items-center">
                            <div>
                                <x-core::card.title class="mb-0">{{ $group['title'] }}</x-core::card.title>
                                <div class="text-muted small">
                                    {{ trans('core/base::kho.total') }}: {{ $orders->total() }}
                                </div>
                            </div>
                            <x-core::badge :color="$group['badge']" class="d-flex align-items-center gap-1">
                                {{ $group['title'] }}
                            </x-core::badge>
                        </x-core::card.header>

                        <x-core::table :hover="false" :striped="false" class="table-vcenter">
                            <x-core::table.header>
                                <x-core::table.header.cell style="width: 16%">{{ trans('core/base::kho.order_code') }}</x-core::table.header.cell>
                                <x-core::table.header.cell>{{ trans('core/base::kho.to_store') }}</x-core::table.header.cell>
                                <x-core::table.header.cell class="text-center" style="width: 16%">{{ trans('core/base::tables.created_at') }}</x-core::table.header.cell>
                                <x-core::table.header.cell class="text-end" style="width: 14%">{{ trans('core/base::kho.total_amount') }}</x-core::table.header.cell>
                                <x-core::table.header.cell class="text-center" style="width: 16%">{{ trans('core/base::kho.payment_status') }}</x-core::table.header.cell>
                                <x-core::table.header.cell class="text-center" style="width: 14%">{{ trans('core/base::kho.status') }}</x-core::table.header.cell>
                                <x-core::table.header.cell class="text-center" style="width: 8%">{{ trans('core/base::kho.actions') }}</x-core::table.header.cell>
                            </x-core::table.header>

                            <x-core::table.body>
                                @forelse ($orders as $order)
                                    @php
                                        $status = $order->status ?? 'unknown';
                                        $statusData = $statusMap[$status] ?? $statusMap['unknown'];
                                        $payment = $order->payment_status ?? 'pending';
                                        $paymentData = $paymentStatusMap[$payment] ?? $paymentStatusMap['unknown'];
                                    @endphp
                                    <x-core::table.body.row>
                                        <x-core::table.body.cell class="fw-semibold">
                                            {{ $order->transaction_code }}
                                        </x-core::table.body.cell>
                                        <x-core::table.body.cell>
                                            <div class="fw-semibold">{{ $order->toStore->name ?? '---' }}</div>
                                            @if ($order->toStore && $order->toStore->storeLevel)
                                                <div class="text-muted small">{{ $order->toStore->storeLevel->name }}</div>
                                            @endif
                                        </x-core::table.body.cell>
                                        <x-core::table.body.cell class="text-center text-muted">
                                            {{ $order->created_at ? BaseHelper::formatDateTime($order->created_at) : '---' }}
                                        </x-core::table.body.cell>
                                        <x-core::table.body.cell class="text-end fw-semibold text-success">
                                            {{ format_price($order->amount) }}
                                        </x-core::table.body.cell>
                                        <x-core::table.body.cell class="text-center">
                                            <x-core::badge :color="$paymentData['color']" class="d-inline-flex align-items-center gap-1">
                                                <x-core::icon :name="$paymentData['icon']" />
                                                {{ $paymentData['text'] }}
                                            </x-core::badge>
                                        </x-core::table.body.cell>
                                        <x-core::table.body.cell class="text-center">
                                            <x-core::badge :color="$statusData['color']" class="d-inline-flex align-items-center gap-1">
                                                <x-core::icon :name="$statusData['icon']" />
                                                {{ $statusData['text'] }}
                                            </x-core::badge>
                                        </x-core::table.body.cell>
                                        <x-core::table.body.cell class="text-center">
                                            <div class="btn-list justify-content-center mb-0">
                                                <x-core::button
                                                    tag="a"
                                                    color="info"
                                                    size="sm"
                                                    class="btn-icon"
                                                    href="{{ route('admin.store-orders.factory-view', $order->id) }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ trans('core/base::kho.view_update') }}"
                                                >
                                                    <x-core::icon name="ti ti-edit" />
                                                </x-core::button>
                                            </div>
                                        </x-core::table.body.cell>
                                    </x-core::table.body.row>
                                @empty
                                    <x-core::table.body.row>
                                        <x-core::table.body.cell colspan="7" class="text-center text-muted py-4">
                                            {{ trans('core/base::kho.no_orders') }}
                                        </x-core::table.body.cell>
                                    </x-core::table.body.row>
                                @endforelse
                            </x-core::table.body>
                        </x-core::table>

                        <x-core::card.footer class="d-flex justify-content-end">
                            {{ $orders->withQueryString()->links() }}
                        </x-core::card.footer>
                    </x-core::card>
                @endforeach
            </div>
        </div>
    </div>
@endsection

