@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="table-wrapper">
        <div id="table-configuration-wrap" class="card mb-3 table-configuration-wrap" style="display: none;">
            <!--<div class="card-body">-->
            <!--    <div class="d-flex justify-content-between align-items-center">-->
            <!--        <p class="d-flex align-items-center mb-0">Filters</p>-->
            <!--        <button id="cancel-table-configuration-wrap" class="btn btn-icon btn-sm btn-show-table-options rounded-pill" type="button">-->
            <!--            <svg class="icon icon-sm icon-left svg-icon-ti-ti-x" xmlns="http://www.w3.org/2000/svg" width="24"-->
            <!--                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"-->
            <!--                stroke-linecap="round" stroke-linejoin="round">-->
            <!--                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>-->
            <!--                <path d="M18 6l-12 12"></path>-->
            <!--                <path d="M6 6l12 12"></path>-->
            <!--            </svg>-->
            <!--        </button>-->
            <!--    </div>-->
            <!--    <div class="wrapper-filter">-->

            <!--        <input type="hidden" class="filter-data-url" value="https://shofy.botble.com/admin/tables/filters">-->

            <!--        <div class="sample-filter-item-wrap hidden">-->
            <!--            <div class="row filter-item form-filter">-->
            <!--                <div class="col-auto w-50 w-sm-auto">-->
            <!--                    <div class="mb-3 position-relative">-->
            <!--                        <select class="form-select filter-column-key" name="filter_columns[]"-->
            <!--                            id="filter_columns[]">-->
            <!--                            <option value="status">Status</option>-->
            <!--                        </select>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                <div class="col-auto w-50 w-sm-auto">-->
            <!--                    <div class="mb-3 position-relative">-->
            <!--                        <select class="form-select filter-operator filter-column-operator"-->
            <!--                            name="filter_operators[]" id="filter_operators[]">-->
            <!--                            <option value="like">Contains</option>-->
            <!--                            <option value="=">Is equal to</option>-->
            <!--                            <option value=">">Greater than</option>-->
            <!--                            <option value="<">Less than</option>-->
            <!--                        </select>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                <div class="col-auto w-100 w-sm-25">-->
            <!--                    <span class="filter-column-value-wrap">-->
            <!--                        <input class="form-control filter-column-value" type="text" placeholder="Value"-->
            <!--                            name="filter_values[]">-->
            <!--                    </span>-->
            <!--                </div>-->

            <!--                <div class="col">-->
            <!--                    <button class="btn btn-icon   btn-remove-filter-item mb-3 text-danger" type="button"-->
            <!--                        data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete"-->
            <!--                        data-bs-original-title="Delete">-->
            <!--                        <svg class="icon icon-left svg-icon-ti-ti-trash" xmlns="http://www.w3.org/2000/svg"-->
            <!--                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"-->
            <!--                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">-->
            <!--                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>-->
            <!--                            <path d="M4 7l16 0"></path>-->
            <!--                            <path d="M10 11l0 6"></path>-->
            <!--                            <path d="M14 11l0 6"></path>-->
            <!--                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>-->
            <!--                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>-->
            <!--                        </svg>-->

            <!--                    </button>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->

            <!--        <form method="GET" action="https://shofy.botble.com/admin/marketplaces/withdrawals"-->
            <!--            accept-charset="UTF-8" class="filter-form">-->
            <!--            <input type="hidden" name="filter_table_id" class="filter-data-table-id"-->
            <!--                value="botble-marketplace-tables-withdrawal-table">-->
            <!--            <input type="hidden" name="class" class="filter-data-class"-->
            <!--                value="Botble\Marketplace\Tables\WithdrawalTable">-->
            <!--            <div class="filter_list inline-block filter-items-wrap">-->
            <!--                <div class="row filter-item form-filter filter-item-default">-->
            <!--                    <div class="col-auto w-50 w-sm-auto">-->
            <!--                        <div class="mb-3 position-relative">-->
            <!--                            <select class="form-select filter-column-key" name="filter_columns[]"-->
            <!--                                id="filter_columns[]">-->
            <!--                                <option value="" selected="">Select field</option>-->
            <!--                                <option value="status">Status</option>-->
            <!--                            </select>-->
            <!--                        </div>-->
            <!--                    </div>-->

            <!--                    <div class="col-auto w-50 w-sm-auto">-->
            <!--                        <div class="mb-3 position-relative">-->
            <!--                            <select class="form-select filter-operator filter-column-operator"-->
            <!--                                name="filter_operators[]" id="filter_operators[]">-->
            <!--                                <option value="like">Contains</option>-->
            <!--                                <option value="=" selected="">Is equal to</option>-->
            <!--                                <option value=">">Greater than</option>-->
            <!--                                <option value="<">Less than</option>-->
            <!--                            </select>-->
            <!--                        </div>-->
            <!--                    </div>-->

            <!--                    <div class="col-auto w-100 w-sm-25">-->
            <!--                        <div class="filter-column-value-wrap mb-3">-->
            <!--                            <input class="form-control filter-column-value" type="text" placeholder="Value"-->
            <!--                                name="filter_values[]" value="">-->
            <!--                        </div>-->
            <!--                    </div>-->

            <!--                    <div class="col">-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--            <div class="btn-list">-->
            <!--                <button class="btn   add-more-filter" type="button">-->

            <!--                    Add additional filter-->

            <!--                </button>-->
            <!--                <button class="btn btn-primary  btn-apply" type="submit">-->

            <!--                    Apply-->

            <!--                </button>-->
            <!--                <a class="btn btn-icon" style="display: none;" type="button"-->
            <!--                    href="https://shofy.botble.com/admin/marketplaces/withdrawals"-->
            <!--                    data-bb-toggle="datatable-reset-filter">-->
            <!--                    <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg"-->
            <!--                        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"-->
            <!--                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">-->
            <!--                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>-->
            <!--                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>-->
            <!--                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>-->
            <!--                    </svg>-->

            <!--                </a>-->
            <!--            </div>-->
            <!--        </form>-->
            <!--    </div>-->
            <!--</div>-->
        </div>

        <div class="card has-actions has-filter">
            <div class="card-header">
                <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                    <!--<div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-1">-->
                    <!--    <div class="dropdown d-inline-block">-->
                    <!--        <button class="btn   dropdown-toggle" type="button" data-bs-toggle="dropdown">-->

                    <!--            Bulk Actions-->

                    <!--        </button>-->

                    <!--        <div class="dropdown-menu">-->
                    <!--            <div class="dropdown-submenu">-->
                    <!--                <button class="dropdown-item">-->

                    <!--                    Bulk changes-->

                    <!--                    <svg class="icon dropdown-item-icon ms-auto me-0 svg-icon-ti-ti-chevron-right"-->
                    <!--                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"-->
                    <!--                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"-->
                    <!--                        stroke-linecap="round" stroke-linejoin="round">-->
                    <!--                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>-->
                    <!--                        <path d="M9 6l6 6l-6 6"></path>-->
                    <!--                    </svg> </button>-->
                    <!--                <div class="dropdown-menu">-->
                    <!--                    <button class="dropdown-item bulk-change-item" data-key="status"-->
                    <!--                        data-class-item="Botble\Marketplace\Tables\WithdrawalTable"-->
                    <!--                        data-save-url="https://shofy.botble.com/admin/tables/bulk-changes/save">-->

                    <!--                        Status-->

                    <!--                    </button>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->

                    <!--    <button id="btn-show-table-options" class="btn btn-show-table-options" type="button">-->
                    <!--        Filters-->
                    <!--    </button>-->

                    <!--    <div class="table-search-input">-->
                    <!--        <label>-->
                    <!--            <input type="search" class="form-control input-sm" placeholder="Search..."-->
                    <!--                style="min-width: 120px">-->
                    <!--        </label>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <div class="d-flex align-items-center gap-1">

                        <a href="" class="btn" type="button" data-bb-toggle="dt-buttons"
                            data-bb-target=".buttons-reload" tabindex="0"
                            aria-controls="botble-marketplace-tables-withdrawal-table">
                            <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                            </svg>
                            Reload

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
                                        colspan="1" aria-label="Vendororderby asc">
                                        {{ trans('core/base::layouts.warehouse') }}
                                    </th>
                                    <th title="Amount" class="column-key-2 sorting" tabindex="0"
                                        aria-controls="botble-marketplace-tables-withdrawal-table" rowspan="1"
                                        colspan="1" aria-label="Amountorderby asc">
                                        {{ trans('core/base::layouts.user-confirm') }}
                                    </th>
                                    <th title="Fee" class="column-key-3 sorting" tabindex="0"
                                        aria-controls="botble-marketplace-tables-withdrawal-table" rowspan="1"
                                        colspan="1" aria-label="Feeorderby asc">
                                        {{ trans('core/base::layouts.order-code') }}
                                    </th>
                                    <th title="Created At" width="100" class="column-key-4 sorting" tabindex="0"
                                        aria-controls="botble-marketplace-tables-withdrawal-table" rowspan="1"
                                        colspan="1" style="width: 100px;" aria-label="Created Atorderby asc">
                                        {{ trans('core/base::layouts.ship-completed-date') }}
                                    </th>
                                    <th title="Status" width="100" class="text-center column-key-5 sorting"
                                        tabindex="0" aria-controls="botble-marketplace-tables-withdrawal-table"
                                        rowspan="1" colspan="1" style="width: 100px;"
                                        aria-label="Statusorderby asc">{{ trans('core/base::layouts.status') }}
                                    </th>
                                    <th title="Status" width="100" class="text-center column-key-6 sorting"
                                        tabindex="0" aria-controls="botble-marketplace-tables-withdrawal-table"
                                        rowspan="1" colspan="1" style="width: 100px;"
                                        aria-label="Statusorderby asc">{{ trans('core/base::layouts.shipping-fee') }}
                                    </th>
                                    <th title="Operations"
                                        class="text-center no-column-visibility text-nowrap sorting_disabled"
                                        rowspan="1" colspan="1" aria-label="Operations">
                                        {{ trans('core/base::layouts.operations') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @dd($confirmVendorShipedToUsers) --}}
                                @foreach ($confirmVendorShipedToUsers as $confirmVendorShipedToUser)
                                    <tr class="odd">
                                        <td class="text-center no-column-visibility column-key-0 sorting_1">
                                            {{ $confirmVendorShipedToUser->id }}</td>
                                        <td class="  text-start  column-key-1">
                                            <a href="customers/edit/8">
                                                {{ $confirmVendorShipedToUser->customer->name ?? '[Không có khách]' }}
                                            </a>
                                        </td>
                                        <td class="   column-key-2">
                                            {{ $confirmVendorShipedToUser->admin->name ?? '[Không có]' }}</td>
                                        <td class="   column-key-3">
                                            {{ $confirmVendorShipedToUser->order->code }}
                                        </td>
                                        <td class="  text-nowrap column-key-4">
                                            {{ date_format($confirmVendorShipedToUser->created_at, 'H:i d-m-Y') }}</td>
                                        @php
                                            $statusLabels = [
                                                '0' => 'core/base::layouts.pending',
                                                '1' => 'core/base::layouts.approved',
                                                '2' => 'core/base::layouts.rejected',
                                            ];

                                            $statusClasses = [
                                                '0' => 'bg-warning text-warning-fg',
                                                '1' => 'bg-success text-success-fg',
                                                '2' => 'bg-danger text-danger-fg',
                                            ];
                                        @endphp

                                        <td class="text-center column-key-5">
                                            <span
                                                class="badge {{ $statusClasses[$confirmVendorShipedToUser->status] ?? 'bg-secondary text-secondary-fg' }}">
                                                {{ trans($statusLabels[$confirmVendorShipedToUser->status] ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        <td class="  text-center  column-key-6">
                                            {{ format_price($confirmVendorShipedToUser->shipping_fee) }}
                                        </td>
                                        <td class="  text-center no-column-visibility text-nowrap">
                                            <div class="table-actions">
                                                <a href="
                                                {{ route('store-to-user.edit', $confirmVendorShipedToUser->id) }}
                                                 "
                                                    class="btn btn-sm btn-icon btn-primary">
                                                    <svg class="icon  svg-icon-ti-ti-edit" data-bs-toggle="tooltip"
                                                        data-bs-title="Edit" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path
                                                            d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1">
                                                        </path>
                                                        <path
                                                            d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z">
                                                        </path>
                                                        <path d="M16 5l3 3"></path>
                                                    </svg>
                                                    <span class="sr-only">{{ trans('core/base::layouts.edit') }}</span>
                                                </a>

                                                <button class="btn btn-sm btn-icon btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteCustomerWithdrawal_{{ $confirmVendorShipedToUser->id }}">
                                                    <svg class="icon  svg-icon-ti-ti-trash" data-bs-toggle="tooltip"
                                                        data-bs-title="Delete" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M4 7l16 0"></path>
                                                        <path d="M10 11l0 6"></path>
                                                        <path d="M14 11l0 6"></path>
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                    </svg>
                                                    <span class="sr-only">{{ trans('core/base::layouts.delete') }}</span>
                                                </button>
                                                {{-- @include('admin.withdrawals_manager.delete') --}}

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div
                            class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
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
                                            </select></span></label>
                                </div>
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
                                            </svg>
                                            <span
                                                class="d-none d-sm-inline">{{ trans('core/base::layouts.show_from') }}</span>
                                            {{ $confirmVendorShipedToUsers->firstItem() }}
                                            {{ trans('core/base::layouts.to') }}
                                            {{ $confirmVendorShipedToUsers->lastItem() }}
                                            {{ trans('core/base::layouts.in') }}
                                            <span class="badge bg-secondary text-secondary-fg">
                                                {{ $confirmVendorShipedToUsers->total() }}
                                            </span>
                                            <span class="hidden-xs">{{ trans('core/base::layouts.records') }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <div class="dataTables_paginate paging_simple_numbers"
                                    id="botble-marketplace-tables-withdrawal-table_paginate">
                                    {{ $confirmVendorShipedToUsers->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#btn-show-table-options").click(function() {
                $("#table-configuration-wrap").slideToggle(300);
            });

            $("#cancel-table-configuration-wrap").click(function() {
                $("#table-configuration-wrap").slideUp(300);
            });
        });
    </script>
@endpush
