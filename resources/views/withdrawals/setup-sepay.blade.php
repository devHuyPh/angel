@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Overview'))

@section('content')
    <style>
        @media (max-width: 767.98px) {
            .desktop {
                display: none !important;
            }

            .mobile {
                display: block !important;
            }

            .h3-mobile-referral {
                font-size: 16px !important;
                background: #f8f8f8;
                padding: 0.5rem 0 0.5rem 10px !important;
            }
        }

        .card-title {
            margin-bottom: 0;
        }
    </style>
    <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <a href="{{ route('bank_accounts.index') }}" class="back-btn text-success">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="header-title text-success">{{ __('Thêm tài khoản ngân hàng') }}</h1>
    </div>
    <div class="container">
        <div class="row justify-content-center g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-success text-white">1</span>
                            <h5 class="mb-0">{{ __('Chuẩn bị') }}</h5>
                        </div>
                        <p class="text-muted mb-3">
                            {{ __('Kết nối tài khoản ngân hàng để tiền ') }}
                        </p>
                        <ul class="text-muted small ps-3 mb-0">
                            <li>{{ __('Đảm bảo thông tin chủ tài khoản trùng khớp .') }}</li>
                            <li>{{ __('Số tài khoản và ngân hàng phải chính xác.') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h4 class="card-title">{{ __('Thêm tài khoản ngân hàng') }}</h4>
                        <p class="text-muted mb-0">{{ trans('core/base::layouts.please_provide_information') }}
                            <a href="https://sepay.vn" target="_blank">{{ trans('core/base::layouts.bank') }}</a>
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('withdrawals.post-setup-sepay') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label" for="payment_sepay_bank">
                                    {{ trans('core/base::layouts.bank') }}
                                </label>
                                <select class="form-control" name="payment_sepay_bank" id="payment_sepay_bank">
                                    <option value="">{{ trans('core/base::layouts.choose_a_bank') }}</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank['bank_code'] }}">
                                            {{ $bank['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="payment_sepay_account_number">
                                    {{ trans('core/base::layouts.account_number') }}
                                </label>
                                <input class="form-control" data-counter="250" name="payment_sepay_account_number"
                                    type="text" value="" id="payment_sepay_account_number" placeholder="123456789">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="payment_sepay_account_holder">
                                    {{ trans('core/base::layouts.account_owner') }}
                                </label>
                                <input class="form-control" data-counter="250" name="payment_sepay_account_holder"
                                    type="text" value="" id="payment_sepay_account_holder" placeholder="NGUYEN VAN A">
                            </div>

                        {{-- <div class="mb-3 position-relative">
                    <label class="form-label" for="payment_sepay_prefix">
                        Tiền tố mã thanh toán
                    </label>
                    <input class="form-control" data-counter="250" name="payment_sepay_prefix" type="text" value="{{setting('payment_sepay_prefix')}}W"
                        id="payment_sepay_prefix" disabled="disabled">
                    <small class="form-hint">
                        {{ trans('core/base::layouts.only_letters_and_numbers_are_allowed,_no_accents_and_no_spaces._example:_sdh')}}
                    </small>
                    <input class="form-control" hidden readonly data-counter="250" name="payment_sepay_prefix" type="text" value="{{setting('payment_sepay_prefix')}}W"
                    id="payment_sepay_prefix">
                </div> --}}
                        {{-- <div class="mb-3 position-relative">
                    <label class="form-label" for="sepay-webhook-url">
                        Webhook URL
                    </label>
                    <input class="form-control" type="text" id="sepay-webhook-url" name="sepay_webhook_url"
                        value="{{url('/')}}/sepay/webhook/withdrawals" disabled="disabled">

                    <input class="form-control" type="text" id="sepay-webhook-url" name="sepay_webhook_url"
                        value="{{url('/')}}/sepay/webhook/withdrawals" hidden readonly>
                </div>
                <div class="mb-3 position-relative">
                    <label class="form-label" for="sepay-webhook-secret">
                        Mã bảo mật webhook
                    </label>
                    <input class="form-control" type="text" id="sepay-webhook-secret" name="sepay_webhook_secret"
                        value="********************************" disabled="disabled">

                    <input class="form-control" type="text" id="sepay-webhook-secret-send" name="sepay_webhook_secret"
                        value="********************************" hidden readonly >
                    <small class="form-hint">Sau khi tạo mã bảo mật, bạn không thể xem lại mã này. Vui lòng sao chép mã bảo mật
                        này và lưu trữ ở nơi an toàn. <br>
                        Vào <a href="https://my.sepay.vn" target="_blank">SePay</a> → Xác thực thanh toán → Webhook → Thêm
                        Webhook sau đó chọn kiểu xác thực là API key và dán mã bảo mật này vào ô API key. <br>
                        Trường hợp bạn quên hoặc chưa tạo mã, vui lòng bấm vào nút "Tạo mã bảo mật" bên dưới.</small>
                </div>

                <button class="btn   mb-2" type="button" data-bb-toggle="sepay-webhook-secret" id="create-sepay-webhook-secret">
                    <svg class="icon  svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                    </svg> Tạo mã bảo mật
                </button>

                <div role="alert" class="alert alert-warning">
                    <div class="d-flex">
                        <div>
                            <svg class="icon alert-icon svg-icon-ti-ti-alert-circle" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                <path d="M12 8v4"></path>
                                <path d="M12 16h.01"></path>
                            </svg>
                        </div>
                        <div class="w-100">
                            Lưu ý: Việc bấm vào mã này sẽ vô hiệu hóa mã bảo mật cũ, tạo mã bảo mật mới và chỉ hiển thị 1 lần
                            duy nhất. Bạn cần cập nhật mã bảo mật mới vào SePay.
                        </div>
                    </div>
                </div> --}}
                            <div class="col-12">
                                <label class="form-check mb-3">
                                    <input type="hidden" name="confirm_bank_account" value="0">
                                    <input type="checkbox" id="confirm-bank-account" name="confirm_bank_account"
                                        class="form-check-input" value="1">
                                    <span class="form-check-label">
                                        {{ trans('core/base::layouts.i_certify_that_i_have_completed_all_steps_and_filled_in_all_information_correctly') }}.
                                    </span>
                                </label>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100">{{ trans('core/base::layouts.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectEl = document.getElementById('payment_sepay_bank');
            if (selectEl && selectEl.options.length === 2) {
                // auto select if only one bank option available
                selectEl.selectedIndex = 1;
            }
        });
    </script>
@endsection

@push('style-lib')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link media="all" type="text/css" rel="stylesheet"
        href="{{ url('/') }}/vendor/core/core/base/libraries/select2/css/select2.min.css?v=...">
@endpush
