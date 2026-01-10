@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="table-wrapper">
        <div id="table-configuration-wrap" class="card mb-3 table-configuration-wrap" style="display: none;">
            
        </div>

        <div class="card has-actions has-filter">
            <div class="card-header">
                <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                    
                    <div class="d-flex align-items-center gap-1">

                        <a href="" class="btn" type="button" data-bb-toggle="dt-buttons" data-bb-target=".buttons-reload"
                            tabindex="0" aria-controls="botble-marketplace-tables-withdrawal-table">
                            <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                            </svg>
                            {{ trans('core/base::layouts.reload') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-table">
                <div class="table-responsive table-has-actions table-has-filter">
                    <div id="botble-marketplace-tables-withdrawal-table_wrapper"
                        class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <table
                            class="table card-table table-vcenter table-striped table-hover dataTable no-footer dtr-inline"
                            id="botble-marketplace-tables-withdrawal-table"
                            aria-describedby="botble-marketplace-tables-withdrawal-table_info">
                            <thead>
                                <tr>
                                    <th title="ID" width="20"
                                        class="text-center no-column-visibility column-key-0 sorting sorting_desc"
                                        tabindex="0" aria-controls="botble-marketplace-tables-withdrawal-table"
                                        rowspan="1" colspan="1" style="width: 20px;" aria-sort="descending"
                                        aria-label="IDorderby asc">ID
                                    </th>
                                    <th title="Vendor" class="text-start column-key-1 sorting" tabindex="0"
                                        aria-controls="botble-marketplace-tables-withdrawal-table" rowspan="1"
                                        colspan="1" aria-label="Vendororderby asc">{{ trans('core/base::layouts.customer') }}
                                    </th>
                                    <th title="Amount" class="column-key-2 sorting" tabindex="0"
                                        aria-controls="botble-marketplace-tables-withdrawal-table" rowspan="1"
                                        colspan="1" aria-label="Amountorderby asc">{{ trans('core/base::layouts.rank_on_profit') }}
                                    </th>
                                    <th title="Fee" class="column-key-3 sorting" tabindex="0"
                                        aria-controls="botble-marketplace-tables-withdrawal-table" rowspan="1"
                                        colspan="1" aria-label="Feeorderby asc">{{ trans('core/base::layouts.current_rank') }}
                                    </th>
                                    <th title="Created At" class="column-key-4 sorting" tabindex="0"
                                        aria-controls="botble-marketplace-tables-withdrawal-table" rowspan="1"
                                        colspan="1" aria-label="Created Atorderby asc">{{ trans('core/base::layouts.date') }}
                                    </th>
                                    <th title="Created At" class="column-key-4 sorting" tabindex="0"
                                        aria-controls="botble-marketplace-tables-withdrawal-table" rowspan="1"
                                        colspan="1" aria-label="Created Atorderby asc">{{ trans('core/base::layouts.amount') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rewardHistories as $rewardHistory)
                                <tr class="odd">
                                    <td class="text-center no-column-visibility column-key-0 sorting_1">{{$rewardHistory->id}}</td>
                                    <td class="  text-start  column-key-1">
                                        {{ ($rewardHistory->customer)->name ?? '[Không có khách]' }}
                                    </td>
                                    <td class="column-key-2">{{$rewardHistory->rank_name}}</td>
                                    <td class="column-key-3">{{$rewardHistory->rank->rank_name}}</td>
                                    <td class="column-key-4">{{ date_format($rewardHistory->created_at, 'H:i d-m-Y') }}</td>
                                    <td class="column-key-5"><strong>{{format_price($rewardHistory->reward)}}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <div class="dataTables_length" id="botble-marketplace-tables-withdrawal-table_length">
                                    <label><span class="dt-length-style"><select
                                                name="botble-marketplace-tables-withdrawal-table_length"
                                                aria-controls="botble-marketplace-tables-withdrawal-table"
                                                class="form-select form-select-sm">
                                                <option value="10">10</option>
                                                <option value="30">30</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                                <option value="500">500</option>
                                                <option value="-1">All</option>
                                            </select></span></label></div>
                                <div class="m-0 text-muted">
                                    <div class="dataTables_info" id="botble-marketplace-tables-withdrawal-table_info"
                                        role="status" aria-live="polite"><span class="dt-length-records">
                                            <svg class="icon  svg-icon-ti-ti-world" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                                <path d="M3.6 9h16.8"></path>
                                                <path d="M3.6 15h16.8"></path>
                                                <path d="M11.5 3a17 17 0 0 0 0 18"></path>
                                                <path d="M12.5 3a17 17 0 0 1 0 18"></path>
                                            </svg> <span class="d-none d-sm-inline">{{trans('core/base::layouts.show_from')}}</span>
                                            {{ $rewardHistories->firstItem() }}
                                            {{trans('core/base::layouts.to')}} 
                                            {{ $rewardHistories->lastItem() }} 
                                            {{trans('core/base::layouts.in')}}
                                            <span class="badge bg-secondary text-secondary-fg">
                                            {{ $rewardHistories->total() }}
                                            </span>
                                            <span class="hidden-xs">{{trans('core/base::layouts.records')}}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <div class="dataTables_paginate paging_simple_numbers"
                                    id="botble-marketplace-tables-withdrawal-table_paginate">
                                    {{ $rewardHistories->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection