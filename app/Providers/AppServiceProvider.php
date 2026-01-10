<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\CustomerNotification;
use App\Models\VendorLateDelivery;
use App\Observers\OrderObserver;
use Botble\Base\Facades\Html;
use Botble\Ecommerce\Models\Customer as EcommerceCustomer;
use Botble\Ecommerce\Models\Order as EcommerceOrder;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);

        add_filter(BASE_FILTER_APPEND_MENU_NAME, function (?string $number, string $menuId): ?string {
            $className = match ($menuId) {
                'cms-core-store-kho' => 'late-delivery-count-parent',
                'cms-core-vendor-late-delivery' => 'late-delivery-count-child',
                default => null,
            };

            return $className
                ? view('core/base::partials.navbar.badge-count', ['class' => $className])->render()
                : $number;
        }, 140, 2);

        add_filter(BASE_FILTER_MENU_ITEMS_COUNT, function (array $data = []): array {
            $count = VendorLateDelivery::on('mysql')->where('status', 0)->count();

            foreach (['late-delivery-count-parent', 'late-delivery-count-child'] as $key) {
                $data[] = [
                    'key' => $key,
                    'value' => $count,
                ];
            }

            return $data;
        }, 140);

        if (! defined('WALLET_PAYMENT_METHOD_NAME')) {
            define('WALLET_PAYMENT_METHOD_NAME', 'wallet_1');
        }

        if (function_exists('is_plugin_active') && is_plugin_active('payment')) {
            $this->registerWalletPaymentHooks();
        }
    }

    protected function registerWalletPaymentHooks(): void
    {
        // Đảm bảo key config ví tồn tại mặc định
        $settingStore = setting();
        $statusKey = get_payment_setting_key('status', WALLET_PAYMENT_METHOD_NAME);
        $nameKey = get_payment_setting_key('name', WALLET_PAYMENT_METHOD_NAME);
        $descriptionKey = get_payment_setting_key('description', WALLET_PAYMENT_METHOD_NAME);

        if ($settingStore->get($statusKey) === null) {
            $settingStore
                ->set($statusKey, 1)
                ->set($nameKey, __('Wallet 1'))
                ->set($descriptionKey, __('Use wallet 1 to pay for your order'))
                ->save();
        }

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['WALLET_1'] = WALLET_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 40, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == WALLET_PAYMENT_METHOD_NAME) {
                $value = __('Wallet 1');
            }

            return $value;
        }, 40, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == WALLET_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-info status-label']
                )->toHtml();
            }

            return $value;
        }, 40, 2);

        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, function (?string $html, array $data): string {
            if (! auth('customer')->check()) {
                return $html;
            }

            $walletBalance = (float) auth('customer')->user()->walet_1;
            $orderAmount = (float) ($data['amount'] ?? 0);

            PaymentMethods::method(WALLET_PAYMENT_METHOD_NAME, [
                'html' => view('payment.wallet-method', compact('walletBalance', 'orderAmount'))->render(),
                'priority' => 100,
            ]);

            return (string) $html;
        }, 45, 2);

        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, function (array $data, Request $request): array {
                if ($request->input('payment_method') !== WALLET_PAYMENT_METHOD_NAME) {
                    return $data;
                }

                Log::warning('Wallet payment start', [
                    'customer_id' => auth('customer')->id(),
                    'order_ids' => $request->input('order_id'),
                    'amount' => $request->input('amount'),
                ]);

                if (! auth('customer')->check()) {
                    return [
                        'error' => true,
                        'message' => __('Please login to use wallet payment.'),
                    ];
                }

                $customer = EcommerceCustomer::query()->find(auth('customer')->id());

                if (! $customer) {
                    \Log::error('Wallet payment: customer not found', ['customer_id' => auth('customer')->id()]);

                    return [
                        'error' => true,
                        'message' => __('Customer not found.'),
                    ];
                }

                $walletPassword = (string) $request->input('wallet_password');

                if (! $walletPassword || ! Hash::check($walletPassword, $customer->password)) {
                    \Log::warning('Wallet payment: invalid password', ['customer_id' => $customer->getKey()]);
                    return [
                        'error' => true,
                        'message' => __('Mật khẩu ví không đúng, vui lòng thử lại.'),
                    ];
                }

                $orderIds = Arr::wrap($request->input('order_id', []));

                $orders = EcommerceOrder::query()->whereIn('id', $orderIds)->get();

                if ($orders->isEmpty()) {
                    \Log::error('Wallet payment: orders not found', ['order_ids' => $orderIds]);
                    return [
                        'error' => true,
                        'message' => __('Order not found for wallet payment.'),
                    ];
                }

                $orderTotal = $orders->sum('amount');

                if ($orderTotal <= 0) {
                    \Log::error('Wallet payment: invalid order total', ['order_total' => $orderTotal, 'order_ids' => $orderIds]);
                    return [
                        'error' => true,
                        'message' => __('Invalid order total.'),
                    ];
                }

                $chargeId = Str::upper(Str::random(12));
                $walletUsed = 0;
                $remainingAfterWallet = 0;
                $walletBalanceAfter = $customer->walet_1;

                try {
                    DB::transaction(function () use (
                        $orders,
                        $customer,
                        $orderIds,
                        $orderTotal,
                        $request,
                        $chargeId,
                        &$walletUsed,
                        &$remainingAfterWallet,
                        &$walletBalanceAfter
                    ): void {
                        $lockedCustomer = EcommerceCustomer::query()
                            ->lockForUpdate()
                            ->find($customer->getKey());

                        $currentWallet = max((float) $lockedCustomer->walet_1, 0);
                        $walletUsed = min($currentWallet, $orderTotal);

                        if ($walletUsed <= 0) {
                            $message = __('Số dư ví không đủ. Ví hiện có :wallet, cần :amount. Vui lòng nạp thêm hoặc chọn chuyển khoản.', [
                                'wallet' => format_price($currentWallet),
                                'amount' => format_price($orderTotal),
                            ]);

                            \Log::warning('Wallet payment: insufficient balance', [
                                'customer_id' => $customer->getKey(),
                                'wallet' => $currentWallet,
                                'order_total' => $orderTotal,
                            ]);

                            throw new \RuntimeException($message);
                        }

                        $lockedCustomer->walet_1 = $currentWallet - $walletUsed;
                        $lockedCustomer->save();

                        $allocations = [];
                        $walletLeft = $walletUsed;

                        foreach ($orders as $order) {
                            $useForOrder = min($walletLeft, (float) $order->amount);
                            $remainingForOrder = max((float) $order->amount - $useForOrder, 0);
                            $allocations[$order->id] = [
                                'wallet_used' => $useForOrder,
                                'remaining' => $remainingForOrder,
                            ];
                            $walletLeft -= $useForOrder;
                        }

                        $isPartial = $walletUsed < $orderTotal;

                        $remainingAfterWallet = $isPartial ? $orderTotal - $walletUsed : 0;
                        $walletBalanceAfter = $lockedCustomer->walet_1;

                        $metadata = [
                            'wallet_payment' => true,
                            'wallet_used' => $walletUsed,
                            'wallet_balance_after' => $walletBalanceAfter,
                            'remaining_amount' => $remainingAfterWallet,
                        ];

                        $paymentActionData = [
                            'amount' => $orderTotal,
                            'currency' => $request->input('currency'),
                            'charge_id' => $chargeId,
                            'order_id' => $orderIds,
                            'customer_id' => $customer->getKey(),
                            'customer_type' => EcommerceCustomer::class,
                            // payment_channel phải thuộc enum -> dùng bank_transfer và lưu thông tin ví ở metadata
                            'payment_channel' => PaymentMethodEnum::BANK_TRANSFER,
                            'status' => $isPartial ? PaymentStatusEnum::PENDING : PaymentStatusEnum::COMPLETED,
                            'metadata' => $metadata,
                            'wallet_payment' => [
                                'allocations' => $allocations,
                                'wallet_used' => $walletUsed,
                                'remaining' => $remainingAfterWallet,
                            ],
                        ];

                        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, $paymentActionData);

                        foreach ($orders as $order) {
                            $allocation = Arr::get($allocations, $order->id, ['wallet_used' => 0, 'remaining' => $order->amount]);

                            CustomerNotification::query()->create([
                                'title' => 'wallet_payment',
                                'dessription' => 'Thanh toán ví 1: :amount. Số dư ví còn :balance.',
                                'variables' => json_encode([
                                    'amount' => format_price($allocation['wallet_used']),
                                    'balance' => format_price($walletBalanceAfter),
                                ]),
                                'customer_id' => $customer->getKey(),
                                'url' => '/marketing/wallet-history',
                            ]);

                            OrderHistory::query()->create([
                                'action' => 'wallet_payment',
                                'description' => sprintf(
                                    'Thanh toán ví 1: %s, cần chuyển khoản thêm: %s',
                                    format_price($allocation['wallet_used']),
                                    format_price($allocation['remaining'])
                                ),
                                'order_id' => $order->getKey(),
                            ]);
                        }
                    });
                } catch (Throwable $exception) {
                    $message = $exception->getMessage() ?: __('Lỗi thanh toán bằng ví. Vui lòng thử lại hoặc chọn chuyển khoản.');

                    \Log::error('Wallet payment error', [
                        'message' => $message,
                        'trace' => $exception->getTraceAsString(),
                        'customer_id' => $customer->getKey(),
                        'orders' => $orderIds,
                    ]);

                    return [
                        'error' => true,
                        'message' => $message,
                    ];
                }

                $data['error'] = false;
                $data['charge_id'] = $chargeId;
                $data['message'] = $remainingAfterWallet > 0
                    ? __('Ví đã trừ :wallet, vui lòng chuyển khoản thêm :remaining để hoàn tất đơn hàng.', [
                        'wallet' => format_price($walletUsed),
                        'remaining' => format_price($remainingAfterWallet),
                    ])
                    : __('Đã thanh toán toàn bộ bằng ví.');

                Log::warning('Wallet payment success', [
                    'charge_id' => $chargeId,
                    'wallet_used' => $walletUsed,
                    'remaining' => $remainingAfterWallet,
                    'order_ids' => $orderIds,
                ]);

                return $data;
            }, 40, 2);
        });
    }
}
