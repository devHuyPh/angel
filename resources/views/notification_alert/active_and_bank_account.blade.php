@php
    $customer = auth('customer')->user();
@endphp

@if (!$customer->is_webhook_sepay_active)
    <!-- <style>
        .notification_active_account {
            display: none;
        }

        @media (max-width: 768px) {

            .notification_active_account,
            .notification_bank_account {
                display: block;
            }

            .notification_active_account h4,
            .notification_bank_account h4 {
                font-size: 9px;
            }

            .gap-alert, .gap-alert-error {
                gap: 5px !important;
            }

            .gap-alert-error svg{
                color: rgba(var(--bs-danger-rgb), var(--bs-text-opacity)) !important;;
            }
        }
    </style>

    {{-- active account --}}
    @if (!$customer->is_active_account)
        <div class="col-12 mt-3 mb-md-0 text-md-start notification_active_account">
            <h3 class="fs-2 fw-bold text-uppercase mb-0" style="font-family: 'Inter';">
                <div class="mt-2">
                    <div role="alert" class="alert alert-danger">
                        <div class="d-flex align-items-center gap-alert-error">
                            <div>
                                <svg class="icon alert-icon svg-icon-ti-ti-info-circle" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                    <path d="M12 9h.01"></path>
                                    <path d="M11 12h1v4h1"></path>
                                </svg>
                            </div>
                            <div class="w-100">
                                <h4 class="alert-title text-danger mb-0">
                                    {{ trans('core/base::layouts.account-activation-instruction') }}
                                    <a class="text-success fw-bold" href="/products">Mua ngay</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </h3>
        </div>
    @endif
    {{-- end active account --}}

    {{-- add account --}}
    <div class="col-12 mt-3 mb-md-0 text-md-start notification_bank_account">
        <h3 class="fs-2 fw-bold text-uppercase mb-0" style="font-family: 'Inter';">
            <div class="mt-2">
                <div role="alert" class="alert alert-danger">
                    <div class="d-flex align-items-center gap-alert-error">
                        <div>
                            <svg class="icon alert-icon svg-icon-ti-ti-info-circle" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                <path d="M12 9h.01"></path>
                                <path d="M11 12h1v4h1"></path>
                            </svg>
                        </div>
                        <div class="w-100">
                            <h4 class="alert-title text-danger mb-0">
                                {{ trans('core/base::layouts.sepay-activation-instruction') }}.
                                <a class="text-success fw-bold" href="{{ route('withdrawals.setup-sepay') }}">Thiết lập
                                    ngay</a>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </h3>
    </div>
    {{-- end add account --}} -->

@endif
 <style>
        .btn-bank{
            padding: 10px 20px;
            background: #4ba314;
            border-radius: 6px;
            color: white;
            font-weight: 700;
        }
        .alert-title{
            background: #f8d7da;
            color:#db3545;
        }
    </style>
    <div>
        @if (!$customer->is_webhook_sepay_active)
        <p class="alert-title p-2">
            <svg class="icon alert-icon svg-icon-ti-ti-info-circle" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                <path d="M12 9h.01"></path>
                                <path d="M11 12h1v4h1"></path>
                            </svg>{{ trans('core/base::layouts.sepay-activation-instruction') }}.</p>
        @endif
        {{-- <a class="btn-bank" href="{{ route('withdrawals.setup-sepay') }}">{{trans('core/base::layouts.add_new')}}</a> --}}
    </div>
