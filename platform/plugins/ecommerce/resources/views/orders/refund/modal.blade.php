<x-core::form :url="$url">
    <div class="table-responsive mb-3">
        <x-core::table :striped="false" :hover="false">
            <x-core::table.header>
                <x-core::table.header.cell>
                    {{ trans('plugins/ecommerce::products.form.product') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell>
                    {{ trans('plugins/ecommerce::products.form.price') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell>
                    {{ trans('plugins/ecommerce::products.form.quantity') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell>
                    {{ trans('plugins/ecommerce::products.form.restock_quantity') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell>
                    {{ trans('plugins/ecommerce::products.form.remain') }}
                </x-core::table.header.cell>
            </x-core::table.header>

            <x-core::table.body>
                @foreach ($order->products as $orderProduct)
                    @php
                        $product = $orderProduct->product->original_product;
                    @endphp

                    <x-core::table.body.row>
                        <x-core::table.body.cell>
                            <a
                                href="{{ $product && $product->id && Auth::user()->hasPermission('products.edit') ? route('products.edit', $product->id) : '#' }}"
                                title="{{ $orderProduct->product_name }}"
                                target="_blank"
                            >
                                {{ $orderProduct->product_name }}
                            </a>
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            {{ format_price($orderProduct->price) }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            {{ $orderProduct->qty }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            {{ $orderProduct->restock_quantity }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            @if ($orderProduct->qty - $orderProduct->restock_quantity > 0)
                                <input
                                    class="j-refund-quantity form-control form-control-sm w-50"
                                    name="products[{{ $orderProduct->product_id }}]"
                                    type="number"
                                    value="{{ $orderProduct->qty - $orderProduct->restock_quantity }}"
                                    min="0"
                                />
                            @endif
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
    </div>

    @php
        $paymentMeta = $order->payment->metadata ?? [];
        $walletRemaining = (float) \Illuminate\Support\Arr::get(
            $paymentMeta,
            'wallet_remaining',
            \Illuminate\Support\Arr::get($paymentMeta, 'remaining_amount', \Illuminate\Support\Arr::get($paymentMeta, 'remaining', 0))
        );
        $rawWalletUsed = (float) \Illuminate\Support\Arr::get($paymentMeta, 'wallet_used', 0);
        $isWalletPayment = \Illuminate\Support\Arr::get($paymentMeta, 'wallet_payment', false)
            || $walletRemaining > 0
            || $rawWalletUsed > 0;
        $walletUsed = $isWalletPayment
            ? ($rawWalletUsed > 0 ? $rawWalletUsed : max(0, (float) $order->amount - $walletRemaining))
            : 0;
        $walletRefundedAmount = (float) \Illuminate\Support\Arr::get($paymentMeta, 'wallet_refunded_amount', 0);
        $walletRefundable = $isWalletPayment ? max(0, $walletUsed - $walletRefundedAmount) : 0;
        $remainingAmount = $walletRemaining;
        $availableRefundAmount = max(0, $order->payment->amount - $order->payment->refunded_amount);
        $defaultRefundAmount = $isWalletPayment
            ? min($walletRefundable, $availableRefundAmount)
            : $availableRefundAmount;
        $showRefundForm = is_plugin_active('payment')
            && $availableRefundAmount > 0
            && (
                $order->payment->status !== \Botble\Payment\Enums\PaymentStatusEnum::PENDING
                || $isWalletPayment
            );
    @endphp

    @if ($isWalletPayment)
        <div class="alert alert-info mb-3">
            <strong>{{ trans('plugins/ecommerce::order.wallet_refund_detail_title') }}</strong>
            <p class="mb-1 small">
                {{ trans('plugins/ecommerce::order.wallet_refund_detail_description', [
                    'used' => format_price($walletUsed),
                    'refundable' => format_price($walletRefundable),
                ]) }}
            </p>
            @if ($remainingAmount > 0)
                <p class="mb-0 small text-muted">
                    {{ trans('plugins/ecommerce::order.wallet_refund_detail_remaining_transfer', [
                        'remaining' => format_price($remainingAmount),
                    ]) }}
                </p>
            @else
                <p class="mb-0 small text-muted">
                    {{ trans('plugins/ecommerce::order.wallet_refund_detail_no_transfer') }}
                </p>
            @endif
        </div>
    @endif

    @if ($order->products->sum('qty') - $order->products->sum('restock_quantity') > 0)
        <div class="d-flex justify-content-end mb-3">
            <x-core::form.checkbox :checked="true">
                <x-slot:label>
                    <span>
                        {!! trans(
                            'plugins/ecommerce::order.restock_products',
                            ['count' => '<span class="total-restock-items"">' . $order->products->sum('qty') - $order->products->sum('restock_quantity') . '</span>']
                        )!!}
                    </span>
                </x-slot:label>
            </x-core::form.checkbox>
        </div>
    @endif

    <x-core::table :striped="false" :hover="false">
        <x-core::table.body>
            <x-core::table.body.row class="text-end">
                <x-core::table.body.cell>
                    {{ trans('plugins/ecommerce::products.form.shipping_fee') }}
                </x-core::table.body.cell>
                <x-core::table.body.cell>
                    {{ format_price($order->shipping_amount) }}
                </x-core::table.body.cell>
            </x-core::table.body.row>

            @if (is_plugin_active('payment') && $order->payment->refunded_amount)
                <x-core::table.body.row class="text-end">
                    <x-core::table.body.cell>
                        {{ trans('plugins/ecommerce::order.total_refund_amount') }}
                    </x-core::table.body.cell>
                    <x-core::table.body.cell>
                        {{ format_price($order->payment->refunded_amount) }}
                    </x-core::table.body.cell>
                </x-core::table.body.row>
            @endif
            <x-core::table.body.row class="text-end">
                <x-core::table.body.cell>
                    {{ trans('plugins/ecommerce::order.total_amount_can_be_refunded') }}
                </x-core::table.body.cell>
                <x-core::table.body.cell>
                    @if (!is_plugin_active('payment') || $order->payment->status == Botble\Payment\Enums\PaymentStatusEnum::PENDING)
                        <span>{{ format_price(0) }}</span>
                    @else
                        <span>{{ format_price($order->payment->amount - $order->payment->refunded_amount) }}</span>
                    @endif
                </x-core::table.body.cell>
            </x-core::table.body.row>
        </x-core::table.body>
    </x-core::table>

    @if ($showRefundForm)
        <div class="d-flex justify-content-between align-items-center my-3">
            <div class="d-flex align-items-center gap-2">
                <x-core::icon name="ti ti-credit-card" size="md" />
                <div>
                    {{ $order->payment->payment_channel->label() }}
                    @if (get_payment_is_support_refund_online($order->payment))
                        <p class="text-muted small mb-0">
                            {{ trans('plugins/ecommerce::order.payment_method_refund_automatic', [
                                'method' => $order->payment->payment_channel->label(),
                            ]) }}
                        </p>
                    @endif
                </div>
            </div>
            <div>
                <div class="input-group input-group-flat">
                    <input
                        class="input-mask-number input-sync-item form-control"
                        name="refund_amount"
                        data-thousands-separator="{{ EcommerceHelper::getThousandSeparatorForInputMask() }}"
                        data-decimal-separator="{{ EcommerceHelper::getDecimalSeparatorForInputMask() }}"
                        data-target=".refund-amount-text"
                        type="text"
                        value="{{ $defaultRefundAmount }}"
                    >
                    <span class="input-group-text">{{ get_application_currency()->symbol }}</span>
                </div>
                @if ($walletRefundable > 0)
                    <p class="text-muted small mt-1">
                        {{ trans('plugins/ecommerce::order.wallet_refund_max_amount_note', [
                            'wallet_refundable' => format_price($walletRefundable),
                        ]) }}
                    </p>
                @endif
                <p id="wallet-amount-warning" class="text-warning small mt-1 @if ($walletRefundable >= $defaultRefundAmount) d-none @endif">
                    {{ trans('plugins/ecommerce::order.wallet_refund_hint_over_limit', ['wallet_refundable' => format_price($walletRefundable)]) }}
                </p>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const input = document.querySelector('input[name="refund_amount"]');
                const warningMessage = document.getElementById('wallet-amount-warning');
                const walletMax = parseFloat("{{ $walletRefundable }}");

                if (!input || !warningMessage) {
                    return;
                }

                const warningText = {!! json_encode(trans('plugins/ecommerce::order.wallet_refund_hint_over_limit', ['wallet_refundable' => format_price($walletRefundable)])) !!};

                const syncWarning = () => {
                    const value = parseFloat(input.value.replace(/[^0-9.]/g, '')) || 0;
                    if (walletMax > 0 && value > walletMax) {
                        warningMessage.classList.remove('d-none');
                        warningMessage.textContent = warningText;
                    } else {
                        warningMessage.classList.add('d-none');
                    }
                };

                input.addEventListener('input', syncWarning);
                syncWarning();
            });
        </script>
    @endif

    <x-core::form.text-input
        :label="trans('plugins/ecommerce::order.refund_reason')"
        name="refund_note"
        :value="$order->payment->refund_note"
    />
</x-core::form>
