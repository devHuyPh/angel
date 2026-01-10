<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VendorLateDelivery;
use App\Models\VendorNotifications;
use Botble\Marketplace\Models\Store;
use Botble\Ecommerce\Models\Order;
use Carbon\Carbon;

class CheckLateDeliveries extends Command
{
    // 5 phút 1 lần
    protected $signature = 'orders:check-late-deliveries';
    protected $description = 'Check for orders pending over 24 hours and mark as late delivery';

    public function handle()
    {
        $cutoff = Carbon::now()->subHours(24);

        $orders = Order::where('status', 'pending')
            ->where('is_confirmed', 0)
            ->where('created_at', '<=', $cutoff)
            ->where('store_id', '!=', null)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Chưa có order muộn 24h');
            return;
        }

        $count = 0;

        foreach ($orders as $order) {
            $exists = VendorLateDelivery::where('order_id', $order->id)->exists();

            if (!$exists) {
                VendorLateDelivery::create([
                    'store_id'    => $order->store_id,
                    'order_id'    => $order->id,
                    'customer_id' => $order->user_id,
                    'status'      => 0,
                ]);

                $store = Store::find($order->store_id);

                if (!$store || !$store->customer) {
                    $this->warn("⚠️ Store or customer not found for order ID: {$order->id}, store ID: {$order->store_id}");
                    continue;
                }

                VendorNotifications::create([
                    'title' => 'core/base::layouts.shipping-user_notification',
                    'description' => 'CheckLateDeliveries_notification',
                    'variables' => json_encode([
                        'text_order_code' => $order->code,
                    ]),
                    'vendor_id' => $store->customer->id,
                    'url' => route('marketplace.vendor.store-orders.index')
                ]);

                $order->no_change = 1;
                $order->save();

                $count++;
            }
        }

        $this->info("✅ $count late deliveries inserted.");
    }
}
