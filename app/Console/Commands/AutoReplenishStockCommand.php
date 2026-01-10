<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\{MpStore, StoreLevel, Product, StoreOrder, OrderStoreProduct, VendorNotifications};

class AutoReplenishStockCommand extends Command
{
  protected $signature = 'stock:auto-replenish';
  protected $description = 'Tự động kiểm tra và tạo đơn bù kho theo tỉnh và cấp độ';

  public function handle()
  {
    $this->info("\n--- Bắt đầu cron bù kho ---");

    $storeLevelValues = StoreLevel::pluck('value', 'id');
    $stores = MpStore::with(['products', 'level'])->get();

    foreach ($stores as $store) {
      $products = $store->products;
      $totalValue = $products->sum(fn($p) => $p->quantity * $p->price);
      $levelValue = $storeLevelValues[$store->store_level_id] ?? null;

      if (!$levelValue) {
        $this->warn("⚠️ Store #{$store->id} không có store_level hợp lệ, bỏ qua...");
        continue;
      }

      $threshold = $levelValue * 0.5;

      if ($totalValue >= $threshold)
        continue;

      $amountToReplenish = $levelValue - $totalValue;

      $upperStore = MpStore::where('state', $store->state)
        ->where('store_level_id', '>', $store->store_level_id)
        ->orderBy('store_level_id', 'asc')
        ->first();

      if (!$upperStore) {
        // Nếu không tìm thấy trong tỉnh, tìm tỉnh gần nhất có kho cấp cao hơn
        $upperStore = MpStore::where('store_level_id', '>', $store->store_level_id)
          ->orderByRaw("ABS(state - {$store->state})") // tìm tỉnh gần nhất theo mã GHN
          ->orderBy('store_level_id', 'asc')
          ->first();
      }

      $fromStoreId = $upperStore?->id;
      if (!$fromStoreId || $store->store_level_id == 3) {
        $fromStoreId = null; // Gửi admin
      } else{
        $storeFrom = MpStore::with('customer')->find($fromStoreId);
        $customerFrom = $storeFrom?->customer;

        if ($customerFrom) {
          VendorNotifications::create([
            'title' => 'core/base::layouts.request_sending_offset_goods_notification',
            'description' => 'Bạn có yêu cầu gửi đơn hàng bù kho cho sản phẩm',
            'variables' => null,
            'vendor_id' => $customerFrom->id,
            'url' => route('marketplace.vendor.store-orders.index'),
          ]);
        }
      }

      $hasPending = StoreOrder::where('to_store', $store->id)
        ->where('status', 'pending')
        ->whereNull('confirm_date')
        ->where('type', 1) // Chỉ kiểm tra đơn bù
        ->exists();

      if ($hasPending)
        continue;

      DB::transaction(function () use ($store, $products, $amountToReplenish, $fromStoreId) {
        $transactionCode = 'AUTO' . now()->format('YmdHis') . $store->id;

        $storeOrder = StoreOrder::create([
          'type' => 1, // Đơn bù
          'from_store' => $fromStoreId,
          'to_store' => $store->id,
          'status' => 'pending',
          'confirm_date' => null,
          'transaction_code' => $transactionCode,
          'amount' => $amountToReplenish,
        ]);

        $totalUnitPrice = $products->sum(fn($p) => $p->price);

        foreach ($products as $product) {
          if ($product->price <= 0)
            continue;

          // Tính tỷ lệ giá của sản phẩm này trên tổng đơn giá
          $ratio = $product->price / $totalUnitPrice;

          // Phân bổ số tiền cho sản phẩm này
          $allocAmount = $amountToReplenish * $ratio;

          // Số lượng cần bù cho sản phẩm này
          $missingQty = floor($allocAmount / $product->price);

          if ($missingQty > 0) {
            OrderStoreProduct::create([
              'order_store_id' => $storeOrder->id,
              'product_id' => $product->id,
              'qty' => $missingQty,
            ]);
          }
        }
      });

      $this->info("\n✔️ Đã tạo đơn bù kho cho kho #{$store->id} (from_store: " . ($fromStoreId ?? 'ADMIN') . ", amount: " . number_format($amountToReplenish) . ")");
    }

    $this->info("--- Kết thúc cron bù kho ---\n");
  }
}
