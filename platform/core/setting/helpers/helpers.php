<?php

use App\Models\CustomerNotification;
use App\Models\ProfitHistory;
use App\Models\ReferralCommission;
use App\Models\TotalDowlineDayHistory;
use Botble\Base\Facades\BaseHelper;
use Botble\Ecommerce\Models\Customer;
use Botble\Setting\Facades\Setting;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Botble\Payment\Enums\PaymentStatusEnum;

if (! function_exists('setting')) {
    function setting(?string $key = null, $default = null)
    {
        if (! empty($key)) {
            try {
                return app(SettingStore::class)->get($key, $default);
            } catch (Throwable) {
                return $default;
            }
        }

        return Setting::getFacadeRoot();
    }
}

if (! function_exists('affiliate')) {
    function affiliate($payment)
    {
//         $status = $payment?->status;
//         $statusValue = is_object($status) && method_exists($status, 'getValue')
//     ? $status->getValue()
//     : (string) $status;
//         $ctx = [
//             'payment_id' => data_get($payment, 'id'),
//             'payment_status' => data_get($payment, 'status'),
//             'payment_amount' => (float) data_get($payment, 'amount', 0),
//             'order_id' => data_get($payment, 'order.id'),
//             'order_amount' => (float) data_get($payment, 'order.amount', 0),
//             'buyer_id' => data_get($payment, 'order.user.id'),
//         ];

//         Log::info('[AFF] begin', $ctx);

//       if (! $payment || $statusValue !== PaymentStatusEnum::COMPLETED) {
//     Log::warning('[AFF] skip - payment not completed', [
//         'payment_id' => data_get($payment, 'id'),
//         'status_value' => $statusValue,
//     ]);
//     return;
// }
        $user = $payment->order->user;
        $orderAmount = (float) $payment->order->amount;
        $order = $payment->order;
        
        // Log::info('[AFF] resolved buyer/order', $ctx + [
        //     'buyer_id' => $user?->id,
        //     'buyer_referral_ids' => $user?->referral_ids,
        //     'buyer_rank_id' => $user?->rank_id,
        // ]);
        if (!empty($user->getAttributes())) {
                    $beforeBuyer = [
            'total_dowline' => (float) $user->total_dowline,
            'total_dowline_day' => (float) $user->total_dowline_day,
            'total_dowline_month' => (float) $user->total_dowline_month,
            'total_dowline_on_rank' => (float) $user->total_dowline_on_rank,
            'rank_id' => $user->rank_id,
        ];

        // Log::info('[AFF] buyer totals BEFORE', $ctx + ['buyer' => $beforeBuyer]);

            $user->update([
                'total_dowline' => (float) $user->total_dowline + $orderAmount,
                'total_dowline_day' => (float) $user->total_dowline_day + $orderAmount,
                'total_dowline_month' => (float) $user->total_dowline_month + $orderAmount
            ]);
            if ($user->rank_id) {
                $user->update([
                    'total_dowline_on_rank' => (float) $user->total_dowline_on_rank + $orderAmount
                ]);
            }
            $user->refresh();
             $afterBuyer = [
            'total_dowline' => (float) $user->total_dowline,
            'total_dowline_day' => (float) $user->total_dowline_day,
            'total_dowline_month' => (float) $user->total_dowline_month,
            'total_dowline_on_rank' => (float) $user->total_dowline_on_rank,
            'rank_id' => $user->rank_id,
        ];

        // Log::info('[AFF] buyer totals AFTER', $ctx + ['buyer' => $afterBuyer]);

            CustomerNotification::create([
                'title' => 'core/base::layouts.downline_total_income_notification',
                'dessription' => 'customer_payment_success_notification',
                'variables' => json_encode([
                    'amount' => $payment->amount,
                    'total_dowline' => $user->total_dowline,
                ]),
                'customer_id' => $user->id,
                'url' => '/marketing/dashboard'
            ]);

             $rankBefore = $user->rank_id;
        // Log::info('[AFF] buyer updateRank START', $ctx + ['rank_before' => $rankBefore, 'rank_assigned_at' => $user->rank_assigned_at]);

        // try {
            $user->updateRank();
        // } catch (\Throwable $e) {
            // Log::error('[AFF] buyer updateRank ERROR: ' . $e->getMessage(), $ctx + ['trace' => substr($e->getTraceAsString(), 0, 2000)]);
        // }

        $user->refresh();
        // Log::info('[AFF] buyer updateRank END', $ctx + ['rank_after' => $user->rank_id, 'rank_assigned_at' => $user->rank_assigned_at]);
            // $user->updateTotalRevenue();

            $user->refresh();
            // if($user->rank_assigned_at != null){
            //     $user->update([
            //         'total_dowline_on_rank' => (float) $user->total_dowline_on_rank + $orderAmount
            //     ]);
            // }

            $commission = (float) setting('direct-referral-commission') * $orderAmount / 100; // 10%
            $referrer = $user->referrer;
            // dd($referrer);
            $level = 1;

            while ($referrer) { // Lặp đến người giới thiệu cuối cùng
                // Cộng total_dowline cho tất cả các referrer
                $refBefore = [
                'referrer_id' => $referrer->id,
                'level' => $level,
                'commission' => $commission,
                'referrer_rank_id' => $referrer->rank_id,
                'referrer_assigned_at' => $referrer->rank_assigned_at,
                'referrer_total_dowline' => (float) $referrer->total_dowline,
            ];

            // Log::info('[AFF] referrer LOOP BEFORE', $ctx + $refBefore);

                $referrer->update([
                    'total_dowline' => (float) $referrer->total_dowline + $orderAmount,
                    'total_dowline_day' => (float) $referrer->total_dowline_day + $orderAmount,
                    'total_dowline_month' => (float) $referrer->total_dowline_month + $orderAmount
                ]);

                if ($referrer->rank_id) {
                    $referrer->update([
                        'total_dowline_on_rank' => (float) $referrer->total_dowline_on_rank + $orderAmount
                    ]);
                }
                $referrer->refresh();
            //     Log::info('[AFF] referrer totals AFTER add', $ctx + [
            //     'referrer_id' => $referrer->id,
            //     'referrer_total_dowline' => (float) $referrer->total_dowline,
            //     'referrer_total_dowline_on_rank' => (float) $referrer->total_dowline_on_rank,
            // ]);
                
                // CustomerNotification::create([
                //     'title' => 'core/base::layouts.downline_total_income_notification',
                //     'dessription' => 'F' . $level . ' core/base::layouts.has_successfully_paid_for_an_order_worth ' . format_price($orderAmount) . ' core/base::layouts.total_downline_income_is ' . format_price($referrer->total_dowline),
                //     'customer_id' => $referrer->id,
                //     'url' => '/bitsgold/dashboard'
                // ]);

                CustomerNotification::create([
                    'title' => 'core/base::layouts.downline_total_income_notification',
                    'dessription' => 'referrer_payment_success_total_dowline_notification',
                    'variables' => json_encode([
                        'text_level' => $level,
                        'amount' => $payment->amount,
                        'total_dowline' => $referrer->total_dowline,
                    ]),
                    'customer_id' => $referrer->id,
                    'url' => '/marketing/dashboard'
                ]);

                 $rankBeforeRef = $referrer->rank_id;
            // Log::info('[AFF] referrer updateRank START', $ctx + ['referrer_id' => $referrer->id, 'rank_before' => $rankBeforeRef]);

            // try {
                $referrer->updateRank();
            // } catch (\Throwable $e) {
            //     Log::error('[AFF] referrer updateRank ERROR: ' . $e->getMessage(), $ctx + [
            //         'referrer_id' => $referrer->id,
            //         'trace' => substr($e->getTraceAsString(), 0, 2000),
            //     ]);
            // }

            $referrer->refresh();
            // Log::info('[AFF] referrer updateRank END', $ctx + [
            //     'referrer_id' => $referrer->id,
            //     'rank_after' => $referrer->rank_id,
            //     'rank_assigned_at' => $referrer->rank_assigned_at,
            // ]);
                // dd($referrer->updateRank());

                // Chỉ cộng wallet_1 nếu hoa hồng >= 10000
                if ($commission >= 1000) {
                    $totalPayment = $referrer->payments()
                        ->where('status', 'completed')
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('amount');
                //      Log::info('[AFF] referrer commission eligible', $ctx + [
                //     'referrer_id' => $referrer->id,
                //     'commission' => $commission,
                //     'totalPaymentThisMonth' => $totalPayment,
                //     'monthly_repurchase' => (float) setting('monthly_repurchase'),
                // ]);
                    $dessription = '';

                    if ($totalPayment < (float) setting('monthly_repurchase')) {
                        $referrer->walet_2 += $commission;
                        $referrer->save();

                        // $dessription = 'F' . $level . ' core/base::layouts.has_successfully_paid_for_an_order_worth ' . format_price($orderAmount) . ' core/base::layouts.your_earnings_wallet_balance_is ' . format_price($referrer->walet_2);
                        $dessription = 'referrer_payment_success_wallet2_notification';
                        $variables = json_encode([
                            'amount' => $commission,
                            'text_level' => $level,
                        ]);
                    } else {
                //         Log::info('[AFF] referrer commission skipped (<1000)', $ctx + [
                //     'referrer_id' => $referrer->id,
                //     'commission' => $commission,
                // ]);
                        $referrer->walet_1 += $commission;
                        $referrer->save();

                        $dessription = 'referrer_payment_success_wallet1_notification';
                        $variables = json_encode([
                            'amount' => $commission,
                            'text_level' => $level,
                            'balence' => $referrer->walet_1,
                        ]);
                    }
                    // dd($totalPayment);


                    ReferralCommission::create([
                        'order_id' => $order->id,
                        'customer_id' => $referrer->id,
                        'level' => $level,
                        'commission_amount' => $commission,
                        'percentage' => 6,
                    ]);

                    $referrer->refresh();

                    CustomerNotification::create([
                        'title' => 'core/base::layouts.commission_notification_for_order',
                        'dessription' => $dessription,
                        'variables' => $variables,
                        'customer_id' => $referrer->id,
                        'url' => '/marketing/dashboard'
                    ]);

                    ProfitHistory::create([
                        'recipient_id' => $referrer->id,
                        'referrer_id' => $user->id,
                        'amount' => $commission
                    ]);
                }

                // Giảm hoa hồng còn 50%
                $commission = $commission * (float) setting('indirect-referral-commission') / 100; // 50%

                // Di chuyển đến người giới thiệu tiếp theo
                $referrer = $referrer->referrer;
                $level++;
                // dd($referrer);
            }

            $mainCustomer = Customer::where('id', 1)->first();

            if (!$mainCustomer) {
                return false;
            }

            $total_dowline_day_history = TotalDowlineDayHistory::whereDate('created_at', Carbon::today())
                ->where('customer_id', 1)
                ->first();

            if ($total_dowline_day_history) {
                $total_dowline_day_history->update([
                    'total_dowline' => $mainCustomer->total_dowline_day
                ]);
            } else {
                TotalDowlineDayHistory::create([
                    'customer_id' => $mainCustomer->id,
                    'total_dowline' => $mainCustomer->total_dowline_day
                ]);
            }
        }
    }
}

if (! function_exists('get_admin_email')) {
    function get_admin_email(): Collection
    {
        $email = setting('admin_email');

        if (! $email) {
            return collect();
        }

        $email = is_array($email) ? $email : (array) json_decode($email, true);

        return collect(array_filter($email));
    }
}

if (! function_exists('get_setting_email_template_content')) {
    function get_setting_email_template_content(string $type, string $module, string $templateKey): string
    {
        $defaultPath = platform_path($type . '/' . $module . '/resources/email-templates/' . $templateKey . '.tpl');
        $storagePath = get_setting_email_template_path($module, $templateKey);

        if ($storagePath != null && File::exists($storagePath)) {
            return BaseHelper::getFileData($storagePath, false);
        }

        return File::exists($defaultPath) ? BaseHelper::getFileData($defaultPath, false) : '';
    }
}

if (! function_exists('get_setting_email_template_path')) {
    function get_setting_email_template_path(string $module, string $templateKey): string
    {
        $template = apply_filters('setting_email_template_path', "$module/$templateKey.tpl", $module, $templateKey);

        return storage_path('app/email-templates/' . $template);
    }
}

if (! function_exists('get_setting_email_subject_key')) {
    function get_setting_email_subject_key(string $type, string $module, string $templateKey): string
    {
        $key = $type . '_' . $module . '_' . $templateKey . '_subject';

        return apply_filters('setting_email_subject_key', $key, $module, $templateKey);
    }
}

if (! function_exists('get_setting_email_subject')) {
    function get_setting_email_subject(string $type, string $module, string $templateKey): string
    {
        return setting(
            get_setting_email_subject_key($type, $module, $templateKey),
            trans(
                config(
                    $type . '.' . $module . '.email.templates.' . $templateKey . '.subject',
                    ''
                )
            )
        );
    }
}

if (! function_exists('get_setting_email_status_key')) {
    function get_setting_email_status_key(string $type, string $module, string $templateKey): string
    {
        return $type . '_' . $module . '_' . $templateKey . '_' . 'status';
    }
}

if (! function_exists('get_setting_email_status')) {
    function get_setting_email_status(string $type, string $module, string $templateKey): string
    {
        $default = config($type . '.' . $module . '.email.templates.' . $templateKey . '.enabled', true);

        return setting(get_setting_email_status_key($type, $module, $templateKey), $default);
    }
}
