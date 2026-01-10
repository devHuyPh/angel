<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Botble\Ecommerce\Models\Customer;
use App\Models\Ranking;
use Carbon\Carbon;
use App\Models\CustomerNotification;

class CheckRank extends Command
{
    protected $signature = 'check:rank';
    protected $description = 'Kiểm tra và cập nhật rank của customer';

    public function handle()
    {
        $customers = Customer::where('is_admin_active', 0)->get();
        $now = now(); 

        foreach ($customers as $customer) {
            $rankAssignedAt = $customer->rank_assigned_at;

            if (!$customer->rank_id || !$rankAssignedAt) {
                continue;
            }

            $currentRank = Ranking::find($customer->rank_id);

            if (!$currentRank) {
                continue;
            }

            // Kiểm tra điều kiện xuống cấp (demotion)
            $demotionTimeMonths = (int) $currentRank->demotion_time_months;
            $demotionInvestment = (int) $currentRank->demotion_investment;
            $demotionReferrals = (int) $currentRank->demotion_referrals;

            $demotionDeadline = Carbon::parse($rankAssignedAt)->copy()->addMonths($demotionTimeMonths);
            $this->info($demotionDeadline);
            $this->info($now);

            // Kiểm tra nếu đã tới thời hạn xuống cấp
            if ($now->greaterThanOrEqualTo($demotionDeadline)) {
                $newReferralsCount = Customer::where('referral_ids', $customer->id)
                    ->where('created_at', '>=', $rankAssignedAt)
                    ->count();

                $this->info($newReferralsCount);

                if ($newReferralsCount < $demotionReferrals || $customer->total_dowline_on_rank < $demotionInvestment) {
                    $this->info('có rank cần hạ');
                    $lowerRank = Ranking::where('rank_lavel', '<', $currentRank->rank_lavel)
                        ->orderByDesc('rank_lavel')
                        ->first();

                    if ($lowerRank) {
                        $this->info($lowerRank);
                        Log::info("Hạ cấp Customer ID: {$customer->id} từ Rank {$customer->rank_id} → {$lowerRank->id}");
                        $customer->update([
                            'rank_id' => $lowerRank->id,
                            'rank_assigned_at' => $now, 
                            'total_dowline_on_rank' => 0, 
                        ]);
                        
                        CustomerNotification::create([ 
                            'title' => 'core/base::layouts.rank_demotion_notification_on '.$currentRank->rank_name.' core/base::layouts.down '.$lowerRank->rank_name,
                            'dessription' => 'core/base::layouts.rank_demotion_description '.$currentRank->demotion_time_months.
                                            ' core/base::layouts.rank_demotion_description_month '.$currentRank->demotion_referrals.
                                            ' core/base::layouts.rank_demotion_description_if '.format_price($currentRank->demotion_investment).
                                            ' core/base::layouts.rank_demotion_description_now '.$lowerRank->rank_name,
                            'customer_id' => $customer->id,
                            'url' => '/marketing/dashboard'
                        ]);                        
                        
                    }else{
                        $this->info('hạ về null');
                        Log::info("Hạ cấp Customer ID: {$customer->id} từ Rank {$customer->rank_id} → {null}");
                        $customer->update([
                            'rank_id' => null,
                            'rank_assigned_at' => null, 
                            'total_dowline_on_rank' => 0, 
                        ]);
                        
                        CustomerNotification::create([ 
                            'title' => 'core/base::layouts.rank_demotion_notification_on '.$currentRank->rank_name.' core/base::layouts.down Vô Hạng',
                            'dessription' => 'core/base::layouts.rank_demotion_description '.$currentRank->demotion_time_months.
                                            ' core/base::layouts.rank_demotion_description_month '.$currentRank->demotion_referrals.
                                            ' core/base::layouts.rank_demotion_description_if '.format_price($currentRank->demotion_investment).
                                            ' core/base::layouts.rank_demotion_description_now Vô Hạng',
                            'customer_id' => $customer->id,
                            'url' => '/marketing/dashboard'
                        ]);    
                    }
                    
                }
            }
        }

        $this->info('Hoàn thành kiểm tra & hạ cấp rank!');
    }
}
