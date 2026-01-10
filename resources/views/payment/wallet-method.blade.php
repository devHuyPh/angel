@php
    $orderAmount = max((float) ($orderAmount ?? 0), 0);
    $walletBalance = max((float) ($walletBalance ?? 0), 0);
    $walletUse = min($walletBalance, $orderAmount);
    $remaining = max($orderAmount - $walletUse, 0);
    $walletName = defined('WALLET_PAYMENT_METHOD_NAME') ? constant('WALLET_PAYMENT_METHOD_NAME') : 'wallet_1';
    $isSelected = PaymentMethods::getSelectingMethod() === $walletName;
    $passwordInputId = 'wallet-password-' . uniqid();
@endphp

<x-plugins-payment::payment-method
    :name="$walletName"
    :label="__('Thanh toán bằng ví rút')"
    :description="__('Sử dụng số dư ví rút để thanh toán. Nếu không đủ, hệ thống sẽ trừ hết ví và yêu cầu chuyển khoản phần còn lại.')"
>
    <div class="alert alert-info mb-3">
        <div class="d-flex justify-content-between">
            <span>{{ __('Số dư ví') }}</span>
            <strong>{{ format_price($walletBalance) }}</strong>
        </div>
        <div class="d-flex justify-content-between">
            <span>{{ __('Tổng thanh toán') }}</span>
            <strong>{{ format_price($orderAmount) }}</strong>
        </div>
        @if ($remaining > 0)
            <div class="mt-2 small">
                {{ __('Hệ thống sẽ trừ :wallet từ ví. Vui lòng chuyển khoản thêm :remaining để hoàn tất.', [
                    'wallet' => format_price($walletUse),
                    'remaining' => format_price($remaining),
                ]) }}
            </div>
        @else
            <div class="mt-2 small">
                {{ __('Số dư ví đủ để thanh toán toàn bộ đơn hàng.') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label" for="{{ $passwordInputId }}">{{ __('Mật khẩu ví') }}</label>
        <input
            type="password"
            name="wallet_password"
            id="{{ $passwordInputId }}"
            class="form-control wallet-password-input"
            placeholder="{{ __('Nhập mật khẩu đăng nhập để xác nhận') }}"
            @if ($isSelected) required @endif
            autocomplete="current-password"
        >
        <small class="text-muted">{{ __('Nhập mật khẩu để xác nhận thanh toán bằng ví rút.') }}</small>
    </div>

    <input type="hidden" name="wallet_intended_amount" value="{{ $walletUse }}">
</x-plugins-payment::payment-method>

@push('footer')
    <script>
        (function () {
            const methodName = '{{ $walletName }}';
            const radioId = `payment-${methodName}`;
            const passwordInput = document.getElementById('{{ $passwordInputId }}');

            const togglePasswordState = () => {
                if (!passwordInput) {
                    return;
                }

                const radio = document.getElementById(radioId);
                const isChecked = radio ? radio.checked : false;

                passwordInput.required = !!isChecked;
                passwordInput.disabled = !isChecked;
            };

            document.addEventListener('change', event => {
                if (event.target && event.target.name === 'payment_method') {
                    togglePasswordState();
                }
            });

            document.addEventListener('payment-form-reloaded', togglePasswordState);
            togglePasswordState();
        })();
    </script>
@endpush
