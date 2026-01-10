@extends(EcommerceHelper::viewPath('customers.master'))

<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

@section('content')
    <style>
        @media (max-width: 767.98px) {

            .profile__tab-content {
                padding: 0 !important;
            }

            .form-control {
                font-size: 16px !important;
            }
        }
    </style>

    <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <a href="{{ route('setting') }}" class="back-btn text-success">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="header-title text-success">{{ __('Quản lý khu vực') }}</h1>
    </div>

    @include('notification_alert.active_account')
    <div class="container rounded-3 p-md-4">
        {{-- Ghi chú --}}
        <div class="note-box d-flex align-items-center justify-content-between p-2 mb-4">
            <span class="note-text blinking">
                <i class="fas fa-bell text-danger me-1"></i>
                {{ trans('plugins/marketplace::marketplace.Note: Actual commission will be based on order') }}
                <strong>{{ trans('plugins/marketplace::marketplace.success') }}</strong>.
            </span>
        </div>

        {{-- Khu vực quản lý và lọc --}}
        <div class="row mb-4 gy-3 align-items-end">
            {{-- Form lọc khu vực --}}
            <div class="col-12 col-md-4">
                <form method="GET" action="">
                    <div class="input-group">
                        <select name="state_id" class="form-select">
                            <option value="">-- {{ trans('plugins/marketplace::marketplace.All areas') }} --</option>
                            @foreach ($managedStates as $state)
                                <option value="{{ $state->state_id }}"
                                    {{ request('state_id') == $state->state_id ? 'selected' : '' }}>
                                    {{ $state->state_name }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-filter me-1"></i> {{ trans('plugins/marketplace::marketplace.fillter') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Danh sách khu vực được quản lý --}}
            <div class="col-12 col-md-8">
                <div class="bg-light p-3 rounded shadow-sm h-100">
                    <strong class="d-block mb-2 text-muted">
                        <i class="fas fa-map-marker-alt me-1 text-warning"></i>
                        {{ trans('plugins/marketplace::marketplace.The area you are managing') }}:
                    </strong>
                    <ul class="list-inline mb-0">
                        @foreach ($managedStates as $state)
                            <li class="list-inline-item bg-success text-white px-3 py-1 rounded mb-2">
                                <i class="fas fa-check-circle me-1"></i> {{ $state->state_name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>


        {{-- Thống kê --}}
        <div class="row mb-4 text-center">
            <div class="col-md-4">
                <div class="card stat-box text-white text-center p-3 mb-3">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <h6>{{ trans('plugins/marketplace::marketplace.total_order') }}</h6>
                    <p class="fs-4 mb-0">{{ $totalOrders }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-box text-white text-center p-3 mb-3">
                    <i class="fas fa-coins fa-2x mb-2"></i>
                    <h6>{{ trans('plugins/marketplace::marketplace.total_sales') }}</h6>
                    <p class="fs-4 mb-0">{{ format_price($totalRevenue) }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-box text-white text-center p-3 mb-3">
                    <i class="fas fa-percent fa-2x mb-2"></i>
                    <h6>{{ trans('plugins/marketplace::marketplace.commission') }} (5%)</h6>
                    <p class="fs-4 mb-0">{{ format_price($commission) }}</p>
                </div>
            </div>
        </div>

        {{-- Danh sách đơn hàng --}}
        <h4 class="mt-4 mb-3 text-success">
            <i class="fas fa-list text-info me-2 "></i>
            {{ trans('plugins/marketplace::marketplace.order_list') }}
        </h4>

        @if ($orders->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                {{ trans('plugins/marketplace::marketplace.There are no orders in this area yet.') }}
            </div>
        @else
            <div class="table-responsive">
                {{-- Table for desktop --}}
                <table class="table table-hover table-striped align-middle overflow-hidden d-none d-md-table">
                    <thead class=" text-center ">
                        <tr>
                            <th class="text-success"><i class="fas fa-user me-1"></i>
                                {{ trans('plugins/marketplace::marketplace.customer_name') }}</th>
                            <th class="text-success"><i class="fas fa-money-bill-wave me-1"></i>
                                {{ trans('plugins/marketplace::marketplace.total_amount') }}</th>
                            <th class="text-success"><i class="fas fa-calendar-alt me-1"></i>
                                {{ trans('plugins/marketplace::marketplace.date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->customer_name }}</td>
                                <td class="text-success fw-semibold">{{ format_price($order->total_amount) }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Cards for mobile --}}
                <div class="d-md-none">
                    @foreach ($orders as $order)
                        <div class="order-card p-3 mb-3 rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong><i class="fas fa-user me-1"></i> {{ $order->customer_name }}</strong>
                                <span class="text-success fw-bold">{{ format_price($order->total_amount) }}</span>
                            </div>
                            <div class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Custom CSS --}}
    <style>
        .note-box {
            background-color: #fef9e7;
            border-left: 6px solid #f80000;
        }

        .note-text {
            font-size: 0.85rem;
            color: #ff0000;
        }

        .blinking {
            animation: blinkingText 1.2s infinite;
        }

        @keyframes blinkingText {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .list-inline-item {
            margin-right: 5px;
            font-size: 0.9rem;
        }

        .input-group select {
            min-width: 160px;
        }

        .stat-box {
            background-color: #4BA213;
            border-radius: 12px;
        }

        .stat-box h6,
        .stat-box p,
        .stat-box i {
            color: #fff !important;
        }

        /* Card style for mobile orders */
        @media (max-width: 768px) {
            .order-card {
                font-size: 0.95rem;
                background-color: #fff;
            }

            .order-card strong {
                font-size: 1rem;
            }
        }
    </style>
@endsection
