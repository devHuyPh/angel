<div class="card-table">
    <div class="table-responsive table-has-actions table-has-filter">
        <table class="table card-table table-vcenter table-striped table-hover dataTable no-footer dtr-inline align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ trans('core/base::layouts.user_name') }}</th>
                    <th>{{ trans('core/base::layouts.rank_name') }}</th>
                    <th>{{ trans('core/base::layouts.level') }}</th>
                    <th class="d-none d-lg-table-cell">{{ trans('core/base::layouts.icon_rank') }}</th>
                    <th>{{ trans('core/base::layouts.total_doawline') }}</th>
                    <th>{{ trans('core/base::layouts.walet1') }}</th>
                    <th>{{ trans('core/base::layouts.status_admin') }}</th>
                    <th class="text-end">{{ trans('core/base::layouts.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr data-id="{{ $customer->id }}">
                        <td class="fw-semibold">
                            {{ $customer->name }}
                        </td>

                        <td>
                            {{ $customer->rank->rank_name ?? trans('core/base::layouts.no_rank') }}
                        </td>

                        <td class="text-center">
                            <span class="badge bg-primary text-white">
                                {{ $customer->rank->rank_lavel ?? 'N/A' }}
                            </span>
                        </td>

                        <td class="d-none d-lg-table-cell">
                            @if ($customer->rank && $customer->rank->rank_icon)
                                <img src="{{ asset($customer->rank->rank_icon) }}" alt="Rank icon"
                                    class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <span class="text-muted">{{ __('No icon') }}</span>
                            @endif
                        </td>

                        <td>
                            {{ format_price($customer->total_dowline) }}
                        </td>

                        <td>
                            {{ format_price($customer->walet_1) }}
                        </td>

                        <td class="text-center">
                            <span class="badge bg-success text-white">
                                {{ trans('core/base::layouts.active') }}
                            </span>
                        </td>

                        <td class="text-end">
                            <button class="btn btn-icon btn-sm btn-primary edit-rank-btn me-1"
                                data-bs-toggle="modal" data-bs-target="#editRankModal" data-id="{{ $customer->id }}"
                                data-rank-id="{{ $customer->rank_id ?? '' }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button class="btn btn-icon btn-sm btn-danger delete-customer-btn"
                                data-bs-toggle="modal" data-bs-target="#deleteCustomerModal"
                                data-route="{{ route('customer.delete.rank', $customer->id) }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            {{ trans('core/base::layouts.no_data_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <div class="text-muted">
            <span class="dt-length-records">
                <svg class="icon svg-icon-ti-ti-world" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                    <path d="M3.6 9h16.8"></path>
                    <path d="M3.6 15h16.8"></path>
                    <path d="M11.5 3a17 17 0 0 0 0 18"></path>
                    <path d="M12.5 3a17 17 0 0 1 0 18"></path>
                </svg>
                <span class="d-none d-sm-inline">{{ trans('core/base::layouts.show_from') }}</span>
                {{ $customers->firstItem() ?? 0 }}
                {{ trans('core/base::layouts.to') }}
                {{ $customers->lastItem() ?? 0 }}
                {{ trans('core/base::layouts.in') }}
                <span class="badge bg-secondary text-secondary-fg">
                    {{ $customers->total() }}
                </span>
                <span class="hidden-xs">{{ trans('core/base::layouts.records') }}</span>
            </span>
        </div>

        <div class="dataTables_paginate paging_simple_numbers">
            {{ $customers->appends(request()->query())->links() }}
        </div>
    </div>
</div>
