@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Overview'))

@section('content')
    <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <a href="{{ route('setting') }}" class="back-btn text-success">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="header-title text-success">{{ __('Rút tiền Marketing') }}</h1>
    </div>
    @include('notification_alert.active_and_bank_account')
    <style>
        .mobile {
            display: none !important;
        }

        @media (max-width: 767.98px) {

            .bg-custom-moblie {
                padding: 0 !important;
            }

            .profile__tab-content {
                padding: 0 !important;
            }

            .form-control {
                /* font-size: 16px !important; */
            }

            .h3-mobile-referral {
                font-size: 16px !important;
                background: #f8f8f8;
                padding: 0.5rem 0 0.5rem 10px !important;
            }

            .desktop {
                display: none !important;
            }

            .mobile {
                display: block !important;
            }

            h5 {
                font-size: 12px !important;
            }

            /* .button-send {
                                            margin: 0 0 20px 10px !important;
                                        } */
        }
    </style>
    <div id="app">
        <div class="table-wrapper desktop">
            <div class="card">
                <div class="card-header bg-white border-0">
                    <div class="w-100 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="text-muted">
                                <div class="small">{{ trans('core/base::layouts.balance') }}</div>
                                <div class="fw-semibold h5 mb-0 text-success">{{ format_price($customer->walet_1) }}</div>
                            </div>
                            <div class="table-search-input">
                                <label class="mb-0">
                                    <input type="search" class="form-control form-control-sm"
                                        placeholder="{{ trans('core/base::layouts.search') }}" style="min-width: 160px">
                                </label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-secondary" type="button" data-bb-toggle="dt-buttons"
                                data-bb-target=".buttons-reload" tabindex="0"
                                aria-controls="botble-marketplace-tables-vendor-withdrawal-table">
                                <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg"
                                    width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                </svg>
                                {{ trans('core/base::layouts.reload') }}
                            </button>
                            @if ($customer->is_webhook_sepay_active)
                                <a href="{{ route('withdrawals.create') }}" class="btn btn-primary"
                                    tabindex="0" aria-controls="botble-marketplace-tables-vendor-withdrawal-table"
                                    type="button" aria-haspopup="dialog" aria-expanded="false">
                                    <span data-action="create" class="d-inline-flex align-items-center gap-1">
                                        <svg class="icon svg-icon-ti-ti-plus" xmlns="http://www.w3.org/2000/svg"
                                            width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 5l0 14"></path>
                                            <path d="M5 12l14 0"></path>
                                        </svg>
                                        {{ trans('core/base::layouts.add_new_withdrawal') }}
                                    </span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-table">
                    <div class="table-responsive">
                        <div id="botble-marketplace-tables-vendor-withdrawal-table_wrapper"
                            class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <table
                                class="table card-table table-vcenter table-striped table-hover dataTable no-footer dtr-inline"
                                id="botble-marketplace-tables-vendor-withdrawal-table"
                                aria-describedby="botble-marketplace-tables-vendor-withdrawal-table_info">
                                <thead>
                                    <tr>
                                        <th title="ID" width="20"
                                            class="text-center no-column-visibility column-key-0 sorting sorting_desc"
                                            tabindex="0" aria-controls="botble-marketplace-tables-vendor-withdrawal-table"
                                            rowspan="1" colspan="1" style="width: 20px;" aria-sort="descending"
                                            aria-label="IDorderby asc">ID</th>
                                        <th title="Amount" class="column-key-1 sorting" tabindex="0"
                                            aria-controls="botble-marketplace-tables-vendor-withdrawal-table" rowspan="1"
                                            colspan="1" aria-label="Amountorderby asc">
                                            {{ trans('core/base::layouts.amount') }}
                                        </th>
                                        <th title="Fee" class="column-key-2 sorting" tabindex="0"
                                            aria-controls="botble-marketplace-tables-vendor-withdrawal-table" rowspan="1"
                                            colspan="1" aria-label="Feeorderby asc">
                                            {{ trans('core/base::layouts.currency') }}
                                        </th>
                                        <th title="Status" width="100" class="text-center column-key-3 sorting"
                                            tabindex="0" aria-controls="botble-marketplace-tables-vendor-withdrawal-table"
                                            rowspan="1" colspan="1" style="width: 100px;"
                                            aria-label="Statusorderby asc">
                                            {{ trans('core/base::layouts.status') }}
                                        </th>
                                        <th title="Created At" width="100" class="column-key-4 sorting" tabindex="0"
                                            aria-controls="botble-marketplace-tables-vendor-withdrawal-table"
                                            rowspan="1" colspan="1" style="width: 100px;"
                                            aria-label="Created Atorderby asc">
                                            {{ trans('core/base::layouts.created_at') }}
                                        </th>
                                        <th title="Operations" class="text-nowrap text-center sorting_disabled"
                                            rowspan="1" colspan="1" aria-label="Operations">
                                            {{ trans('core/base::layouts.operations') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($withdrawals->isEmpty())
                                        <tr class="odd">
                                            <td valign="top" colspan="8" class="dataTables_empty text-center">
                                                {{ trans('core/base::layouts.no_data_found') }}
                                            </td>
                                        </tr>
                                    @endif
                                    @foreach ($withdrawals as $withdrawal)
                                        <tr class="odd">
                                            <td
                                                class="text-center no-column-visibility column-key-0 sorting_1 dtr-control">
                                                {{ $withdrawal->id }}
                                            </td>
                                            <td class="   column-key-1">{{ format_price($withdrawal->amount) }}</td>
                                            <td class="   column-key-2">{{ $withdrawal->currency }}</td>
                                            <td class="text-center column-key-3">
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'bg-warning text-warning-fg',
                                                        'completed' => 'bg-success text-success-fg',
                                                        'rejected' => 'bg-danger text-danger-fg',
                                                        'cancelled' => 'bg-danger text-danger-fg',
                                                    ];
                                                @endphp

                                                <span
                                                    class="badge {{ $statusClasses[$withdrawal->status] ?? 'bg-secondary text-secondary-fg' }}">
                                                    {{ trans('core/base::layouts.' . $withdrawal->status) }}
                                                </span>
                                            </td>

                                            <td class="    text-nowrap column-key-4">
                                                {{ date_format($withdrawal->created_at, 'H:i d-m-Y') }}</td>
                                            <td class="  text-nowrap text-center column-key-5">
                                                <div class="table-actions">
                                                    <a class="btn btn-icon btn-sm btn-success" data-bs-toggle="tooltip"
                                                        data-bs-original-title="Show"
                                                        href="{{ route('withdrawals.show', $withdrawal->id) }}">
                                                        <svg class="icon  svg-icon-ti-ti-eye"
                                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                                            <path
                                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6">
                                                            </path>
                                                        </svg> </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2"
                                style>
                                <p>{{ trans('core/base::layouts.show_from') }}
                                    {{ $withdrawals->firstItem() }}
                                    {{ trans('core/base::layouts.to') }}
                                    {{ $withdrawals->lastItem() }}
                                    {{ trans('core/base::layouts.in') }}
                                    {{ $withdrawals->total() }}
                                    {{ trans('core/base::layouts.records') }}</p>
                                {{ $withdrawals->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mobile">
            @if ($customer->is_webhook_sepay_active)
                <div class="row align-items-center justify-content-between p-3">
                    <a href="{{ route('withdrawals.create') }}" class="col-6 btn btn-primary button-send"
                        tabindex="0" aria-controls="botble-marketplace-tables-vendor-withdrawal-table" type="button"
                        aria-haspopup="dialog" aria-expanded="false">
                        <span data-action="create" class="d-inline-flex align-items-center gap-1">
                            <svg class="icon svg-icon-ti-ti-plus" xmlns="http://www.w3.org/2000/svg" width="20"
                                height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 5l0 14"></path>
                                <path d="M5 12l14 0"></path>
                            </svg> {{ trans('core/base::layouts.add_new_withdrawal') }}
                        </span>
                    </a>
                    <div class="col-6 text-end">
                        <div class="small text-muted">{{ trans('core/base::layouts.balance') }}</div>
                        <div class="fw-semibold text-success">{{ format_price($customer->walet_1) }}</div>
                    </div>
                </div>
            @endif
            <h3 class="h3-mobile-referral text-success">Lịch sử rút tiền</h3>
            <div class="container">
                @if ($withdrawals->isEmpty())
                    <p>{{ trans('core/base::layouts.no_data_found') }}</p>
                @endif
                @foreach ($withdrawals as $withdrawal)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold text-dark mb-1">
                                    {{ trans('core/base::layouts.amount') }}: {{ format_price($withdrawal->amount) }}
                                </div>
                                <div class="text-muted small mb-1">
                                    {{ trans('core/base::layouts.transaction_id') }}: {{ $withdrawal->transaction_id }}
                                </div>
                                <div class="text-muted small mb-1">
                                    {{ trans('core/base::layouts.created_at') }}: {{ date_format($withdrawal->created_at, 'H:i d-m-Y') }}
                                </div>
                                <div class="text-muted small mb-1">
                                    {{ trans('core/base::layouts.withdrawal_method') }}: {{ $withdrawal->withdrawal_method }}
                                </div>
                                <div class="text-muted small mb-1">
                                    {{ trans('core/base::layouts.fee') }}: {{ format_price($withdrawal->fee) }}
                                </div>
                            </div>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-warning text-warning-fg',
                                    'completed' => 'bg-success text-success-fg',
                                    'rejected' => 'bg-danger text-danger-fg',
                                    'cancelled' => 'bg-danger text-danger-fg',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$withdrawal->status] ?? 'bg-secondary text-secondary-fg' }}">
                                {{ trans('core/base::layouts.' . $withdrawal->status) }}
                            </span>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a class="btn btn-link p-0" href="{{ route('withdrawals.show', $withdrawal->id) }}">
                                {{ trans('core/base::layouts.view') }}
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="my-3 py-3" style="color: grey">
                    <p>{{ trans('core/base::layouts.show_from') }}
                        {{ $withdrawals->firstItem() }}
                        {{ trans('core/base::layouts.to') }}
                        {{ $withdrawals->lastItem() }}
                        {{ trans('core/base::layouts.in') }}
                        {{ $withdrawals->total() }}
                        {{ trans('core/base::layouts.records') }}</p>
                    {{ $withdrawals->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
