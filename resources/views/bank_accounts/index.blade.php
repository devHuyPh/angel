@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Tài khoản ngân hàng'))

@section('content')
    <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <a href="{{ route('setting') }}" class="back-btn text-success">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="header-title text-success">{{ __('Tài khoản ngân hàng') }}</h1>
    </div>
    @php
        $customer = auth('customer')->user();
    @endphp
    <style>
        @media (max-width: 768px) {
            .alert-title {
                font-size: 9px !important;
                /* line-height */
            }
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

            .img-bank-mobile {
                /* width: 100px !important; */
                margin-bottom: 20px !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            .item-content-bank {
                display: flex !important;
                gap: 10px !important;
                justify-content: center !important;
                align-items: center !important;
            }

            .qr {
                max-width: 100px !important;
            }

            .account-content {
                p {
                    margin-bottom: 0 !important;
                }
            }

            .profile__address-title {
                font-size: 14px !important;
            }

            .profile__area {
                min-height: 100vh !important;
            }
        }

        @media (max-width: 767.98px) {
            .mobile-actions {
                position: sticky;
                bottom: 0;
                left: 0;
                right: 0;
                background: #fff;
                padding: 12px;
                box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
                z-index: 10;
            }
        }

        .summary-card {
            border: 1px solid #e9ecef;
        }

        .bank-card {
            border: 1px solid #e9ecef;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .bank-card:hover {
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }
    </style>
    <div class="container my-3 my-md-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">{{ __('Danh sách tài khoản ngân hàng') }}</h4>
                <p class="text-muted small mb-0">{{ __('Quản lý tài khoản nhận tiền rút của bạn') }}</p>
            </div>
            @if ($accounts->isNotEmpty())
                <a class="btn btn-primary d-none d-md-inline-flex" href="{{ route('withdrawals.edit-setup-sepay') }}">
                    {{ trans('core/base::layouts.edit') }}
                </a>
            @endif
        </div>

        @if ($accounts->isEmpty())
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <h5 class="mb-2">{{ __('Chưa có tài khoản ngân hàng') }}</h5>
                    <p class="text-muted mb-3">{{ __('Thêm tài khoản để nhận tiền rút tự động.') }}</p>
                    <a class="btn btn-primary" href="{{ route('withdrawals.setup-sepay') }}">
                        {{ trans('core/base::layouts.add_new') }}
                    </a>
                </div>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 g-3">
                @foreach ($accounts as $account)
                    <div class="col">
                        <div class="card h-100 shadow-sm bank-card">
                            <div class="card-body d-flex align-items-start gap-3">
                                @php
                                    $logo = $account->bank->image ?? null;
                                @endphp
                                <div class="flex-shrink-0">
                                    @if ($logo)
                                        <img src="{{ asset('storage/' . $logo) }}" alt="bank_image"
                                            class="img-bank-mobile" width="60">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                            style="width:60px;height:60px;">
                                            <span class="text-muted fw-bold">{{ $account->bank_code }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="profile__address-title mb-1">{{ $account->account_holder }}</h5>
                                        @if ($account->is_default ?? false)
                                            <span class="badge bg-success">Default</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small mb-1">
                                        {{ trans('core/base::layouts.account_number') }}: {{ $account->account_number }}
                                    </div>
                                    <div class="text-muted small mb-1">
                                        {{ trans('core/base::layouts.bank_name') }}: {{ $account->bank->name ?? '' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ trans('core/base::layouts.bank_code') }}: {{ $account->bank_code }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">{{ __('QR chuyển khoản nhanh') }}</div>
                                    <img src="https://img.vietqr.io/image/{{ $account->bank_code }}-{{ $account->account_number }}-compact2.png?accountName={{ $account->account_holder }}"
                                        alt="QR VietQR" class="qr" style="max-width: 120px;">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="d-md-none mobile-actions mt-3">
            @if ($accounts->isNotEmpty())
                <a class="btn btn-primary w-100" href="{{ route('withdrawals.edit-setup-sepay') }}">
                    {{ trans('core/base::layouts.edit') }}
                </a>
            @endif
        </div>
    </div>
@endsection
