<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Botble\Ecommerce\Models\Customer;
use App\Models\Ranking;
use Carbon\Carbon;
use App\Models\CustomerNotification;
use Illuminate\Support\Facades\DB;

class CheckPositivePoint extends Command
{
    protected $signature = 'check:positive_point';
    protected $description = 'Kiểm tra và cập nhật chuyển từ ví điểm tích cực sang ví rút';

    public function handle()
    {
        $customers = Customer::where('walet_2', '>', 0)->get();
        $now = now();

        foreach ($customers as $customer) {
            $totalPayment = $customer->payments()
                ->where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount');

            if ($totalPayment > (float) setting('monthly_repurchase')) {
                $customer->update([
                    'walet_1' => $customer->walet_1 + $customer->walet_2,
                    'walet_2' => 0
                ]);
            }
            //  else {
            //     CustomerNotification::create([
            //         'title' => 'core/base::layouts.monthly_repurchase_notification',
            //         'dessription' => 'repurchase_to_receive_balance_notification',
            //         'variables' => json_encode([
            //             'amount' => $customer->walet_2,
            //             'required' => setting('monthly_repurchase'),
            //         ]),
            //         'customer_id' => $customer->id,
            //         'url' => '/products'
            //     ]);
            // }
        }

        $this->info('Hoàn thành kiểm tra');
    }
}
