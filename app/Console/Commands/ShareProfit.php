<?php

namespace App\Console\Commands;

use App\Models\CustomerNotification;
use App\Models\TotalDowlineMonthHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Botble\Ecommerce\Models\Customer;
use App\Models\Ranking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\RewardHistory;

class ShareProfit extends Command
{
    protected $signature = 'share:profit';
    protected $description = 'Chia tổng doanh thu theo rank';

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        $now = Carbon::now();

        $alreadyShared = RewardHistory::where('date_reward', 'like', "$today%")->exists();
        $isCorrectTime = $now->hour === 23 && in_array($now->minute, haystack: [58, 59]);
        // $this->info(RewardHistory::where('date_reward', $today));

        if (!$isCorrectTime) {
            $this->info('Lệnh chỉ chạy vào 23:59. Hiện tại là ' . $now->toDateTimeString() . ', bỏ qua.');
            return 0;
        }

        if ($alreadyShared) {
            $this->info("Lợi nhuận đã được chia trong ngày hôm nay! Không thực hiện lại.");
            return;
        }

        $mainCustomer = Customer::find(1);
        if (!$mainCustomer) {
            $this->error('Không tìm thấy customer ID = 1');
            return;
        }

        $totalFunds = $mainCustomer->total_dowline_day;
        $this->info($totalFunds);

        if ($totalFunds <= 0) {
            $this->info('Không có số tiền để chia hôm nay!');
            return;
        }

        $ranks = Ranking::orderBy('rank_lavel', 'desc')->get();
        $rankUserCounts = [];
        $cumulativeUserCounts = [];

        $totalUsers = 0;
        foreach ($ranks as $rank) {
            $rankUserCounts[$rank->id] = Customer::where('rank_id', $rank->id)->count();
            $totalUsers += $rankUserCounts[$rank->id];
            $cumulativeUserCounts[$rank->id] = $totalUsers;
        }

        if ($totalUsers == 0) {
            $this->info('Không có user nào để chia tiền hôm nay!');
            return;
        }

        DB::beginTransaction();
        try {
            $accumulatedRanks = [];

            foreach ($ranks as $rank) {
                // if ($rankUserCounts[$rank->id] == 0) {
                //     continue;
                // }

                // Tính phần trăm số tiền của rank này
                $rankPercentage = $rank->percentage_reward / 100;
                $amountToShare = $totalFunds * $rankPercentage;

                // Tổng số người nhận tính từ rank này trở lên
                $usersAtOrAboveRank = 0;
                foreach ($ranks as $higherRank) {
                    if ($higherRank->rank_lavel >= $rank->rank_lavel) {
                        $usersAtOrAboveRank += $rankUserCounts[$higherRank->id];
                    }
                }

                if ($usersAtOrAboveRank > 0) {
                    $amountPerUser = $amountToShare / $usersAtOrAboveRank;

                    // Lưu lại tổng số tiền mà từng rank sẽ nhận được khi cộng dồn
                    foreach ($ranks as $higherRank) {
                        if ($higherRank->rank_lavel >= $rank->rank_lavel && $rankUserCounts[$higherRank->id] > 0) {
                            if (!isset($accumulatedRanks[$higherRank->id])) {
                                $accumulatedRanks[$higherRank->id] = 0;
                            }
                            $accumulatedRanks[$higherRank->id] += $amountPerUser;

                            $this->info("Rank {$rank->rank_name} chia {$amountPerUser} cho {$rankUserCounts[$higherRank->id]} users có rank >= {$rank->rank_name}");
                            Log::info("Rank {$rank->rank_name} chia {$amountPerUser} cho {$rankUserCounts[$higherRank->id]} users có rank >= {$rank->rank_name}");
                        }
                    }
                }
            }

            // Cập nhật số tiền cuối cùng vào ví của từng rank
            foreach ($accumulatedRanks as $rankId => $totalAmountPerUser) {
                if ($totalAmountPerUser <= 0) {
                    continue;
                }
                // Cập nhật số dư trong bảng customers
                // $customers = Customer::where('rank_id', $rankId);

                // $totalPayment = $customers->payments()
                //     ->where('status', 'completed')
                //     ->whereMonth('created_at', Carbon::now()->month)
                //     ->whereYear('created_at', Carbon::now()->year)
                //     ->sum('amount');

                // $dessription = '';

                // if ($totalPayment <  (float) setting('monthly_repurchase')) {
                //     $customers->update([
                //         'walet_2' => DB::raw("COALESCE(walet_2, 0) + {$totalAmountPerUser}"),
                //     ]);
                // } else {
                //     $customers->update([
                //         'walet_1' => DB::raw("COALESCE(walet_1, 0) + {$totalAmountPerUser}"),
                //     ]);
                // }

                // Lấy danh sách customer có rank_id hiện tại
                $customers = Customer::where('rank_id', $rankId)->get();

                foreach ($customers as $customer) {
                    // $rank = $customer->rank()->get();
                    // $this->info('rank: '.$rank->rank_name);

                    $totalPayment = $customer->payments()
                        ->where('status', 'completed')
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('amount');

                    if ($totalPayment < (float) setting('monthly_repurchase')) {
                        //(float) setting('monthly_repurchase')
                        $customer->update([
                            'walet_2' => DB::raw("COALESCE(walet_2, 0) + {$totalAmountPerUser}"),
                        ]);

                        $customer->refresh();

                        CustomerNotification::create([
                            'title' => 'core/base::layouts.profit_sharing_notification',
                            'dessription' => 'profit_share_received_point_wallet',
                            'variables' => json_encode([
                                'amount' => $totalAmountPerUser,
                                'text_rank' => $customer->rank->rank_name,
                                'balance' => $customer->walet_2,
                            ]),
                            'customer_id' => $customer->id,
                            'url' => '/marketing/dashboard'
                        ]);
                    } else {
                        $customer->update([
                            'walet_1' => DB::raw("COALESCE(walet_1, 0) + {$totalAmountPerUser}"),
                        ]);

                        $customer->refresh();

                        CustomerNotification::create([
                            'title' => 'core/base::layouts.profit_sharing_notification',
                            'dessription' => 'profit_share_received',
                            'variables' => json_encode([
                                'amount' => $totalAmountPerUser,
                                'text_rank' => $customer->rank->rank_name,
                                'balance' => $customer->walet_1,
                            ]),
                            'customer_id' => $customer->id,
                            'url' => '/marketing/dashboard'
                        ]);
                    }

                    RewardHistory::create([
                        'customer_id' => $customer->id,
                        'rank_id' => $rankId,
                        'reward' => $totalAmountPerUser,
                        'date_reward' =>  Carbon::now(),
                        'rank_name' => $customer->rank->rank_name
                    ]);
                }

                $this->info("Rank ID {$rankId} thực nhận: {$totalAmountPerUser}/người");
                Log::info("Rank ID {$rankId} thực nhận: {$totalAmountPerUser}/người");
            }

            // $this->recordTotalDowlineHistory();

            // Reset total_dowline_month về 0 cho tất cả user
            Customer::resetAllTotalDDay();
            DB::commit();
            $this->info('Chia lợi nhuận thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi chia lợi nhuận: ' . $e->getMessage());
            $this->error('Có lỗi xảy ra khi chia lợi nhuận!' . $e->getMessage());
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
