<?php

namespace App\Models;

use Botble\Ecommerce\Models\Customer as EcommerceCustomer;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Customer extends Model
{
    protected $table = 'ec_customers';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'referral_ids',
        'total_dowline',
        'total_dowline_month',
        'rank_id',
        'walet_1',
        'walet_2',
        'rank_assigned_at',
        'last_branch_revenue',
        'is_admin_active',
        'kyc_status'
    ];

    protected $dates = ['rank_assigned_at'];

    public function rank()
    {
        return $this->belongsTo(Ranking::class, 'rank_id');
    }

    public function upline()
    {
        return $this->belongsTo(Customer::class, 'referral_ids');
    }

    public function downline()
    {
        return $this->hasMany(Customer::class, 'referral_ids');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // Tính tổng doanh thu nhánh, bao gồm chính F0
    public function calculateBranchRevenue()
    {
        $downlineIds = $this->getAllDownlineIds();
        return Customer::whereIn('id', $downlineIds)->sum('total_dowline');
    }

    // Lấy tất cả ID tuyến dưới (bao gồm F0)
    public function getAllDownlineIds()
    {
        $ids = [$this->id];
        $directDownline = $this->downline;

        foreach ($directDownline as $downline) {
            $ids = array_merge($ids, $downline->getAllDownlineIds());
        }

        return $ids;
    }

    // Cập nhật total_dowline và rank
    public function updateTotalDowlineAndRank()
    {
        $this->total_dowline = $this->calculateBranchRevenue();
        $this->save();

        $this->assignRank();
    }

    // Đếm số F1 mới từ thời điểm lên rank
    public function countNewReferralsSinceRankAssigned()
    {
        if (!$this->rank_assigned_at) {
            return $this->downline()->count();
        }
        return $this->downline()
            ->where('created_at', '>', $this->rank_assigned_at)
            ->count();
    }

    // Gán hoặc hạ rank dựa trên điều kiện trong bảng rankings
    public function assignRank()
    {
        $directReferrals = $this->downline()->count();
        $branchRevenue = $this->calculateBranchRevenue();
        $investment = $this->walet_1;

        // Lấy rank hiện tại
        $currentRank = $this->rank_id ? Ranking::find($this->rank_id) : null;

        // Tìm rank cao nhất đủ điều kiện
        $rank = Ranking::where('number_referrals', '<=', $directReferrals)
            ->where('total_revenue', '<=', $branchRevenue)
            ->where('status', 1) // Chỉ lấy rank đang hoạt động
            ->orderBy('id', 'desc')
            ->first();

        // Nếu rank thay đổi, cập nhật thời điểm lên rank và doanh thu
        if (!$currentRank || ($rank && $currentRank->id !== $rank->id)) {
            $this->rank_assigned_at = Carbon::now();
            $this->last_branch_revenue = $branchRevenue;
        }

        // Kiểm tra điều kiện duy trì rank hiện tại
        if ($currentRank) {
            $monthsSinceRankAssigned = $this->rank_assigned_at ? Carbon::now()->diffInMonths($this->rank_assigned_at) : 0;
            $newReferrals = $this->countNewReferralsSinceRankAssigned();
            $revenueIncrease = $branchRevenue - $this->last_branch_revenue;

            // Kiểm tra điều kiện hạ rank
            if (
                $monthsSinceRankAssigned >= $currentRank->demotion_time_months &&
                ($newReferrals < $currentRank->demotion_referrals ||
                    $investment < $currentRank->demotion_investment ||
                    $revenueIncrease < ($currentRank->total_revenue * 0.25))
            ) { // Giả định tăng 25% total_revenue
                // Hạ rank xuống cấp thấp hơn hoặc không rank
                $rank = Ranking::where('number_referrals', '<=', $directReferrals)
                    ->where('total_revenue', '<=', $branchRevenue)
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->first();
            }
        }

        // Cập nhật rank
        $this->rank_id = $rank ? $rank->id : null;
        $this->save();

        return $rank ? $rank->rank_name : 'No Rank';
    }

    // Update rank when total_dowline changes using ecommerce plugin logic
    public function updateRank()
    {
        $botbleCustomer = EcommerceCustomer::find($this->id);

        if (!$botbleCustomer) {
            return;
        }

        $botbleCustomer->updateRank();

        $this->refresh();
    }
    public function kycPendings()
    {
        return $this->hasMany(PendingLog::class, 'customer_id');
    }
    public function referrer()
    {
        return $this->belongsTo(Customer::class, 'referral_ids', 'id');
    }
}