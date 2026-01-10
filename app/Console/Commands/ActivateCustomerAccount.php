<?php

namespace App\Console\Commands;

use App\Models\CustomerNotification;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActivateCustomerAccount extends Command
{
    protected $signature = 'customer:activate-account';
    protected $description = 'Kích hoạt tài khoản khách hàng nếu có đơn hàng >= 500000đ đã hoàn thành';

    public function handle()
    {
        $customers = Customer::where('is_active_account', 0)->get();
        $this->info('Đang kiểm tra ' . $customers->count() . ' khách hàng...');

        $activatedCount = 0;

        foreach ($customers as $customer) {
            $hasQualifiedOrder = Payment::where('customer_id', $customer->id)
                ->where('status', 'completed')
                ->where('amount', '>=', (float) setting('monthly_repurchase'))
                ->exists();

            if ($hasQualifiedOrder) {
                $customer->is_active_account = 1;
                $customer->save();
                $activatedCount++;
                $this->info('Đã kích hoạt tài khoản cho Customer ID: ' . $customer->id);

                CustomerNotification::create([
                    'title' => 'core/base::layouts.your-account-is-active',
                    'dessription' => 'profit_share_received_point_wallet',
                    'variables' => json_encode([
                        'monthly_repurchase' => (float) setting('monthly_repurchase'),
                    ]),
                    'customer_id' => $customer->id,
                    'url' => '/marketing/dashboard'
                ]);
            }
        }

        $this->info('Đã kích hoạt tổng cộng ' . $activatedCount . ' tài khoản.');
        return 0;
    }
}
