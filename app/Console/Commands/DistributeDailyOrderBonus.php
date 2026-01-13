<?php

namespace App\Console\Commands;
use App\Models\CustomerNotification;
use Botble\Ecommerce\Models\Customer;
use Botble\Setting\Supports\SettingStore;
use Botble\Ecommerce\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\DailyBonusLog;
use Illuminate\Database\Eloquent\Builder;

class DistributeDailyOrderBonus extends Command
{
    protected $signature = 'customer:distribute-daily-bonus';
    protected $description = 'Tính 5% tổng tiền đơn hàng hoàn thành trong ngày và chia đều cho người dùng';

    public function handle()
    {
        $bonusPercent = setting('bonus_percentage');
        $now = Carbon::now();
        $today = Carbon::today();
        $this->info('Đang tính tổng doanh thu cho ngày: ' . $today->toDateString());
        $isCorrectTime = $now->hour === 23 && in_array($now->minute, haystack: [58, 59]) ;
        if (!$isCorrectTime) {
            $this->info('Lệnh chỉ chạy vào 23:59. Hiện tại là ' . $now->toDateTimeString() . ', bỏ qua.');
            return 0;
        }
        $this->info('Đang kiểm tra phân bổ tiền thưởng cho ngày: ' . $today->toDateString());
        $hasRunToday = DailyBonusLog::whereDate('distribution_date', $today->toDateString())
            ->exists();
        if ($hasRunToday) {
            $this->info('Tiền thưởng đã được chia cho ngày ' . $today->toDateString() . ', bỏ qua.');
            return 0;
        }
        $endToDay = Carbon::today()->endOfDay();
        $totalOrderAmount = Order::
            whereBetween('updated_at', [$today, $endToDay])
            ->whereHas('payment', function (Builder $query) {
                $query->where('status', 'completed');
            })
            ->sum('amount');
        if ($totalOrderAmount <= 0) {
            $this->info('Không có đơn hàng nào trong ngày hôm nay.');
            return 0;
        }
        $this->info('Tổng doanh thu trong ngày ' . $today->toDateString() . ' là :' . $totalOrderAmount,);

        $bonusAmount = $totalOrderAmount * ($bonusPercent / 100);
        $this->info('Tổng tiền thưởng trong ngày hôm nay là: ' . $bonusAmount );

        $activeCustomers = Customer::where('kyc_status', 1)->where('is_active_account', 1)->get();
        $totalActiveCustomers = $activeCustomers->count();

        if ($totalActiveCustomers == 0) {
            $this->info('Không có người dùng nào đủ điều kiện nhận thưởng.');
            return 0;
        }
        $this->info('Tổng số người dùng đủ điều kiện nhận thưởng là: ' . $totalActiveCustomers);

        $bonusPerCustomer = round($bonusAmount / $totalActiveCustomers, 2);
        $this->info('Mỗi người dùng sẽ nhận được: ' .$bonusPerCustomer);

        if ($bonusPerCustomer <= 0) {
            $this->warn('Tiền thưởng mỗi người bằng 0, không tạo thông báo hoặc cập nhật ví.');
            return 0;
        }

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            $logData = [];

            foreach ($activeCustomers as $customer) {
                // Cộng tiền thưởng vào walet_1
                $customer->walet_1 += $bonusPerCustomer;
                $customer->save();


                // Chuẩn bị dữ liệu log
                $logData[] = [
                    'customer_id' => $customer->id,
                    'bonus_amount' => $bonusPerCustomer,
                    'order_total' => $totalOrderAmount,
                    'distribution_date' => $today,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // Tạo thông báo cho khách hàng
                CustomerNotification::create([
                    'title' => 'core/base::layouts.get_daily_bonus',
                    'dessription' => 'core/base::layouts.you_have_been_credited ' . $bonusPerCustomer . ' core/base::layouts.into_wallet_from_daily_single_bonus ' . $today->format('d/m/Y') . '.',
                    'customer_id' => $customer->id,
                    // 'url' => '/wallet', // Liên kết đến trang ví
                ]);

                $updatedCount++;
                $this->info('Đã cộng thêm ' . $bonusPerCustomer) . ' vào ví của khách hàng ID: ' . $customer->id . ' (' . $customer->name . ')';
            }

            // Ghi log hàng loạt vào daily_bonus_logs
            DailyBonusLog::insert($logData);

            DB::commit();
            $this->info('Đã cập nhật tiền thưởng cho ' . $updatedCount . ' người dùng.');
            $this->info('Đã phân bổ tiền thưởng thành công cho ' . $updatedCount . ' khách hàng vào ngày ' . $today->toDateString() . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi khi phân bổ tiền thưởng: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
