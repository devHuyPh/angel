@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Báo cáo người dùng'))

@section('content')
    <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <a href="{{ route('setting') }}" class="back-btn text-success">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="header-title text-success">{{ __('Quản lý khu vực') }}</h1>
    </div>
    @include('notification_alert.active_account')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .report-card {
            background: #4ba314;
            box-shadow: 0 2px 8px rgb(38 79 40 / 82%);
        }

        .report-icon {
            width: 50px;
            height: 50px;
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .badge-rank {
            background-color: #228822;
            color: #228822;
        }

        .scrollable-table {
            max-height: 450px;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        /* loc */
        .filter-wrapper {
            display: inline-flex;
            padding-top: 2px;
        }

        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.35);
            transition:
                transform 0.15s ease,
                box-shadow 0.15s ease,
                background 0.15s ease;
        }

        .filter-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(46, 125, 50, 0.45);
            background: linear-gradient(135deg, #57c85c, #2E7D32);
        }

        .filter-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(46, 125, 50, 0.3);
        }

        .filter-icon {
            display: inline-flex;
            width: 16px;
            height: 16px;
        }

        .filter-icon svg {
            width: 100%;
            height: 100%;
            fill: currentColor;
        }

        .filter-text {
            white-space: nowrap;
        }
        .filter-form{
            border-bottom: 1px solid #b2b2b2;
        }
        .form-control{
            height: 40px !important;
            /* color: white !important; */
        } 
        .btn-filter{
            color:white;
            height: 40px;
            font-size: 14px;
        }
    </style>

    <style>
        @media (max-width: 767.98px) {

            .bg-custom-moblie {
                padding: 0 !important;
            }

            .profile__tab-content {
                padding: 0 !important;
            }

            .h3-mobile-referral {
                font-size: 16px !important;
                background: #f8f8f8;
                padding: 0.5rem 0 0.5rem 10px !important;
            }

            
            .form-control-report-mobile {
                height: 40px !important;
                font-size: 12px !important;
            }

            .form-label-mobile {
                font-size: 12px !important;
                margin-bottom: 0 !important;
            }

            .flex-report-mobile {
                flex-direction: column !important;
            }

            .fs-report-mobile {
                font-size: 14px !important;
            }

            .fs-report-mobile-center {
                font-size: 14px !important;
                text-align: center !important;
            }
        }
    </style>

    <div class="container">
        <div class="card m-0 h-100 border-0 rounded-md-4 overflow-hidden">

            {{-- Bộ lọc thời gian --}}
            <div class="filter-wrapper">
                <button class="filter-btn" id="filterToggle">
                    <span class="filter-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 5h16v2l-6 6v5l-4 2v-7L4 7V5z"></path>
                        </svg>
                    </span>
                    <span class="filter-text">Lọc</span>
                </button>
            </div>
            <div class="pt-2 pt-md-4 filter-form d-none">
                <form method="GET" action="{{ route('client.dashboard') }}" class="row g-3 align-items-end pb-3">

                    <div class="col-6 col-md-3">
                        <label class="form-label-mobile fw-semibold"> @lang('plugins/marketplace::marketplace.from_month')</label>
                        <input
                            id="from_month"
                            name="from_month"
                            type="month"
                            class="form-control"
                            value="{{ request('from_month', now()->format('Y-m')) }}"
                        >
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label-mobile fw-semibold"> @lang('plugins/marketplace::marketplace.to_month')</label>
                        <input
                            id="to_month"
                            name="to_month"
                            type="month"
                            class="form-control"
                            value="{{ request('to_month') }}"
                        >
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label-mobile fw-semibold"> @lang('plugins/marketplace::marketplace.from_date')</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="form-control form-control-report-mobile">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label-mobile fw-semibold"> @lang('plugins/marketplace::marketplace.to_date')</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="form-control form-control-report-mobile">
                    </div>
                    <div class="col-6 d-grid col-md-2">
                        <button type="submit" class="btn btn-success rounded-3 form-control form-control-report-mobile btn-filter">
                            <i class="fas fa-filter me-2"></i> @lang('plugins/marketplace::marketplace.fillter')
                        </button>
                    </div>
                    <div class="col-6 col-md-2 d-grid">
                        <a href="{{ route('client.dashboard') }}"
                        class="btn rounded-3 btn-filter form-control-report-mobile d-flex align-items-center justify-content-center"
                        style="background:#454545;">
                            <i class="fas fa-filter me-2"></i>
                            @lang('plugins/marketplace::marketplace.fillter_none')
                        </a>
                    </div>
                    
                </form>
            </div>

            {{-- Thống kê --}}
            <div class="row py-4 g-3">
                <div class="col-6 col-md-6 col-xl-4 d-flex">
                    <div class="card border-0 rounded-md-4 text-white report-card h-100 w-100">
                        <div class="card-body d-flex align-items-center">
                            <div
                                class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center me-3 report-icon">
                                <i class="fas fa-wallet fs-4"></i>
                            </div>
                            <div>
                                <small class="fw-light">@lang('plugins/marketplace::marketplace.profit_wallet')</small>
                                <h4 class="mb-0 fw-bold fs-report-mobile js-countup" data-target="{{ (float) $totalDownline }}">0</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-6 col-xl-4 d-flex">
                    <div class="card border-0 rounded-md-4 text-white report-card h-100 w-100">
                        <div class="card-body d-flex align-items-center flex-report-mobile">
                            <div
                                class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center me-md-3 report-icon">
                                <i class="fas fa-users fs-4"></i>
                            </div>
                            <div>
                                <small class="fw-light">@lang('plugins/marketplace::marketplace.total_referrals')</small>
                                <h4 class="mb-0 fw-bold fs-report-mobile-center js-countup" data-target="{{ (float) $totalReferrals }}">0</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-6 col-xl-4 d-flex">
                    <div class="card border-0 rounded-md-4 text-white report-card h-100 w-100">
                        <div class="card-body d-flex align-items-center flex-report-mobile">
                            <div
                                class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center me-md-3 report-icon">
                                <i class="fas fa-wallet fs-4"></i>
                            </div>
                            <div>
                                <small class="fw-light">@lang('plugins/marketplace::marketplace.income')</small>
                                <h4 class="mb-0 fw-bold fs-report-mobile js-countup" data-target="{{ (float) $totalAmount }}">0</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-6 col-xl-4 d-flex">
                    <div class="card border-0 rounded-md-4 text-white report-card h-100 w-100">
                        <div class="card-body d-flex align-items-center flex-report-mobile">
                            <div
                                class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center me-md-3 report-icon">
                                <i class="fas fa-coins fs-4"></i>
                            </div>
                            <div>
                                <small class="fw-light">@lang('plugins/marketplace::marketplace.referral_commission')</small>
                                <h4 class="mb-0 fw-bold fs-report-mobile js-countup" data-target="{{ (float) $referralCommission }}">0</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-6 col-xl-4 d-flex">
                    <div class="card border-0 rounded-md-4 text-white report-card h-100 w-100">
                        <div class="card-body d-flex align-items-center flex-report-mobile">
                            <div
                                class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center me-md-3 report-icon">
                                <i class="fas fa-coins fs-4"></i>
                            </div>
                            <div>
                                <small class="fw-light">@lang('plugins/marketplace::marketplace.warehouse_referral_commission')</small>
                                <h4 class="mb-0 fw-bold fs-report-mobile js-countup" data-target="{{ (float) $wareHouseReferral }}">0</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-6 col-xl-4 d-flex">
                    <div class="card border-0 rounded-md-4 text-white report-card h-100 w-100">
                        <div class="card-body d-flex align-items-center flex-report-mobile">
                            <div
                                class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center me-md-3 report-icon">
                                <i class="fas fa-coins fs-4"></i>
                            </div>
                            <div>
                                <small class="fw-light">@lang('plugins/marketplace::marketplace.rose_split')</small>
                                <h4 class="mb-0 fw-bold fs-report-mobile js-countup" data-target="{{ (float) $rewardsByRank }}">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Biểu đồ --}}
            <div class="row pb-4">
                <div class="col-12 col-xl-6 mb-4">
                    <div class="card shadow-sm rounded-md-4">
                        <div class="card-header bg-success text-white fw-bold">
                            @lang('plugins/marketplace::marketplace.income')
                        </div>
                        <div class="card-body">
                            <canvas id="profitChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6 mb-4">
                    <div class="card shadow-sm rounded-md-4">
                        <div class="card-header bg-success text-white fw-bold">
                            @lang('plugins/marketplace::marketplace.total_profit_wallet')
                        </div>
                        <div class="card-body">
                            <canvas id="referralChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bảng danh sách --}}
            <div class="">
                <div class="table-responsive bg-white rounded-md-4 shadow-sm scrollable-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-success text-white">
                            <tr>
                                <th class="px-3 py-3">@lang('core/base::layouts.user_name')</th>


                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr class="border-bottom">
                                    <td class="px-3 py-2">{{ $customer->name }}</td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                                        {{ trans('core/base::layouts.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                            
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Phân trang --}}
            <div class="p-3 bg-white border-top">
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <script>
        const totalDownlineData = @json($totalDownline);
        const profitLabels = @json($profitLabels);
        const monthlyIncomeData = @json($monthlyIncomeData);
        const monthlyTotalDownline = @json($monthlyTotalDownline);

        const ctxProfit = document.getElementById('profitChart').getContext('2d');
        new Chart(ctxProfit, {
            type: 'line',
            data: {
                labels: profitLabels,
                datasets: [{
                    label: 'Thu nhập',
                    data: monthlyIncomeData,
                    borderColor: '#228822',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        display: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const ctxReferral = document.getElementById('referralChart').getContext('2d');
        new Chart(ctxReferral, {
            type: 'bar',
            data: {
                labels: profitLabels,
                datasets: [{
                    label: 'Chi tiêu',
                    data: monthlyTotalDownline,
                    borderColor: '#228822',
                    backgroundColor: 'rgba(33, 240, 81, 0.89)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        display: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });



    document.addEventListener('DOMContentLoaded', function () {
         const btn  = document.getElementById('filterToggle');
        const form = document.querySelector('.filter-form');
        if (!btn || !form) return; 
        btn.addEventListener('click', function () {
            form.classList.toggle('d-none');
        });
    });


    document.addEventListener('DOMContentLoaded', function () {
        const counters = document.querySelectorAll('.js-countup');

        counters.forEach(function (el) {
            const target = parseFloat(el.dataset.target) || 0;
            const duration = 500;
            const start = 0;
            const startTime = performance.now();

            function formatNumber(value) {
                // Format kiểu 1.234.567, có thể thêm " ₫" nếu muốn
                return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
            }

            function animate(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1); 

                const currentValue = Math.floor(start + (target - start) * progress);

                el.textContent = formatNumber(currentValue);

                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            }

            // Nếu target = 0 thì khỏi chạy animation cho đỡ tốn
            if (target > 0) {
                requestAnimationFrame(animate);
            } else {
                el.textContent = formatNumber(0);
            }
        });
    });
    </script>
@endsection
