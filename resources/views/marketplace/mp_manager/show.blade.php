@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
    <div class="store-infor">
        {{ $store->name }} <br />
        {{ $store->email }} <br />
        {{ $store->phone }}
    </div>
    <section class="ps-dashboard report-chart-content" id="report-chart">
        <div class="row mb-3 mt-5">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="ps-block--stat yellow">
                    <div class="ps-block__left">
                        <span><i class="icon-bag2"></i></span>
                    </div>
                    <div class="ps-block__content">
                        <p>{{ __('Orders') }}</p>
                        <h4>{{ $store->orders->count() }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="ps-block--stat pink">
                    <div class="ps-block__left">
                        <span><i class="icon-bag-dollar"></i></span>
                    </div>
                    <div class="ps-block__content">
                        @php
                            $orders = $store->orders;
                            $total = 0;
                            foreach ($orders as $order) {
                                $total += $order->payment->where('status', 'completed')->amount;
                            }
                        @endphp
                        <p>{{ __('Revenue') }}</p>
                        <h4>{{ format_price($total) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="ps-block--stat green">
                    <div class="ps-block__left">
                        <span><i class="icon-database"></i></span>
                    </div>
                    <div class="ps-block__content">
                        <p>{{ __('Products') }}</p>
                        <h4>{{ $store->products->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Những đơn đặt hàng gần đây</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ngày</th>
                                    <th>Khách hàng</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Tổng cộng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td>
                                            {{ $order->code }}
                                        </td>
                                        <td>{{ date_format($order->created_at, 'H:i d-m-Y') }}</td>
                                        <td>
                                            {{ $order->user->name }}
                                        </td>
                                        <td>
                                            @php
                                                $status = $order->payment->status ?? 'unknown';

                                                $statusMap = [
                                                    'completed' => [
                                                        'class' => 'badge bg-success text-success-fg',
                                                        'text' => 'Đã hoàn thành',
                                                    ],
                                                    'pending' => [
                                                        'class' => 'badge bg-warning text-warning-fg',
                                                        'text' => 'Đang chờ',
                                                    ],
                                                    'failed' => [
                                                        'class' => 'badge bg-danger text-danger-fg',
                                                        'text' => 'Thanh toán lỗi',
                                                    ],
                                                    'refunded' => [
                                                        'class' => 'badge bg-info text-info-fg',
                                                        'text' => 'Đã hoàn tiền',
                                                    ],
                                                    'unknown' => [
                                                        'class' => 'badge bg-secondary text-secondary-fg',
                                                        'text' => 'Không xác định',
                                                    ],
                                                ];

                                                $statusData = $statusMap[$status] ?? $statusMap['unknown'];
                                            @endphp

                                            <span class="{{ $statusData['class'] }}">
                                                {{ $statusData['text'] }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $status = $order->status ?? 'unknown';

                                                $statusMap = [
                                                    'completed' => [
                                                        'class' => 'badge bg-success text-success-fg',
                                                        'text' => 'Đã hoàn thành',
                                                    ],
                                                    'pending' => [
                                                        'class' => 'badge bg-warning text-warning-fg',
                                                        'text' => 'Đang chờ',
                                                    ],
                                                    'failed' => [
                                                        'class' => 'badge bg-danger text-danger-fg',
                                                        'text' => 'Thanh toán lỗi',
                                                    ],
                                                    'processing' => [
                                                        'class' => 'badge bg-info text-info-fg',
                                                        'text' => 'Đang xử lý',
                                                    ],
                                                    'unknown' => [
                                                        'class' => 'badge bg-secondary text-secondary-fg',
                                                        'text' => 'Không xác định',
                                                    ],
                                                ];

                                                $statusData = $statusMap[$status] ?? $statusMap['unknown'];
                                            @endphp

                                            <span class="{{ $statusData['class'] }}">
                                                {{ $statusData['text'] }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ format_price($order->payment->amount) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted py-4">
                                            Không có đơn hàng nào.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Sản phẩm bán chạy nhất</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Đã bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bestSellPros as $bestSellPro)
                                    <tr>
                                        <td>{{ $bestSellPro->id }}</td>
                                        <td>
                                            {{ $bestSellPro->name }}
                                        </td>
                                        <td>{{ format_price($bestSellPro->sale_price ?? $bestSellPro->price) }}</td>
                                        <td>
                                            <span class="badge bg-success text-success-fg">Đã xuất bản</span>
                                        </td>
                                        <td>
                                            {{ $bestSellPro->sold_count }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted py-4">
                                            Không có sản phẩm nào.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;

            const links = document.querySelectorAll('a[href]');
            links.forEach(function(link) {
                const linkPath = new URL(link.href).pathname;

                if (linkPath === '/vendor/storemanager/index') {
                    if (currentPath.startsWith('/vendor/storemanager/')) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                }
            });
        });
    </script>
@endpush
