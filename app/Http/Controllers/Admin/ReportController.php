<?php

namespace App\Http\Controllers\Admin;

use App\Models\CusTomer;
use App\Models\ProfitHistory;
use App\Models\RewardHistory;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use function Symfony\Component\Clock\now;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
  const PATH_VIEW = 'report.';
  public function index()
  {
    $to_date = $request['to_date'] ?? Carbon::now()->endOfMonth()->toDateString();
    // dd($to_date);
    $from_date = $request['from_date'] ?? Carbon::now()->startOfMonth()->toDateString();

    $dataCaculatorProfit = $this->caculatorProfit($from_date, $to_date);
    // dd($dataCaculatorProfit);
    $paginate = 20;
     $customers = CusTomer::where('id', '!=', 1)
      ->with('rank') // thêm quan hệ rank
      ->paginate($paginate);
    $totals = CusTomer::where('id', '!=', 1)->get();
    $recipients = ProfitHistory::where('recipient_id', 1)->pluck('amount');
    $data = ProfitHistory::selectRaw('SUM(amount) as total_amounts, DATE(created_at) as days')
      ->where('recipient_id', 1)
      ->groupBy('days')
      ->orderBy('days')
      ->get();
    $days = $data->pluck('days');
    $total_amounts = $data->pluck('total_amounts');
    $amount_company = ProfitHistory::where('recipient_id', 1)->sum('amount');
    $amount_user = ProfitHistory::where('recipient_id', '!=', 1)->sum('amount');

    return view(self::PATH_VIEW . __FUNCTION__, compact('customers', 'totals', 'paginate', 'recipients', 'amount_company', 'amount_user', 'days', 'total_amounts', 'to_date', 'from_date', 'dataCaculatorProfit'));
  }

  public function getChartByDays(Request $request)
  {
    $to_date = $request['to_date'] ?? Carbon::now()->endOfMonth()->toDateString();
    $from_date = $request['from_date'] ?? Carbon::now()->startOfMonth()->toDateString();

    $dataCaculatorProfit = $this->caculatorProfit($from_date, $to_date);
    // dd($request->all());

    $paginate = 8;

    $customers = CusTomer::where('id', '!=', 1)->paginate($paginate);
    $totals = CusTomer::where('id', '!=', 1)->get();
    $recipients = ProfitHistory::where('recipient_id', 1)->pluck('amount');
    $data = ProfitHistory::selectRaw('SUM(amount) as total_amounts, DATE(created_at) as days')
      ->where('recipient_id', 1)
      ->whereBetween('created_at', [$from_date, $to_date . ' 23:59:59'])
      ->groupBy('days')
      ->orderBy('days')
      ->get();
    $days = $data->pluck('days');
    $total_amounts = $data->pluck('total_amounts');
    $amount_company = ProfitHistory::where('recipient_id', 1)->sum('amount');
    $amount_user = ProfitHistory::where('recipient_id', '!=', 1)->sum('amount');
    return view('report.index', compact('customers', 'totals', 'paginate', 'recipients', 'amount_company', 'amount_user', 'days', 'total_amounts', 'to_date', 'from_date',     'dataCaculatorProfit'));
  }

  public function caculatorProfit($from_date, $to_date)
  {
    $totalDownlineRevenue = DB::table('total_dowline_day_histories')
      ->whereBetween('created_at', [$from_date, $to_date])
      ->sum('total_dowline');

    $referralCommission = DB::table('referral_commissions')
      ->whereBetween('created_at', [$from_date, $to_date])
      ->sum('commission_amount');

    $dailyBonus = DB::table('daily_bonus_logs')
      ->whereBetween('created_at', [$from_date, $to_date])
      ->sum('bonus_amount');

    $areaBonus = DB::table('area_bonus_histories')
      ->whereRaw("STR_TO_DATE(CONCAT(year, '-', month, '-01'), '%Y-%m-%d') BETWEEN ? AND ?", [$from_date, $to_date])
      ->sum('bonus_amount');

    $rewardSharing = DB::table('reward_history')
      ->whereBetween('date_reward', [$from_date, $to_date])
      ->sum('reward');

    $dataRewardSharings = RewardHistory::with('customer')
      ->whereBetween('date_reward', [$from_date, $to_date])
      ->orderByDesc('id')->paginate(5);

    // dd($dataRewardSharings);

    $totalExpenses = $referralCommission + $dailyBonus + $areaBonus + $rewardSharing;

    $netProfit = $totalDownlineRevenue - $totalExpenses;

    $percentProfit = $totalDownlineRevenue != 0 
    ? round($netProfit * 100 / $totalDownlineRevenue, 2) 
    : 0;

    return [
      'from_date' => $from_date,
      'to_date' => $to_date,
      'total_downline_revenue' => $totalDownlineRevenue,
      'expenses' => [
        'referral_commission' => $referralCommission,
        'daily_bonus' => $dailyBonus,
        'area_bonus' => $areaBonus,
        'reward_sharing' => $rewardSharing,
      ],
      'total_expenses' => $totalExpenses,
      'net_profit' => $netProfit,
      'profit_label' => $netProfit < 0 ? '-' : '',

      'dataRewardSharings' => $dataRewardSharings,
      'percentProfit' => $percentProfit
    ];
  }
}
