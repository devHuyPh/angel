<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CusTomer;
use App\Models\ProfitHistory;
use App\Models\CustomerWithdrawal;
use App\Models\RewardHistory;
use Illuminate\Support\Facades\DB;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use Botble\Ecommerce\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Botble\Ecommerce\Models\WareHouseReferral;
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Models\Payment;

class ClientController extends BaseController
{

  public function __construct()
  {
      $version = get_cms_version();

      Theme::asset()
          ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);

      Theme::asset()
          ->add('front-ecommerce-css', 'vendor/core/plugins/ecommerce/css/front-ecommerce.css', version: $version);

      Theme::asset()
          ->container('footer')
          ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'], version: $version)
          ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'], version: $version)
          ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery'], version: $version);
  }
  const PATH_VIEW = 'dashboard.client.';

  public function dashboard(Request $request)
  {
  	SeoHelper::setTitle(__('Báo cáo người dùng'));
    $customerId = auth('customer')->user()->id;

    //theo tháng
    $from_month = $request->input('from_month', now()->format('Y-m'));
    $to_month = $request->input('to_month');
    [$year, $month] = explode('-', $from_month);
    $year  = (int) $year;
    $month = (int) $month;
    $start = null;
    $end = null;
    if ($from_month && $to_month) {
      [$fromYear, $fromMonth] = explode('-', $from_month);
      [$toYear, $toMonth]     = explode('-', $to_month);

      $start = Carbon::create((int)$fromYear, (int)$fromMonth, 1)->startOfDay();
      $end   = Carbon::create((int)$toYear, (int)$toMonth, 1)->endOfMonth()->endOfDay();
    }

    //theo ngày
    $startDate = $request->filled('start_date')
    ? Carbon::parse($request->input('start_date'))->startOfDay()
    : null;
    $endDate = $request->filled('end_date')
    ? Carbon::parse($request->input('end_date'))->endOfDay()
    : null;

    // Lấy total_dowline của người dùng đang đăng nhập
    $totalDownlineQuery = Order::where('user_id', $customerId)->where("status", 'completed');
    
    // Tính tổng số người được giới thiệu (trực tiếp)
    $totalReferralsQuery = Customer::where('referral_ids', $customerId);

    //Đồng chia
    $rewardsByRankQuery = RewardHistory::where('customer_id',$customerId);

    //hoa hồng kho
    $wareHouseReferralQuery = WareHouseReferral::where('customer_id',$customerId);
    // hoa hồng trực tiếp
    $referralCommissionQuery = DB::table('referral_commissions')
      ->where('customer_id', $customerId);

    // Hàm áp điều kiện thời gian
    $applyDateFilter = function ($query) use ($startDate, $endDate, $year, $month, $start, $end) {
      if ($startDate && $endDate) {
          return $query->whereBetween('created_at', [$startDate, $endDate]);
      } elseif ($startDate && ! $endDate) {
          return $query->whereDate('created_at', $startDate);
      } elseif (! $startDate && $endDate) {
          return $query->whereDate('created_at', $endDate);
      }

      if($start && $end){
        return $query->whereBetween('created_at', [$start, $end]);
      }
      return $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
    };
  
    $totalDownline = $applyDateFilter($totalDownlineQuery)
        ->sum('amount');

    $totalReferrals = $applyDateFilter($totalReferralsQuery)
        ->count();

    $rewardsByRank = $applyDateFilter($rewardsByRankQuery)
        ->sum('reward'); 

    $wareHouseReferral = $applyDateFilter($wareHouseReferralQuery)
        ->sum('amount');

    $referralCommission = $applyDateFilter($referralCommissionQuery)
        ->sum('commission_amount');

    // Lấy danh sách khách hàng tuyến dưới (trực tiếp)
    $customers = Customer::where('referral_ids', $customerId)
      ->select('id', 'referral_ids', 'name', 'phone', 'rank_id', 'total_dowline', 'walet_1', 'is_admin_active')
      ->with('rank')
      ->paginate(10);
  

    $totalAmount = $rewardsByRank + $wareHouseReferral + $referralCommission;
    //  $totalAmount = CustomerWithdrawal::where('customer_id', $customerId)
    //   ->where('status', 'completed')
    //   ->whereMonth('created_at', Carbon::now()->month)
    //   ->whereYear('created_at', Carbon::now()->year)
    //   ->sum('amount') ?? 0;



    // chuẩn bị data cho biểu đồ
    $profitLabels = [];
    $monthlyIncomeData   = [];
    
    for ($m = 1; $m <= 12; $m++) {
        $profitLabels[] = sprintf('%02d', $m);
        
    }

    $monthWareHouseReferral = $this->monthWareHouseReferral($customerId, now()->year);
    $monthReferralCommission = $this->monthReferralCommission($customerId, now()->year);
    $monthRewardsByRank = $this->monthRewardsByRank($customerId, now()->year);
    $monthlyIncomeData = array_map(function ($a, $b, $c) {
        return $a + $b + $c;
    }, $monthWareHouseReferral, $monthReferralCommission, $monthRewardsByRank);
    $monthlyTotalDownline = $this->monthlyTotalDownline($customerId, now()->year);

    return Theme::scope(self::PATH_VIEW . 'dashboard', compact(
      'totalDownline',
      'totalReferrals',
      'customers',
      'totalAmount',
      'from_month',
      'startDate',
      'endDate',
      'rewardsByRank',
      'referralCommission',
      'wareHouseReferral',
      'profitLabels',
      'monthlyIncomeData',
      'monthlyTotalDownline'
    ), 'dashboard.client.dashboard')->render();
  }

  public function exportCustomers()
  {
    $customerId = auth('customer')->user()->id;
    return Excel::download(new CustomersExport($customerId), 'customers.xlsx');
  }

  public function monthRewardsByRank($customerId,$year){
    $raw = RewardHistory::query()
    ->where('customer_id', $customerId)
    ->whereYear('created_at', $year)
    ->selectRaw('MONTH(created_at) as month, SUM(reward) as total')
    ->groupBy('month')
    ->pluck('total', 'month'); 

    $rewardsByMonth = [];

    for ($m = 1; $m <= 12; $m++) {
        $rewardsByMonth[] = (float) ($raw[$m] ?? 0);
    }
    return $rewardsByMonth;
  } 
  public function monthWareHouseReferral($customerId,$year){
    $raw = WareHouseReferral::query()
    ->where('customer_id', $customerId)
    ->whereYear('created_at', $year)
    ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
    ->groupBy('month')
    ->pluck('total', 'month'); 

    $rewardsByMonth = [];

    for ($m = 1; $m <= 12; $m++) {
        $rewardsByMonth[] = (float) ($raw[$m] ?? 0);
    }
    return $rewardsByMonth;
  } 
  public function monthReferralCommission($customerId,$year){
    $raw = DB::table('referral_commissions')
    ->where('customer_id', $customerId)
    ->whereYear('created_at', $year)
    ->selectRaw('MONTH(created_at) as month, SUM(commission_amount) as total')
    ->groupBy('month')
    ->pluck('total', 'month'); 

    $rewardsByMonth = [];

    for ($m = 1; $m <= 12; $m++) {
        $rewardsByMonth[] = (float) ($raw[$m] ?? 0);
    }
    return $rewardsByMonth;
  } 
  public function monthlyTotalDownline($customerId,$year){
    $raw = Order::query()
    ->where('user_id', $customerId)
    ->where("status", 'completed')
    ->whereYear('created_at', $year)
    ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
    ->groupBy('month')
    ->pluck('total', 'month'); 

    $data = [];

    for ($m = 1; $m <= 12; $m++) {
        $data[] = (float) ($raw[$m] ?? 0);
    }
    return $data;
  } 
}
