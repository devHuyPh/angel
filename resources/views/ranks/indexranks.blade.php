@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
        {{-- Danh sach cap bac --}}
        <div class="table-wrapper">
            <div class="card has-actions">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="card-title mb-0">
                        {{ trans('core/base::layouts.rank_list') }}
                    </h4>

                    <div class="card-toolbar d-flex align-items-center gap-2">
                        <a href="{{ route('rank.add') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            {{ trans('core/base::layouts.add_new') }}
                        </a>
                    </div>
                </div>

                <div class="card-table">
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ trans('core/base::layouts.rank_name') }}</th>
                                    <th>{{ trans('core/base::layouts.level') }}</th>
                                    <th class="d-none d-lg-table-cell">{{ trans('core/base::layouts.icon') }}</th>
                                    <th class="d-none d-md-table-cell">
                                        {{ trans('core/base::layouts.upgrade_conditions') }}
                                    </th>
                                    <th class="d-none d-md-table-cell">
                                        {{ trans('core/base::layouts.demotion_conditions') }}
                                    </th>
                                    <th>{{ trans('core/base::layouts.reward_percentage') }}</th>
                                    <th class="d-none d-lg-table-cell">{{ trans('core/base::layouts.details') }}</th>
                                    <th>{{ trans('core/base::layouts.status') }}</th>
                                    <th class="text-end">{{ trans('core/base::layouts.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                    <tr data-id="{{ $item->id }}">
                                        <td class="fw-semibold">
                                            {{ $item->rank_name }}
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-primary text-white">
                                                {{ $item->rank_lavel }}
                                            </span>
                                        </td>

                                        <td class="d-none d-lg-table-cell">
                                            @if ($item->rank_icon)
                                                <img src="{{ asset($item->rank_icon) }}" alt="Rank icon" class="rounded-circle border"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                        </td>

                                        <td class="text-success d-none d-md-table-cell">
                                            <div>
                                                <strong>{{ trans('core/base::layouts.referrals') }}:</strong>
                                                {{ $item->number_referrals }}
                                            </div>
                                            <div>
                                                <strong>{{ trans('core/base::layouts.total_revenue') }}:</strong>
                                                {{ format_price($item->total_revenue) }}
                                            </div>
                                        </td>

                                        @if ($item->demotion_time_months || $item->demotion_referrals || $item->demotion_investment || $item->ranking_date_conditions)
                                            <td class="text-danger d-none d-md-table-cell">
                                                <div>
                                                    <strong>{{ trans('core/base::layouts.months') }}:</strong>
                                                    {{ $item->demotion_time_months }}
                                                </div>
                                                <div>
                                                    <strong>{{ trans('core/base::layouts.referrals') }}:</strong>
                                                    {{ $item->demotion_referrals }}
                                                </div>
                                                <div>
                                                    <strong>{{ trans('core/base::layouts.investments') }}:</strong>
                                                    {{ format_price($item->demotion_investment) }}
                                                </div>
                                                <div>
                                                    <strong>{{ trans('core/base::layouts.ranking_date_conditions') }}:</strong>
                                                    {{ $item->ranking_date_conditions }}
                                                </div>
                                            </td>
                                        @else
                                            <td class="d-none d-md-table-cell text-muted">
                                                {{ __('No demotion conditions') }}
                                            </td>
                                        @endif

                                        <td class="fw-semibold">
                                            {{ $item->percentage_reward }}%
                                        </td>

                                        <td class="d-none d-lg-table-cell">
                                            @if ($item->description)
                                                {{ Str::limit($item->description, 42) }}
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <span class="badge {{ $item->status ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                                {{ $item->status ? trans('core/base::layouts.active') : trans('core/base::layouts.inactive') }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <a href="{{ route('rank.edit', $item->id) }}" class="btn btn-icon btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button class="btn btn-icon btn-sm btn-danger delete-btn" data-bs-toggle="modal"
                                                data-bs-target="#delete-modal" data-route="{{ route('rank.delete', $item->id) }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ trans('core/base::layouts.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        <div class="text-muted d-flex align-items-center gap-2">
                            <i class="fas fa-globe"></i>
                            <span>
                                {{ trans('core/base::layouts.show_from') }}
                                {{ $data->firstItem() ?? 0 }}
                                {{ trans('core/base::layouts.to') }}
                                {{ $data->lastItem() ?? 0 }}
                                {{ trans('core/base::layouts.in') }}
                            </span>
                            <span class="badge bg-secondary text-secondary-fg">
                                {{ $data->total() }}
                            </span>
                            <span>{{ trans('core/base::layouts.records') }}</span>
                        </div>
                        <div class="dataTables_paginate paging_simple_numbers mb-0">
                            {{ $data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Danh sach hang nguoi dung --}}
        <div class="table-wrapper mt-4">
            <div id="table-configuration-wrap" class="card mb-3 table-configuration-wrap"
                style="{{ request()->filled('rank_filter') ? '' : 'display: none;' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="mb-0 fw-semibold">{{ __('Filters') }}</p>
                        <button id="cancel-table-configuration-wrap" class="btn btn-icon btn-sm btn-show-table-options rounded-pill"
                            type="button">
                            <svg class="icon icon-sm icon-left svg-icon-ti-ti-x" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M18 6l-12 12"></path>
                                <path d="M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form method="GET" action="{{ route('rank.index') }}" class="filter-form user-rank-ajax-form" id="user-rank-filter-form">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label for="rank_filter" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.select_rank') }}
                                </label>
                                <select name="rank_filter" id="rank_filter" class="form-select">
                                    <option value="">{{ __('All ranks') }}</option>
                                    <option value="no_rank" {{ request('rank_filter') === 'no_rank' ? 'selected' : '' }}>
                                        {{ trans('core/base::layouts.no_rank') }}
                                    </option>
                                    @foreach ($ranks as $rank)
                                        <option value="{{ $rank->id }}" {{ (string) request('rank_filter') === (string) $rank->id ? 'selected' : '' }}>
                                            {{ $rank->rank_name }} ({{ $rank->rank_lavel }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 d-flex justify-content-end gap-2">
                                <a href="{{ route('rank.index') }}" class="btn btn-outline-secondary">
                                    {{ __('Reset') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Apply') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card has-actions has-filter">
                <div class="card-header">
                    <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                        <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-2">
                            <button id="btn-show-table-options" class="btn btn-show-table-options" type="button">
                                {{ __('Filters') }}
                            </button>

                            <form method="GET" action="{{ route('rank.index') }}"
                                class="d-flex align-items-center table-search-input gap-2 user-rank-ajax-form" id="user-rank-search-form">
                                <input type="hidden" name="rank_filter" value="{{ request('rank_filter') }}">
                                <label class="mb-0">
                                    <input type="search" name="search" class="form-control input-sm"
                                        placeholder="{{ trans('core/base::layouts.search') }}" style="min-width: 160px"
                                        value="{{ request('search') }}">
                                </label>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ trans('core/base::layouts.search') }}
                                </button>

                                @if (request()->filled('search') || request()->filled('rank_filter'))
                                    <a href="{{ route('rank.index') }}" class="btn btn-outline-secondary btn-sm">
                                        {{ __('Reset') }}
                                    </a>
                                @endif
                            </form>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('rank.index', request()->only(['search', 'rank_filter'])) }}" class="btn"
                                type="button">
                                <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                </svg>
                                {{ __('Reload') }}
                            </a>

                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRankModal" type="button">
                                <i class="fas fa-plus me-1"></i>
                                {{ trans('core/base::layouts.add_new') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div id="user-rank-table-wrapper">
                    @include('ranks.partials.user_rank_table')
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL THASM USER RANK --}}
    <div class="modal fade" id="addRankModal" tabindex="-1" aria-labelledby="addRankModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="addRankModalLabel">
                        {{ trans('core/base::layouts.add_new_by_list') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="addRankForm" method="POST" action="{{ route('customer.store.rank') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label fw-semibold">
                                {{ trans('core/base::layouts.select_user') }}
                            </label>
                            <select name="customer_id" id="customer_id" class="form-control" required>
                                <option value="">
                                    -- {{ trans('core/base::layouts.select_user') }} --
                                </option>
                                @foreach ($allCustomers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="rank_id_add" class="form-label fw-semibold">
                                {{ trans('core/base::layouts.select_rank') }}
                            </label>
                            <select name="rank_id" id="rank_id_add" class="form-control">
                                <option value="">{{ trans('core/base::layouts.no_rank') }}</option>
                                @foreach ($ranks as $rank)
                                    <option value="{{ $rank->id }}">
                                        {{ $rank->rank_name }} (Cap {{ $rank->rank_lavel }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ trans('core/base::layouts.close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ trans('core/base::layouts.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL SUA USER RANK --}}
    <div class="modal fade" id="editRankModal" tabindex="-1" aria-labelledby="editRankModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="editRankModalLabel">
                        {{ trans('core/base::layouts.edit_rank_user') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="editRankForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="customer_id" id="edit_customer_id">

                        <div class="mb-3">
                            <label for="rank_id" class="form-label fw-semibold">
                                {{ trans('core/base::layouts.select_rank') }}
                            </label>
                            <select name="rank_id" id="rank_id" class="form-control">
                                <option value="">{{ trans('core/base::layouts.no_rank') }}</option>
                                @foreach ($ranks as $rank)
                                    <option value="{{ $rank->id }}">
                                        {{ $rank->rank_name }} (Cap {{ $rank->rank_lavel }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ trans('core/base::layouts.close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ trans('core/base::layouts.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL XOA RANK --}}
    <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title fw-bold" id="deleteModalLabel">
                        {{ trans('core/base::layouts.confirm_deletion') }}
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ trans('core/base::layouts.are_you_sure_you_want_to_delete_this_rank') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ trans('core/base::layouts.close') }}
                    </button>
                    <form id="delete-form" method="POST" action="" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            {{ trans('core/base::layouts.yes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL XOA CUSTOMER RANK --}}
    <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title fw-bold" id="deleteCustomerModalLabel">
                        {{ trans('core/base::layouts.confirm_deletion') }}
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ trans('core/base::layouts.confirm_status') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ trans('core/base::layouts.close') }}
                    </button>
                    <form id="delete-customer-form" method="POST" action="" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            {{ trans('core/base::layouts.yes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function () {
            // Delete rank
            $(document).on('click', '.delete-btn', function () {
                const route = $(this).data('route');
                $('#delete-form').attr('action', route);
            });

            // Delete customer rank
            $(document).on('click', '.delete-customer-btn', function () {
                const route = $(this).data('route');
                $('#delete-customer-form').attr('action', route);
            });

            // Bind data to edit modal
            $(document).on('click', '.edit-rank-btn', function () {
                const customerId = $(this).data('id');
                const rankId = $(this).data('rank-id') || '';

                $('#edit_customer_id').val(customerId);
                $('#rank_id').val(rankId);

                $('#editRankForm').attr(
                    'action',
                    '{{ url('/admin/customers') }}/' + customerId + '/update-rank'
                );
            });

            // Toggle filter wrap
            $('#btn-show-table-options').on('click', function () {
                $('#table-configuration-wrap').slideToggle(300);
            });

            $('#cancel-table-configuration-wrap').on('click', function () {
                $('#table-configuration-wrap').slideUp(300);
            });

            const $userRankWrapper = $('#user-rank-table-wrapper');

            function syncUserRankForms(params) {
                const searchVal = params.get('search') || '';
                const rankVal = params.get('rank_filter') || '';

                $('#user-rank-search-form input[name=\"search\"]').val(searchVal);
                $('#user-rank-search-form input[name=\"rank_filter\"]').val(rankVal);
                $('#user-rank-filter-form input[name=\"search\"]').val(searchVal);
                $('#rank_filter').val(rankVal);
            }

            function loadUserRanks(url) {
                const targetUrl = url || '{{ route('rank.index') }}';
                const searchParams = new URL(targetUrl, window.location.href).searchParams;
                $.get(targetUrl, function (html) {
                    $userRankWrapper.html(html);
                    syncUserRankForms(searchParams);
                });
            }

            $('.user-rank-ajax-form').on('submit', function (e) {
                e.preventDefault();
                const query = $(this).serialize();
                const url = $(this).attr('action') + (query ? '?' + query : '');
                loadUserRanks(url);
            });

            $('#rank_filter').on('change', function () {
                $('#user-rank-filter-form').trigger('submit');
            });

            $(document).on('click', '#user-rank-table-wrapper .pagination a', function (e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    loadUserRanks(url);
                }
            });
        });
    </script>
@endpush
