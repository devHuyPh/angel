<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConfirmVendorShipedToUser;
use Carbon\Carbon;
use Botble\Ecommerce\Models\Order;
use Botble\Marketplace\Models\Store;
use App\Models\CustomerNotification;
use App\Models\VendorNotifications;
use Botble\Base\Models\AdminNotification;




class AutoUpdateStatusAfter48h extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-update-status-48h';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động set status = 1 sau 48h nếu status != 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $auto_confirmation_time=setting('auto-confirmation-time');
        $threshold = now()->subHours($auto_confirmation_time);
        $updatedCount = 0;
        $order = ConfirmVendorShipedToUser::where('status', '!=', 1)
            ->where('created_at', '<=', $threshold)
            ->get();
        foreach ($order as $orde) {
            $getDataOrder = Order::with(['payment', 'store.customer'])->find($orde->order_id);

            if (! $getDataOrder || ! $getDataOrder->store || ! $getDataOrder->store->customer) {
                // Dữ liệu bẩn thì bỏ qua, tránh die cả job
                continue;
            }
            $store    = $getDataOrder->store;
            $customer = $store->customer;
            $amount = optional($getDataOrder->payment)->amount ?? 0;
            $fee_dis_ware_to_customer = setting('fee_distribution_warehouse_to_customer');
            $shippingFee = ($fee_dis_ware_to_customer / 100) * $amount;

            $orde->update([
                'status' => 1,
                'note'   => 'Tự động xác nhận sau 48h',
            ]);

            // 2) Cộng ví kho
            $customer->update([
                'walet_1' => $customer->walet_1 + ($orde->shipping_fee ?? 0),
            ]);
            VendorNotifications::create([
                'title' => 'core/base::layouts.shipping-user_notification',
                'description' => 'delivery_user_status_updated',
                'variables' => json_encode([
                    'text_order_id' => $getDataOrder->code,
                    'shipping_fee' => $shippingFee,
                ]),
                'vendor_id' => $store->customer->id,
                'url' => route('marketplace.vendor.store-orders.index')
            ]);
            CustomerNotification::create([
                'title' => 'core/base::layouts.shipping-user_notification',
                'dessription' => 'delivery_user_status_updated',
                'variables' => json_encode([
                    'text_order_id' => $getDataOrder->code,
                    'shipping_fee' => $shippingFee,
                ]),
                'customer_id' => $store->customer->id,
                'url' => route('marketplace.vendor.store-orders.index')
            ]);

            AdminNotification::create([
                'title' => 'Xác nhận giao hàng từ kho',
                'action_label' => 'Xem',
                'action_url' => '/admin',
                'description' => 'Hệ thống đã tự động xác nhận giao thành công đơn hàng ' . $getDataOrder->code . ' từ kho ' . $store->name
            ]);
             $updatedCount++;
        }

        $this->info("Updated {$updatedCount} orders to status = 1");

        return Command::SUCCESS;
    }
}
