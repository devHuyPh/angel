<?php

namespace FriendsOfBotble\SePay\Providers;

use Botble\Ecommerce\Models\Order;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Facades\PaymentMethods;
use FriendsOfBotble\SePay\Forms\SePayPaymentMethodForm;
use FriendsOfBotble\SePay\SePay;
use FriendsOfBotble\SePay\Services\Gateways\SePayPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerSePayMethod'], 2, 2);

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 2);

        add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($data, $payment) {
            if ($payment->payment_channel == SEPAY_PAYMENT_METHOD_NAME && $payment->metadata) {
                return view('plugins/fob-sepay::detail', compact('payment'));
            }

            return $data;
        }, 20, 2);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['SEPAY'] = SEPAY_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 2, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == SEPAY_PAYMENT_METHOD_NAME) {
                $value = 'SePay';
            }

            return $value;
        }, 2, 2);

        add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithSePay'], 11, 2);

        add_filter('ecommerce_thank_you_customer_info', function (?string $html, Collection|Order $orders) {
            if (! $orders instanceof Collection) {
                $collection = new Collection();
                $collection->add($orders);
                $orders = $collection;
            }

            $payments = $orders
                ->map(fn (Order $order) => $order->payment)
                ->filter(fn ($payment) => $payment && $payment->payment_channel->getValue() === SEPAY_PAYMENT_METHOD_NAME)
                ->filter(fn ($payment) => $payment->currency === 'VND')
                ->unique('id');

            if ($payments->isEmpty()) {
                return $html;
            }

            $paymentGroups = $payments->groupBy(function ($payment) {
                return $payment->charge_id ?: ('payment_' . $payment->getKey());
            });

            $paymentData = $paymentGroups->map(function ($paymentGroup) use ($orders) {
                $primaryPayment = $paymentGroup->first();
                $chargeId = $primaryPayment->charge_id ?: ('PAY-' . $primaryPayment->getKey());

                $paymentOrders = $orders->filter(function ($order) use ($chargeId, $primaryPayment) {
                    $payment = $order->payment;

                    if (! $payment) {
                        return false;
                    }

                    if ($payment->charge_id && $payment->charge_id === $chargeId) {
                        return true;
                    }

                    if (! $payment->charge_id && $payment->getKey() === $primaryPayment->getKey()) {
                        return true;
                    }

                    return false;
                });

                $paymentMeta = $primaryPayment->metadata ?? [];
                $walletRemaining = data_get($paymentMeta, 'wallet_remaining', data_get($paymentMeta, 'remaining_amount'));
                $orderAmount = 0;

                if ($walletRemaining !== null) {
                    $orderAmount = (float) $walletRemaining;
                } else {
                    foreach ($paymentOrders as $item) {
                        $allocationRemaining = data_get($paymentMeta, "wallet_payment.allocations.{$item->id}.remaining");

                        if ($allocationRemaining !== null) {
                            $payAmount = (float) $allocationRemaining;
                        } else {
                            $payAmount = (float) $item->amount;
                        }

                        $orderAmount += $payAmount;
                    }

                    if ($orderAmount <= 0) {
                        $orderAmount = (float) $paymentOrders->sum('amount') ?: (float) $paymentGroup->sum('amount');
                    }
                }

                $isCompleted = $paymentGroup->every(fn ($payment) => $payment->status == PaymentStatusEnum::COMPLETED);

                return [
                    'payment' => $primaryPayment,
                    'orderAmount' => $orderAmount,
                    'chargeId' => $chargeId,
                    'imageUrl' => SePay::getQRCodeUrl($orderAmount, $chargeId),
                    'isCompleted' => $isCompleted,
                ];
            });

            $html .= view(
                'plugins/fob-sepay::bank-info',
                [
                    'paymentData' => $paymentData,
                    'bank' => SePay::getBankById(get_payment_setting('bank', SEPAY_PAYMENT_METHOD_NAME)),
                    'bankAccountNumber' => get_payment_setting('account_number', SEPAY_PAYMENT_METHOD_NAME),
                    'bankAccountHolder' => get_payment_setting('account_holder', SEPAY_PAYMENT_METHOD_NAME),
                ]
            )->render();

            return $html;
        }, 9999, 2);
    }

    public function registerSePayMethod(?string $html, array $data): ?string
    {
        // Support old versions
        if (! view()->exists('plugins/payment::components.payment-method')) {
            return $html . view('plugins/fob-sepay::support-old-versions.payment-method', $data)->render();
        }

        PaymentMethods::method(SEPAY_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/fob-sepay::payments.methods', $data)->render(),
        ]);

        return $html;
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . SePayPaymentMethodForm::create()->renderForm();
    }

    public function checkoutWithSePay(array $data, Request $request): array
    {
        if ($data['type'] !== SEPAY_PAYMENT_METHOD_NAME) {
            return $data;
        }

        $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

        $data['charge_id'] = (new SePayPaymentService())->execute($paymentData);

        return $data;
    }
}
