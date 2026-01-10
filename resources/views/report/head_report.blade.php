<div id="report-stats-content" class="mb-5">
    <div class="row row-cards">
        <div class="widget-item col-md-6">
            <div class="h-100 position-relative">
                <div class="card analytic-card">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <svg
                                    class="icon icon-md text-white bg-azure rounded p-1 svg-icon-ti-ti-database"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0"></path>
                                    <path d="M4 6v6a8 3 0 0 0 16 0v-6"></path>
                                    <path d="M4 12v6a8 3 0 0 0 16 0v-6"></path>
                                </svg>
                            </div>
                            <div class="col mt-0">
                                <p class="text-secondary mb-0 fs-4"> {{trans('core/base::layouts.total_downline')}} </p>
                                <h3 class="mb-n1 fs-1"> {{format_price($dataCaculatorProfit['total_downline_revenue'])}} </h3>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 pb-4"><span class="text-success">tăng <svg class="icon svg-icon-ti-ti-trending-up"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 17l6 -6l4 4l8 -8"></path>
                                <path d="M14 7l7 0l0 7"></path>
                            </svg></span></div>
                </div>
                <div class="position-absolute fixed-bottom" id="new-product-card-widget"
                    style="z-index: 1; min-height: 20px;">
                </div>
            </div>
        </div>

        <div class="widget-item col-md-6">
            <div class="h-100 position-relative">
                <div class="card analytic-card">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <svg
                                    class="icon icon-md text-white bg-azure rounded p-1 svg-icon-ti-ti-database"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0"></path>
                                    <path d="M4 6v6a8 3 0 0 0 16 0v-6"></path>
                                    <path d="M4 12v6a8 3 0 0 0 16 0v-6"></path>
                                </svg>
                            </div>
                            <div class="col mt-0">
                                <p class="text-secondary mb-0 fs-4"> Lợi nhuận khi trừ phí vận hành </p>
                                <h3 class="mb-n1 fs-1"> {{ $dataCaculatorProfit['profit_label'] . format_price($dataCaculatorProfit['net_profit'])}} </h3>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 pb-4"><span class="text-success"> {{$dataCaculatorProfit['percentProfit']}}% tăng <svg class="icon svg-icon-ti-ti-trending-up"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 17l6 -6l4 4l8 -8"></path>
                                <path d="M14 7l7 0l0 7"></path>
                            </svg></span></div>
                </div>
                <div class="position-absolute fixed-bottom" id="new-product-card-widget"
                    style="z-index: 1; min-height: 20px;">
                </div>
            </div>
        </div>
        @foreach ($dataCaculatorProfit['expenses'] as $key  => $value)
        {{-- @dd($key) --}}
        <div class="widget-item col-md-3">
            <div class="h-100 position-relative">
                <div class="card analytic-card">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <svg
                                    class="icon icon-md text-white bg-azure rounded p-1 svg-icon-ti-ti-database"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0"></path>
                                    <path d="M4 6v6a8 3 0 0 0 16 0v-6"></path>
                                    <path d="M4 12v6a8 3 0 0 0 16 0v-6"></path>
                                </svg>
                            </div>
                            <div class="col mt-0">
                                <p class="text-secondary mb-0 fs-4"> {{ trans('core/base::layouts.'.$key)}} </p>
                                <h3 class="mb-n1 fs-1"> {{format_price($value)}} </h3>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 pb-4"><span class="text-success"> tăng <svg class="icon svg-icon-ti-ti-trending-up"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 17l6 -6l4 4l8 -8"></path>
                                <path d="M14 7l7 0l0 7"></path>
                            </svg></span></div>
                </div>
                <div class="position-absolute fixed-bottom" id="new-product-card-widget"
                    style="z-index: 1; min-height: 20px;">
                </div>
            </div>
        </div>
        @endforeach

        {{-- <div class="widget-item col-md-6">
            <div class="h-100 position-relative">
                <div class="card analytic-card">
                    <div class="card-body p-3">
                        <div class="table-wrapper">
                            <div class="card has-actions has-filter">
                                <div class="card-header">
                                    <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                                        <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-1">
                                            <div class="table-search-input">
                                                <input type="search" class="form-control input-sm" placeholder="Tìm kiếm..." style="min-width: 120px">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    
                                <div class="card-table">
                                    <div class="table-responsive table-has-actions table-has-filter">
                                        <table class="table card-table table-vcenter table-striped table-hover" id="botble-ecommerce-tables-product-table">
                                            <thead>
                                                <tr>
                                                    <th title="ID" width="20" class="text-center no-column-visibility column-key-0">ID</th>
                                                    <th title="Hình ảnh" width="50" class="column-key-1">{{ trans('core/base::layouts.rank') }}</th>
                                                    <th title="Sản phẩm" class="text-start column-key-2">{{ trans('core/base::layouts.name') }}</th>
                                                    <th title="Số lượng" class="text-start column-key-3">{{ trans('core/base::layouts.reward_sharing') }}</th>
                                                    <th title="Mã sản phẩm" class="text-start column-key-4">{{ trans('core/base::layouts.created_at') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataCaculatorProfit['dataRewardSharings'] as $dataRewardSharing)
                                                    <tr>
                                                        <td class="text-center no-column-visibility column-key-0" width="20">{{ $dataRewardSharing->id }}</td>
                                                        <td class="column-key-1" width="50">{{ $dataRewardSharing->rank_name }}</td>
                                                        <td class="text-start column-key-2">{{ $dataRewardSharing->customer->name }}</td>
                                                        <td class="text-start column-key-3">{{ $dataRewardSharing->reward }}</td>
                                                        <td class="column-key-4" width="100">{{ $dataRewardSharing->created_at }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="my-3 mx-3 d-flex justify-content-between align-items-center">
                                        <div class="text-secondary d-flex align-items-center gap-2">
                                            <i class="fas fa-globe"></i>
                                            <span>Hiển thị từ {{$dataCaculatorProfit['dataRewardSharings']->firstItem()}} đến {{$dataCaculatorProfit['dataRewardSharings']->lastItem()}} trong tổng số</span>
                                            <span class="bg-secondary text-white px-2 py-1 rounded fw-bold d-flex align-items-center justify-content-center" style="min-width: 32px;">
                                                {{ $dataCaculatorProfit['dataRewardSharings']->total() }}
                                            </span>
                                            <span>bản ghi</span>
                                        </div>
                                        <div class="pagination-container">
                                            {{ $dataCaculatorProfit['dataRewardSharings']->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="widget-item col-md-6">
            <div class="h-100 position-relative">
                <div class="card analytic-card">
                    <div class="card-body p-3">
                        12333
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>