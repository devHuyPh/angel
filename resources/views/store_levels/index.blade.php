@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid p-0">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <h3 class="mb-1">
                    <i class="fas fa-store me-2"></i>{{ __('Danh sách kho bãi') }}
                </h3>
                <p class="text-muted mb-0">
                    {{ __('Quản lý kho bãi và cấu hình chiết khấu theo từng kho.') }}
                </p>
            </div>
        </div>

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- CẤU HÌNH CHIẾT KHẤU KHO --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light border-0 py-2">
                <h5 class="mb-0">
                    {{ __('Cấu hình chiết khấu kho') }}
                </h5>
            </div>

            <div class="card-body">
                @if ($levels->isEmpty())
                    <p class="text-muted mb-0">
                        {{ trans('core/base::layouts.empty') }}
                    </p>
                @else
                    <div class="row">
                        @foreach($levels as $level)
                            <div class="col-12 mb-3">
                                <div class="border rounded-3 p-3 position-relative">

                                    {{-- Nút sửa level --}}
                                    <a href="{{ route('store-levels.edit', $level->id) }}"
                                       class="btn btn-warning btn-sm position-absolute"
                                       style="top: 8px; right: 8px;"
                                       title="{{ trans('core/base::layouts.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <div class="text-uppercase small text-muted mb-1">
                                        {{ __('Level kho') }}
                                    </div>

                                    <div class="fw-semibold mb-2">
                                        {{ $level->name }}
                                    </div>

                                    <div class="d-flex justify-content-between small text-muted mt-2">
                                        <span>{{ trans('core/base::layouts.commission') }}</span>
                                        <span class="fw-semibold text-dark">
                                            {{ $level->commission }}%
                                        </span>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- DANH SÁCH KHO --}}
        <div class="card shadow-sm">

            <div class="card-header bg-light border-0 py-2">
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">

                    <div class="text-uppercase text-muted small">
                        {{ __('Danh sách kho') }}
                    </div>

                    {{-- Thanh tìm kiếm --}}
                    <form method="GET"
                          action="{{ route('store-levels.index') }}"
                          class="d-flex align-items-center gap-2 flex-wrap">

                        <div class="position-relative" style="min-width: 220px; max-width: 320px;">
                            <input
                                type="text"
                                name="keyword"
                                class="form-control form-control-sm pe-4"
                                value="{{ $keyword }}"
                                placeholder="{{ __('Tìm kiếm...') }}"
                            >
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-2">
                                <i class="fas fa-search text-muted small"></i>
                            </span>
                        </div>

                        <button class="btn btn-sm btn-primary" type="submit">
                            <i class="fas fa-filter me-1"></i>{{ __('Lọc') }}
                        </button>

                        @if ($selectedLevel || $keyword)
                            <a href="{{ route('store-levels.index') }}"
                               class="btn btn-sm btn-outline-secondary">
                                {{ trans('core/base::tables.reset') }}
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    @php
                        $startIndex = $stores instanceof \Illuminate\Pagination\LengthAwarePaginator
                            ? $stores->firstItem()
                            : 1;
                    @endphp

                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th>{{ trans('core/base::layouts.store_name') }}</th>
                                <th>{{ trans('core/base::layouts.user_name') }}</th>
                                <th>{{ trans('core/base::layouts.referrer') }}</th>
                                <th class="text-end">
                                    {{ __('Tổng đơn đặt') }}
                                </th>
                                <th class="text-center">
                                    {{ trans('core/base::layouts.action') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($stores as $index => $store)
                                <tr>
                                    <td>{{ $startIndex + $index }}</td>

                                    <td class="fw-semibold">
                                        {{ $store->name }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $store->customer?->name ?? trans('core/base::layouts.n_a') }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $store->customer?->phone }}
                                            @if ($store->customer?->email)
                                                - {{ $store->customer->email }}
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $store->customer?->referrer?->name ?? trans('core/base::layouts.n_a') }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $store->customer?->referrer?->phone }}
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        {{ number_format($store->orders_count ?? 0) }}
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('store-levels.stores.show', $store->id) }}"
                                           class="btn btn-icon btn-sm btn-primary"
                                           title="{{ trans('core/base::tables.view') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        {{ trans('core/base::layouts.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>

            @if ($stores instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        {{ __('Hiển thị') }}
                        {{ $stores->firstItem() }} – {{ $stores->lastItem() }}
                        / {{ $stores->total() }}
                    </div>
                    {{ $stores->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
