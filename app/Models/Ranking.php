<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Ranking extends Model
{
    protected $table = 'rankings';

    protected $fillable = [
        'rank_name',
        'rank_lavel',
        'ranking_description',
        'rank_lavel_unq',
        'number_referrals',
        'total_revenue',
        'min_earning',
        'rank_icon',
        'sort_by',
        'status',
        'demotion_time_months',
        'demotion_investment',
        'demotion_referrals',
        'percentage_reward',
    'ranking_date_conditions',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'rank_id');
    }

    // Lấy tổng doanh thu từ total_dowline của id = 1
    public static function getTotalSystemRevenue()
    {
        $companyAccount = Customer::find(1);
        return $companyAccount ? ($companyAccount->total_dowline ?? 0) : 0;
    }

    // Chia thưởng theo rank và cộng vào wallet_1
    public static function distributeRewards()
    {
        // Lấy tổng doanh thu từ id = 1
        $totalRevenue = self::getTotalSystemRevenue();

        // Nếu không có doanh thu, trả về mảng rỗng
        if ($totalRevenue <= 0) {
            return [];
        }

        // Lấy tất cả rank đang hoạt động, sắp xếp từ thấp đến cao
        $activeRanks = self::where('status', 1)->orderBy('id', 'asc')->get();

        // Nếu không có rank hoạt động, trả về mảng rỗng
        if ($activeRanks->isEmpty()) {
            return [];
        }

        // Lấy tất cả khách hàng có rank (trừ id = 1)
        $customers = Customer::whereNotNull('rank_id')
            ->where('id', '!=', 1)
            ->whereIn('rank_id', $activeRanks->pluck('id'))
            ->with('rank')
            ->get();

        // Nếu không có khách hàng nào đủ điều kiện, trả về mảng rỗng
        if ($customers->isEmpty()) {
            return [];
        }

        // Tạo mảng để lưu phần thưởng cho từng khách hàng (chỉ để trả về thông tin)
        $rewards = [];

        // Duyệt qua từng rank để tính phần thưởng
        foreach ($activeRanks as $rank) {
            // Tính phần thưởng của rank này
            $rankReward = $totalRevenue * ($rank->percentage_reward / 100);

            // Lọc khách hàng từ rank hiện tại trở lên
            $eligibleCustomers = $customers->filter(function ($customer) use ($rank) {
                return $customer->rank_id >= $rank->id;
            });

            $eligibleCount = $eligibleCustomers->count();

            if ($eligibleCount > 0) {
                // Chia đều phần thưởng cho số người đủ điều kiện
                $rewardPerPerson = $rankReward / $eligibleCount;

                // Cộng phần thưởng vào wallet_1 và lưu thông tin trả về
                foreach ($eligibleCustomers as $customer) {
                    // Khởi tạo thông tin trong mảng rewards nếu chưa có
                    if (!isset($rewards[$customer->id])) {
                        $rewards[$customer->id] = [
                            'name' => $customer->name ?? 'Unknown',
                            'rank_name' => $customer->rank->rank_name ?? 'Unknown',
                            'reward' => 0
                        ];
                    }

                    // Cộng dồn phần thưởng vào mảng trả về
                    $rewards[$customer->id]['reward'] += $rewardPerPerson;

                    // Cộng trực tiếp vào wallet_1 trong database
                    $customer->increment('wallet_1', $rewardPerPerson);
                }
            }
        }

        return $rewards;
    }
}
