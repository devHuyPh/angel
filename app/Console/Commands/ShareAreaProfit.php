<?php

namespace App\Console\Commands;

use App\Models\CustomerNotification;
use App\Models\TotalDowlineMonthHistory;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShareAreaProfit extends Command
{
  protected $signature = 'share:comrade-manager-profit';
  protected $description = 'Chia thưởng cho tất cả quản lý khu vực theo tháng';

  public function handle()
  {
    $percentSetting = setting('comrade_manager', 0);
    if ($percentSetting <= 0) {
      $this->error('Cấu hình "comrade_manager" không hợp lệ.');
      return;
    }

    $currentMonth = now()->month;
    $currentYear = now()->year;

    if (!now()->isSameDay(now()->endOfMonth())) {
        $this->info('Không phải ngày thưởng khu vực');
        return;
    }

    $customerIds = DB::table('ec_customer_manager')
      ->pluck('customer_id')
      ->unique()
      ->values();

    if ($customerIds->isEmpty()) {
      $this->info('Không có quản lý nào.');
      return;
    }

    DB::beginTransaction();
    try {
      foreach ($customerIds as $customerId) {
        // Check nếu đã thưởng tháng này rồi thì bỏ qua
        $exists = DB::table('area_bonus_histories')
          ->where('customer_id', $customerId)
          ->where('month', $currentMonth)
          ->where('year', $currentYear)
          ->exists();

        if ($exists) {
          $this->info("Customer ID {$customerId} đã được thưởng tháng {$currentMonth}/{$currentYear}, bỏ qua.");
          continue;
        }

        // Lấy danh sách state quản lý
        $stateIds = DB::table('ec_customer_manager')
          ->where('customer_id', $customerId)
          ->pluck('state_id')
          ->toArray();

        if (empty($stateIds)) {
          $this->warn("Customer ID {$customerId} không quản lý state nào.");
          continue;
        }

        // Tổng amount đơn hàng trong tháng
        $totalAmount = DB::table('ec_order_addresses as addr')
          ->join('ec_orders as orders', 'addr.order_id', '=', 'orders.id')
          ->whereIn('addr.state', $stateIds)
          ->whereMonth('orders.created_at', $currentMonth)
          ->whereYear('orders.created_at', $currentYear)
          ->sum('orders.amount');
        $this->info("Tổng doanh thu{$totalAmount}");
        if ($totalAmount <= 0) {
          $this->warn("Customer ID {$customerId} không có doanh thu tháng này.");
          continue;
        }

        // Tính tiền thưởng, làm tròn xuống
        $bonus = $totalAmount * ($percentSetting / 100);
        $roundedBonus = floor($bonus);

        if ($roundedBonus <= 0) {
          $this->warn("Customer ID {$customerId} tiền thưởng sau làm tròn xuống <= 0, bỏ qua.");
          continue;
        }

        // Ghi vào bảng thưởng
        DB::table('area_bonus_histories')->insert([
          'manager_id' => $customerId,
          'customer_id' => $customerId,
          'bonus_amount' => $roundedBonus,
          'month' => $currentMonth,
          'year' => $currentYear,
          'created_at' => now(),
          'updated_at' => now(),
        ]);

        // Cộng vào walet_1
        $affected = DB::table('ec_customers')
          ->where('id', $customerId)
          ->increment('walet_1', $roundedBonus);

        if ($affected) {
          $this->info("Customer ID {$customerId} được thưởng: " . number_format($roundedBonus) . " VNĐ và đã cộng vào ví.");
          Log::info("Area Profit Share - Customer ID {$customerId}: " . number_format($roundedBonus) . " VNĐ cộng vào ví.");


          CustomerNotification::create([
            'title' => __('core/base::layouts.area_bonus_title'),
            'dessription' => __('core/base::layouts.area_bonus_description', [
              'amount' => number_format($roundedBonus),
              'month' => $currentMonth,
              'year' => $currentYear,
            ]),
            'customer_id' => $customerId,
            'readed' => 0,
            'viewed' => 0,
            'url' => '/wallet/history',
          ]);
          $this->recordTotalDowlineHistory();

          Customer::resetAllTotalDMonth();

        } else {
          $this->warn("Customer ID {$customerId} không tồn tại trong bảng ec_customers, không cộng ví được.");
          Log::warning("Area Profit Share - Customer ID {$customerId} không tồn tại khi cộng ví.");
        }
      }

      DB::commit();
      $this->info('Đã chia thưởng cho tất cả quản lý khu vực thành công!');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Lỗi khi chia thưởng khu vực: ' . $e->getMessage());
      $this->error('Có lỗi xảy ra: ' . $e->getMessage());
    }
  }

    private function recordTotalDowlineHistory()
    {
        $previousMonth = Carbon::now()->subMonth()->month;
        $previousYear = Carbon::now()->subMonth()->year;

        DB::beginTransaction();
        try {
            $customers = Customer::whereNotNull('total_dowline_month')->get();
            foreach ($customers as $customer) {
                TotalDowlineMonthHistory::create([
                    'customer_id' => $customer->id,
                    'total_dowline' => $customer->total_dowline_month,
                    'month' => $previousMonth - 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::commit();
            Log::info('Lịch sử thu nhập đã ghi lại thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi ghi lại lịch sử thu nhập: ' . $e->getMessage());
        }
    }
}
