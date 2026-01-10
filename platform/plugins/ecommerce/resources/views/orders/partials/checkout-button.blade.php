@if (EcommerceHelper::isValidToProcessCheckout())
    <button
        id="btn_checkout"
        class="btn payment-checkout-btn payment-checkout-btn-step float-end"
        data-processing-text="{{ __('Processing. Please wait...') }}"
        data-error-header="{{ __('Error') }}"
        type="button"
    >
        {{ __('Checkout') }}
    </button>
@else
    <span class="btn payment-checkout-btn-step float-end disabled">
        {{ __('Checkout') }}
    </span>
@endif
