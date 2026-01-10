@if (is_plugin_active('payment') && $orderAmount)
    @php
        $paymentMethods =
            apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                'amount' => format_price($orderAmount, null, true),
                'currency' => strtoupper(get_application_currency()->title),
                'name' => null,
                'selected' => PaymentMethods::getSelectedMethod(),
                'default' => PaymentMethods::getDefaultMethod(),
                'selecting' => PaymentMethods::getSelectingMethod(),
            ]) . PaymentMethods::render();

        if (auth('customer')->check()) {
            $walletName = defined('WALLET_PAYMENT_METHOD_NAME') ? constant('WALLET_PAYMENT_METHOD_NAME') : 'wallet_1';
            $walletBalance = (float) auth('customer')->user()->walet_1;
            $walletHtml = view('payment.wallet-method', [
                'walletBalance' => $walletBalance,
                'orderAmount' => $orderAmount,
            ])->render();

            $paymentId = 'payment-' . $walletName;

            if (! \Illuminate\Support\Str::contains($paymentMethods, $paymentId)) {
                $paymentMethods = $walletHtml . $paymentMethods;
            }
        }
    @endphp

    <input name="currency" type="hidden" value="{{ strtoupper(get_application_currency()->title) }}">

    @if ($paymentMethods)
        <div class="position-relative mb-4">
            <div class="payment-info-loading loading-spinner" style="display: none"></div>
            <h5 class="checkout-payment-title">{{ __('Payment method') }}</h5>

            {!! apply_filters(PAYMENT_FILTER_PAYMENT_PARAMETERS, null) !!}

            <ul class="list-group list_payment_method">
                {!! $paymentMethods !!}
            </ul>
        </div>
    @endif
@endif

{{-- @if ($wallet_2)
    <div class="position-relative mb-4">
        <div class="payment-info-loading loading-spinner" style="display: none"></div>

        <ul class="list-group">
            <li class="list-group-item payment-method-item">
                <input class="magic-checkbox" type="checkbox" id="use_wallet_2" value = "{{ $wallet_2 }}">
                <label for="use_wallet_2" class="form-label">
                    Sử dụng điểm
                </label>

                <div class="payment_collapse_wrap collapse mt-1 show">
                    <p class="text-muted">
                        Bạn sẽ sử dụng {{ number_format($wallet_2, 0, ',', '.') }} điểm, tương đương với
                        {{ format_price($wallet_2) }}
                    </p>
                </div>

                <div class="payment-method-logo">
                    <img src="https://newskysuckhoevang.com.vn/storage/payments/cod.png" data-bb-lazy="true"
                        loading="lazy">
                </div>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let checkbox = document.getElementById("use_wallet_2");

            if (localStorage.getItem("use_wallet_2") === "true") {
                checkbox.checked = true;
            }

            checkbox.addEventListener("change", function() {
                localStorage.setItem("use_wallet_2", this.checked);
            });
        });
    </script>
@else
    <input hidden class="" type="" id="use_wallet_2" value = "0">
@endif --}}
