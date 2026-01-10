@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
    <form method="GET" action="{{ route('marketplace.vendor.store-manager.index') }}" id="store-filter-form">
        <div class="table-wrapper">
            <div class="card">
                <div class="card-header">
                    <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                        <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-1">
                            <div class="table-search-input">
                                <label>
                                    <input type="search" name="name" value="{{ request('name') }}"
                                        class="form-control auto-submit" placeholder="Tìm kiếm..."
                                        style="min-width: 120px" />
                                </label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <select name="store_level_id" class="form-select auto-submit">
                                @if ($myStore->store_level_id > 2)
                                    <option value="2" {{ request('store_level_id') == 2 ? 'selected' : '' }}>Kho cấp 2
                                    </option>
                                @endif
                                @if ($myStore->store_level_id > 1)
                                    <option value="1" {{ request('store_level_id') == 1 ? 'selected' : '' }}>Kho cấp 1
                                    </option>
                                @endif
                            </select>

                            <button class="btn" type="button" data-bb-toggle="dt-buttons"
                                data-bb-target=".buttons-reload" tabindex="0"
                                aria-controls="botble-marketplace-tables-order-table">
                                <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                </svg>
                                Tải lại
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-table">
                    <div class="table-responsive">
                        <div id="botble-marketplace-tables-order-table_wrapper"
                            class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div id="botble-marketplace-tables-order-table_processing" class="dataTables_processing card"
                                role="status" style="display: none">
                                <div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                            <table
                                class="table card-table table-vcenter table-striped table-hover dataTable no-footer dtr-inline collapsed"
                                id="botble-marketplace-tables-order-table"
                                aria-describedby="botble-marketplace-tables-order-table_info">
                                <thead>
                                    <tr>
                                        <th title="ID" width="20"
                                            class="text-center no-column-visibility column-key-0 sorting sorting_desc"
                                            tabindex="0" aria-controls="botble-marketplace-tables-order-table"
                                            rowspan="1" colspan="1" style="width: 20px" aria-sort="descending"
                                            aria-label="IDorderby asc">
                                            ID
                                        </th>
                                        <th title="Email" class="text-start column-key-1 sorting_disabled" rowspan="1"
                                            colspan="1" aria-label="Email">
                                            Thông tin
                                        </th>
                                        <th title="Tổng số" class="column-key-2 sorting" tabindex="0"
                                            aria-controls="botble-marketplace-tables-order-table" rowspan="1"
                                            colspan="1" aria-label="Tổng sốorderby asc">
                                            Doanh thu
                                        </th>
                                        <th title="Số tiền thuế" class="column-key-3 sorting" tabindex="0"
                                            aria-controls="botble-marketplace-tables-order-table" rowspan="1"
                                            colspan="1" aria-label="Số tiền thuếorderby asc">
                                            Sản phẩm
                                        </th>
                                        <th title="Phí vận chuyển" class="column-key-4 sorting" tabindex="0"
                                            aria-controls="botble-marketplace-tables-order-table" rowspan="1"
                                            colspan="1" aria-label="Phí vận chuyểnorderby asc">
                                            Đã giao
                                        </th>
                                        <th title="Phương thức thanh toán" class="text-start column-key-5 sorting"
                                            tabindex="0" aria-controls="botble-marketplace-tables-order-table"
                                            rowspan="1" colspan="1" aria-label="Phương thức thanh toánorderby asc">
                                            Địa chỉ
                                        </th>
                                        {{-- <th
                    title="Trạng thái thanh toán"
                    class="column-key-6 sorting"
                    tabindex="0"
                    aria-controls="botble-marketplace-tables-order-table"
                    rowspan="1"
                    colspan="1"
                    aria-label="Trạng thái thanh toánorderby asc"
                    >
                    Trạng thái
                    </th> --}}

                                        <th title="Tác vụ"
                                            class="text-center no-column-visibility text-nowrap sorting_disabled"
                                            rowspan="1" colspan="1" aria-label="Tác vụ">
                                            Tác vụ
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stores as $store)
                                        <tr class="odd">
                                            <td class="text-center no-column-visibility column-key-0 sorting_1 dtr-control"
                                                style="">
                                                {{ $store->id }}
                                            </td>
                                            <td class="text-start column-key-1">
                                                {{ $store->name }} <br />
                                                {{ $store->email }} <br />
                                                {{ $store->phone }}
                                            </td>
                                            @php
                                                $orders = $store->orders;
                                                $total = 0;
                                                foreach ($orders as $order) {
                                                    $total += $order->payment->where('status', 'completed')->amount;
                                                }
                                            @endphp
                                            <td class="column-key-2">{{ format_price($total) }}</td>
                                            <td class="column-key-3">{{ $store->products->count() }}</td>
                                            <td class="column-key-4">
                                                {{ $store->orders->where('status', 'completed')->count() }}</td>
                                            <td class="text-start column-key-5">
                                                {{ $store->full_address }}
                                            </td>
                                            <td class="text-center no-column-visibility text-nowrap">
                                                <div class="table-actions">
                                                    <a href="{{ route('marketplace.vendor.store-manager.show', $store->id) }}"
                                                        class="btn btn-sm btn-icon btn-primary">
                                                        <x-core::icon name="ti ti-eye" data-bs-title="Xem" />
                                                        <span class="sr-only">Xem</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2"
                                style="">
                                <div class="d-flex justify-content-between align-items-center gap-3">
                                    <div class="dataTables_length" id="botble-marketplace-tables-order-table_length"
                                        style="">
                                        <label>
                                            <span class="dt-length-style">
                                                <select name="per_page"
                                                    aria-controls="botble-marketplace-tables-order-table"
                                                    class="form-select form-select-sm auto-submit">
                                                    <option value="10"
                                                        {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                                    <option value="30"
                                                        {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                                                    <option value="50"
                                                        {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                                    <option value="100"
                                                        {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                                    <option value="500"
                                                        {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                                                    <option value="-1"
                                                        {{ request('per_page') == -1 ? 'selected' : '' }}>Tất cả
                                                    </option>
                                                </select>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="m-0 text-muted">
                                        <div class="dataTables_info" id="botble-marketplace-tables-order-table_info"
                                            role="status" aria-live="polite">
                                            <span class="dt-length-records">
                                                <svg class="icon svg-icon-ti-ti-world" xmlns="http://www.w3.org/2000/svg"
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
                                                <span class="d-none d-sm-inline">Hiển thị từ</span>
                                                {{ $stores->firstItem() }} đến {{ $stores->lastItem() }} trong tổng số
                                                <span class="badge bg-secondary text-secondary-fg">
                                                    {{ $stores->total() }}
                                                </span>
                                                <span class="hidden-xs">bản ghi</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <div class="dataTables_paginate paging_simple_numbers"
                                        id="botble-marketplace-tables-order-table_paginate" style="">
                                        {{-- <ul class="pagination">
                    <li
                      class="paginate_button page-item previous disabled"
                      id="botble-marketplace-tables-order-table_previous"
                    >
                      <a
                        aria-controls="botble-marketplace-tables-order-table"
                        aria-disabled="true"
                        aria-label="&amp;laquo; Trang sau"
                        role="link"
                        data-dt-idx="previous"
                        tabindex="-1"
                        class="page-link"
                        >« Trang sau</a
                      >
                    </li>
                    <li class="paginate_button page-item active">
                      <a
                        href="#"
                        aria-controls="botble-marketplace-tables-order-table"
                        role="link"
                        aria-current="page"
                        data-dt-idx="0"
                        tabindex="0"
                        class="page-link"
                        >1</a
                      >
                    </li>
                    <li
                      class="paginate_button page-item next disabled"
                      id="botble-marketplace-tables-order-table_next"
                    >
                      <a
                        aria-controls="botble-marketplace-tables-order-table"
                        aria-disabled="true"
                        aria-label="Trang trước &amp;raquo;"
                        role="link"
                        data-dt-idx="next"
                        tabindex="-1"
                        class="page-link"
                        >Trang trước »</a
                      >
                    </li>
                  </ul> --}}
                                        {{ $stores->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.auto-submit').forEach(function(el) {
            el.addEventListener('change', function() {
                document.getElementById('store-filter-form').submit();
            });
        });

        // Optional: submit khi ấn Enter trong input search
        document.querySelector('input[name="name"]').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // ngăn reload mặc định
                document.getElementById('store-filter-form').submit();
            }
        });
    </script>
@endpush
