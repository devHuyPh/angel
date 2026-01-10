@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('title', trans('Report'))

@push('header-action')
    <form method="post" action="{{ route('report.days') }}" class="d-flex align-items-center gap-2">
        @csrf
        <label class="me-1">{{ trans('core/base::layouts.from') }}</label>
        <input type="date" class="form-control w-auto" name="from_date" id="from-date" value="{{ $from_date }}">
        <label>-</label>
        <label class="mx-1">{{ trans('core/base::layouts.to') }}</label>
        <input type="date" class="form-control w-auto" name="to_date" id="to-date" value="{{ $to_date }}">
        <button type="submit" id="submit-search-reprot" class="btn btn-success me-6"><i class="fas fa-filter"></i> Lọc</button>
    </form>
@endpush

@section('content')
    <div>
            {{-- //startduong --}}
            @include('report.head_report')

            {{-- //endduong --}}
        <div class="row mb-4" >
            <div class="areaChart col-lg-7 col-md-12 mb-4" 
                id="areaChart">
            </div>
            <div class="donutChart col-lg-5 col-md-12 mb-4" 
                id="donutChart">
            </div>
        </div>
    
        <div class="table-wrapper">
            <div class="card has-actions has-filter">
                <div class="card-header">
                    <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                        <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-1">
                            <div class="table-search-input">
                                <input type="search" class="form-control input-sm" placeholder="Tìm kiếm..." style="min-width: 120px">
                            </div>
                            <a class="btn buttons-collection action-item btn-primary ms-3" href="{{ route('export.index') }}">
                                <i class="fas fa-file-export"></i>
                                {{ trans('core/base::layouts.export') }}
                            </a>
                        </div>
                    </div>
                </div>
    
                <div class="card-table">
                    <div class="table-responsive table-has-actions table-has-filter">
                        <table class="table card-table table-vcenter table-striped table-hover" id="botble-ecommerce-tables-product-table">
                            <thead>
                                <tr>
                                    {{-- <th title="Hộp kiểm"><input class="form-check-input m-0 align-middle table-check-all" data-set=".dataTable .checkboxes" type="checkbox"></th> --}}
                                    <th title="Hình ảnh" width="50" class="column-key-1">{{ trans('core/base::layouts.referral_id') }}</th>
                                    <th title="Sản phẩm" class="text-start column-key-2">{{ trans('core/base::layouts.name') }}</th>
                                    {{-- <th title="Giá cơ bản" class="text-start column-key-3">{{ trans('core/base::layouts.phone') }}</th> --}}
                                    {{-- <th title="Tình trạng tồn kho" class="column-key-4">Email</th> --}}
                                    <th title="Số lượng" class="text-start column-key-5">{{ trans('core/base::layouts.rank') }}</th>
                                    <th title="Mã sản phẩm" class="text-start column-key-6">{{ trans('core/base::layouts.total_downline') }}</th>
                                    <th title="Thứ tự sắp xếp" width="50" class="column-key-7">{{ trans('core/base::layouts.wallet_1') }}</th>
                                    {{-- <th title="Ngày tạo" width="100" class="column-key-8">{{ trans('core/base::layouts.wallet_2') }}</th> --}}
                                    <th title="Trạng thái" width="100" class="text-center column-key-9">{{ trans('core/base::layouts.total_downline_month') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    <tr>
                                        {{-- <td><input class="form-check-input m-0 align-middle table-check-all" data-set=".dataTable .checkboxes" type="checkbox"></td> --}}
                                        <td class="column-key-1" width="50">{{ $customer['referral_ids'] }}</td>
                                        <td class="text-start column-key-2">{{ $customer['name'] }}</td>
                                        {{-- <td class="text-start column-key-3">{{ $customer['phone'] }}</td> --}}
                                        {{-- <td class="column-key-4">{{ $customer['email'] }}</td> --}}
                                        <td class="text-start column-key-5">{{ $customer->rank->rank_name ?? 'Chưa có hạng' }}</td>
                                        <td class="text-start column-key-6">{{ format_price($customer['total_dowline']) }}</td>
                                        <td class="column-key-7" width="50">{{ format_price($customer['walet_1']) }}</td>
                                        {{-- <td class="column-key-8" width="100">{{ $customer['walet_2'] }}</td> --}}
                                        <td class="text-center column-key-9" width="100">{{ format_price($customer['total_dowline_month']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @php
                        $start = !request()->query('page') || request()->query('page') == 1 ? 1 : (request()->query('page') - 1) * $paginate + 1;
                        $end = !request()->query('page') ? $paginate : (request()->query('page') * $paginate < count($totals) ? request()->query('page') * $paginate : count($totals));
                    @endphp
                    <div class="my-3 mx-3 d-flex justify-content-between align-items-center">
                        <div class="text-secondary d-flex align-items-center gap-2">
                            <i class="fas fa-globe"></i>
                            <span>Hiển thị từ {{ $start }} đến {{ $end }} trong tổng số</span>
                            <span class="bg-secondary text-white px-2 py-1 rounded fw-bold d-flex align-items-center justify-content-center" style="min-width: 32px;">
                                {{ count($totals) }}
                            </span>
                            <span>bản ghi</span>
                        </div>
                        <div class="pagination-container">
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var recipients = @json($recipients);
        var amount_company = parseInt(@json($amount_company));
        var amount_user = parseInt(@json($amount_user));
        var days = @json($days);
        var total_amounts = @json($total_amounts);

        var options = {
            chart: { type: 'area', height: 350 },
            series: [{ name: 'Hoa Hồng Công Ty', data: total_amounts }],
            xaxis: { categories: days },
            dataLabels: { enabled: true },
            colors: ['#22863A']
        };

        var options2 = {
            chart: { type: 'donut', height: 350 },
            series: [amount_user, amount_company],
            labels: ['Hoa Hồng Người Dùng', 'Hoa Hồng Công Ty'],
            colors: ['#066FD1', '#22863A'],
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: { show: true },
                            value: { show: true },
                            total: {
                                show: true,
                                label: 'Tổng Hoa Hồng',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                },
                            },
                        },
                    },
                },
            },
        };

        var areaChart = new ApexCharts(document.querySelector('#areaChart'), options);
        var donutChart = new ApexCharts(document.querySelector('#donutChart'), options2);
        areaChart.render();
        donutChart.render();
    </script>
@endpush

<style>
    .areaChart, .donutChart {
        min-height: 350px; 
    }

    .pagination-container {
        display: flex;
        justify-content: flex-end;
    }

    .pagination {
        display: flex;
        gap: 5px;
        padding: 0;
        list-style: none;
        margin: 0;
    }

    .pagination .page-item .page-link {
        padding: 6px 12px;
        border-radius: 4px;
        color: #333;
        text-decoration: none;
        border: 1px solid #dee2e6;
        background-color: white;
        transition: all 0.2s ease;
    }

    .pagination .page-item.active .page-link {
        background-color: #22863a;
        color: white;
        border-color: #22863a;
    }

    .pagination .page-item .page-link:hover:not(.active) {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        cursor: not-allowed;
        background-color: #fff;
    }

    tbody th {
        font-weight: normal;
        font-size: 14px;
    }
</style>