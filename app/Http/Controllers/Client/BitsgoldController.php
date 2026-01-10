<?php

namespace App\Http\Controllers\Client;

use Botble\Ecommerce\Http\Controllers\BaseController;
use App\Models\CustomerWithdrawal;
use Auth;
use App\Models\ConfirmVendorShipedToUser;
use App\Models\CustomerNotification;
use Illuminate\Support\Str;
use Botble\Ecommerce\Models\Customer;
use App\Models\RewardHistory;
use App\Models\ProfitHistory;
use Illuminate\Http\Request;
use Botble\Base\Supports\Language;
use Carbon\Carbon;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Botble\Ecommerce\Models\Order;
use Illuminate\Support\Facades\DB;
use Botble\Payment\Models\Payment;
use Botble\Payment\Enums\PaymentStatusEnum;



class BitsgoldController extends BaseController
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
  /**
   * Display a listing of the resource.
   */
  public const PATH_VIEW = 'dashboard.bitsgold.';

// public function sumRevenueOfF1($date, $date_conditions){
//   $userId = auth('customer')->user()->id;
//   $startDate = Carbon::parse($date)->startOfDay();
//   $endDate = (clone $startDate)->addDays($date_conditions)->endOfDay();
//    $f1Ids = Customer::query()
//         ->where('referral_ids', $userId)
//         ->pluck('id');
//       if ($f1Ids->isEmpty()) {
//         return 0;
//       }
//       return (float) Payment::query()
//         ->whereIn('customer_id', $f1Ids)
//         ->where('status', PaymentStatusEnum::COMPLETED)
//         ->whereBetween('created_at', [$startDate, $endDate])
//         ->sum(DB::raw('COALESCE(amount, 0) - COALESCE(refunded_amount, 0)'));
// }

// public function totalBuyRank($date, $date_conditions)
// {
//     $userId = auth('customer')->user()->id;
//     $startDate = Carbon::parse($date)->startOfDay();
//     $endDate = (clone $startDate)->addDays($date_conditions)->endOfDay();
//     $totalSpend = Order::where('user_id', $userId)
//         ->whereBetween('created_at', [$startDate, $endDate])
//         ->sum('amount');
//     return $totalSpend;
// }
private function resolveRollingWindow(int $days): array
{
    $days = $days > 0 ? $days : 30;

    $end = Carbon::now()->endOfDay();
    $start = Carbon::now()->subDays($days)->startOfDay();

    return [$start, $end];
}

public function sumRevenueSelfRolling(int $days): float
{

    [$startDate, $endDate] = $this->resolveRollingWindow($days);
    $userId = auth('customer')->id();
    $customer = Customer::findOrFail($userId);
    if($customer->rank_reset_at && Carbon::parse($customer->rank_reset_at)->lte(Carbon::now())){
        // dd('here');
        $startDate = Carbon::parse($customer->rank_reset_at);
    }



    return (float) Payment::query()
        ->where('customer_id', $userId)
        ->where('status', PaymentStatusEnum::COMPLETED)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum(DB::raw('COALESCE(amount, 0) - COALESCE(refunded_amount, 0)'));
}

public function sumRevenueOfF1Rolling(int $days): float
{
    [$startDate, $endDate] = $this->resolveRollingWindow($days);
    $userId = auth('customer')->id();
    $customer = Customer::findOrFail($userId);
    if($customer->rank_reset_at && Carbon::parse($customer->rank_reset_at)->lte(Carbon::now())){
        // dd('here');
        $startDate = Carbon::parse($customer->rank_reset_at);
    }

    $f1Ids = Customer::query()
        ->where('referral_ids', $userId)
        // ->where('is_active_account', 1)   // đồng bộ với referralCount
        // ->where('kyc_status', 1)          // đồng bộ với referralCount
        ->pluck('id');

    if ($f1Ids->isEmpty()) {
        return 0.0;
    }

    return (float) Payment::query()
        ->whereIn('customer_id', $f1Ids)
        ->where('status', PaymentStatusEnum::COMPLETED)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum(DB::raw('COALESCE(amount, 0) - COALESCE(refunded_amount, 0)'));
}

  public function dashboard()
  {
    SeoHelper::setTitle(__('Dashboard Marketing'));

    $customer = Customer::findOrFail(auth('customer')->user()->id);
    $customerId1 = Customer::findOrFail(1);
    $totalDownlineday = $customerId1->total_dowline_day;
    $totalDownlineMonth = $customerId1->total_dowline_month;
	$expired = $customer->rank_reset_at && Carbon::parse($customer->rank_reset_at)->lte(Carbon::now());
 	if ($expired) {
        // F0 chỉ tính từ sau thời điểm hết hạn (giữ giờ/phút/giây)
        $selfStart = Carbon::parse($customer->rank_reset_at);
        $selfEnd   = Carbon::now();

        // Nếu bố muốn F0 chỉ được tính trong 30 ngày sau hết hạn (chu kỳ mới), bật đoạn này:
        // $selfEnd = $selfStart->copy()->addDays(30);
        // if ($selfEnd->gt($now)) $selfEnd = $now;

    } else {
        // Chưa hết hạn / lần đầu: F0 theo window rank (rolling)
        $selfStart = Carbon::now()->copy()->subDays(30);
        $selfEnd   = Carbon::now();
    }
    // $totalDownlineMonthbyuser = $customer->total_dowline_month;
    $totalSpendMonth= Payment::query()
        ->where('customer_id', $customer->id)
        ->where('status', PaymentStatusEnum::COMPLETED)
        ->whereBetween('created_at', [$selfStart, $selfEnd])
        ->sum(DB::raw('COALESCE(amount, 0) - COALESCE(refunded_amount, 0)'));
    // $totalDownlineMonthbyuser = $customer->total_dowline_month;
  	// $totalSpendMonth=Order::where('user_id',auth('customer')->user()->id)->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at',Carbon::now()->year)->sum('amount');
    $total=setting('monthly_repurchase');
    $totalProgress=(($totalSpendMonth/$total)*100);

    $ranks = \DB::table('rankings')->select('id', 'rank_name', 'percentage_reward')->get();
    $results = $ranks->map(function ($rank) use ($totalDownlineday) {
      return [
        'id' => $rank->id,
        'name' => $rank->rank_name,
        'percentage_reward' => $rank->percentage_reward,
        'result' => $rank->percentage_reward > 0 ? ($rank->percentage_reward / 100) * $totalDownlineday : null
      ];
    })->filter(function ($item) {
      return $item['result'] !== null;
    });
    $bonusPercent = setting('bonus_percentage');
    $community_sharing = ($bonusPercent / 100) * $totalDownlineday;
    $referralCount = Customer::where('referral_ids', $customer->id)
      ->where('is_active_account', 1)
      ->where('kyc_status', 1)
      ->count();

    //Đồng chia
    $rewardsByRank = RewardHistory::select('rank_id', DB::raw('SUM(reward) as total'))
    ->where('customer_id', auth('customer')->user()->id)
    ->groupBy('rank_id')
    ->with('rank')
    ->get()
    ->mapWithKeys(function ($item) {
        return [
            $item->rank->rank_lavel => [
              'rank_name' => $item->rank->rank_name,
              'total' =>$item->total],
        ];
    });
    $rewardsByRank = $rewardsByRank->sortKeys()->all();

    // hoa hồng trực tiếp
    $referralCommission = DB::table('referral_commissions')
      ->where('customer_id', auth('customer')->user()->id)
      ->sum('commission_amount');


      $shippingIncome = ConfirmVendorShipedToUser::where('customer_id', $customer->id)
      ->where('status', 1)
      ->sum('shipping_fee');

    // dd($rewardsByRank);

    $ranks = \DB::table('rankings')
      ->select('id', 'rank_name', 'rank_lavel', 'number_referrals', 'total_revenue', 'percentage_reward','ranking_date_conditions')
      ->orderBy('rank_lavel')
      ->get();

    // Rank hiện tại
    $currentRank = $ranks->firstWhere('id', $customer->rank_id);

    // Rank tiếp theo chưa đạt
    $nextRank = $ranks->first(function ($rank) use ($currentRank, $referralCount, $totalDownlineday) {
      return (!$currentRank || $rank->rank_lavel > $currentRank->rank_lavel)
        && (
          $referralCount < $rank->number_referrals ||
          ($currentRank == null ? 1 : $currentRank->rank_lavel + 1)
        );
    });

    
    // dd($totalDownlineMonthbyuser);
    // Tính phần trăm tiến độ tới rank tiếp theo (nếu có)
    $progressToNextRank = null;
    $totalDownlineMonthbyuser = null;
    if ($nextRank) {
    $days = (int) $nextRank->ranking_date_conditions;

    $selfRevenue = $this->sumRevenueSelfRolling($days);   // F0
    $f1Revenue   = $this->sumRevenueOfF1Rolling($days);   // F1
    $totalRevenue = $selfRevenue + $f1Revenue;

    $totalDownlineMonthbyuser = $totalRevenue; // giữ tên biến để view khỏi sửa nhiều

    $referralProgress = 0;
    if ($nextRank->number_referrals !== 0) {
        $referralProgress = min(100, ($referralCount / $nextRank->number_referrals) * 100);
    }

    $revenueProgress = $nextRank->total_revenue > 0
        ? min(100, ($totalRevenue / $nextRank->total_revenue) * 100)
        : 100;

    $progressToNextRank = [
        'rank' => $nextRank->rank_name,
        'referral_progress' => round($referralProgress, 2),
        'revenue_progress' => round($revenueProgress, 2),
        'required_referrals' => $nextRank->number_referrals,
        'required_revenue' => $nextRank->total_revenue,
        'ranking_date_conditions' => $days,

        // nếu bố muốn hiển thị breakdown ở view:
        'self_revenue' => $selfRevenue,
        'f1_revenue' => $f1Revenue,
        'total_revenue' => $totalRevenue,
    ];
}



    $totalAmount = CustomerWithdrawal::where('customer_id', $customer->id)
      ->where('status', 'completed')
      ->sum('amount');
    // dd($totalAmount);
    // dd($results);
    // dd($totalDownlineMonth);

    return Theme::scope(self::PATH_VIEW . 'dashboard', compact(
      'customer',
      'totalDownlineMonth',
      'results',
      'currentRank',
      'progressToNextRank',
      'referralCount',
      'totalDownlineMonthbyuser',
      'totalAmount',
      'community_sharing',
      'totalSpendMonth',
      'total',
      'totalProgress',
      'rewardsByRank',
      'referralCommission',
      'shippingIncome'
    ), 'dashboard.bitsgold.dashboard')->render();
  }
  public function plan()
  {
    return redirect('/products');
  }
  public function invest_history() {}
  public function add_fund() {}
  public function transaction() {}

  public function referral()
  {
    SeoHelper::setTitle(__('Người giới thiệu'));
    $title = 'My Referral';
    $customer = auth('customer')->user();
    $referrals = $customer->referrers()->paginate(10);
    return Theme::scope(self::PATH_VIEW . 'referral', compact('title', 'referrals', 'customer'), 'dashboard.bitsgold.referral')->render();
  }

  // public function referral()
  // {
  //   SeoHelper::setTitle(__('Người giới thiệu'));
  //   $title = 'My Referral';
  //   $user = auth('customer')->user();
  //   if (!empty($user['id'])) {
  //     $customer = Customer::findOrFail($user['id']);
  //     $profits = $customer->receivedProfits;

  //     $referrals = $customer->getAllLevelUser($user['id'], 5);

  //     return Theme::scope(self::PATH_VIEW . 'referral', compact('title', 'referrals', 'user'), 'dashboard.bitsgold.referral')->render();
  //   }
  // }

  public function kyc_bonus() {}

  public function loadChildren($id)
  {
    $customer = Customer::findOrFail($id);
    $referrals = $customer->referrers()->with('referrer', 'referrers')->get(); // eager load

    return view('dashboard.bitsgold.partial-referral-item', compact('referrals'))->render();
  }


public function walletHistory()
  {
    $customer = Customer::findOrFail(auth('customer')->user()->id);

    $logs = CustomerNotification::where('customer_id', $customer->id)
      ->latest()
      ->paginate(15);

    $logs->getCollection()->transform(function ($log) {
      $variables = json_decode($log->variables, true) ?: [];
      $descKey = Str::lower($log->dessription ?? '');
      $titleKey = Str::lower($log->title ?? '');
      $resolveText = function (?string $key) use ($variables) {
        if (!$key) {
          return null;
        }

        $candidates = [
          $key,
          'core/base::layouts.' . $key,
          'plugins/ecommerce::order.' . $key,
        ];

        $text = null;
        foreach ($candidates as $candidate) {
          $translated = trans($candidate);
          if ($translated !== $candidate) {
            $text = $translated;
            break;
          }
        }

        if (!$text) {
          $text = Str::of($key)->replace('_', ' ')->title();
        }

        foreach ($variables as $k => $v) {
          $text = str_replace(':' . $k, $v, $text);
        }

        return $text;
      };

      $amount = $variables['amount'] ?? $variables['shipping_fee'] ?? null;

      $wallet = 'Ví rút';
      if (Str::contains($descKey, ['wallet2', 'walet_2', 'wallet_2', 'point_wallet'])) {
        $wallet = 'Ví tiêu dùng';
      }

      $type = 'in';
      if (Str::contains($descKey, ['withdrawal', 'rút tiền', 'transfer_out', 'transfer_sent', 'transfer_out_wallet1'])) {
        $type = Str::contains($descKey, ['rejected', 'bị từ chối', 'reject']) ? 'rejected' : 'out';
      } elseif (Str::contains($descKey, ['transfer_in', 'transfer_received', 'transfer_in_wallet1'])) {
        $type = 'in';
      }
      if ($type === 'out' && $amount) {
        $amount = -abs($amount);
      }

      $log->computed_amount = $amount;
      $log->wallet_label = $wallet;
      $isTransferOut = Str::contains($descKey, ['transfer_out_wallet1']) || Str::contains($titleKey, ['wallet_transfer_sent']);
      $isTransferIn = Str::contains($descKey, ['transfer_in_wallet1']) || Str::contains($titleKey, ['wallet_transfer_received']);

      if ($isTransferOut) {
        $log->display_title = 'Chuyển tiền (ví rút)';
        $log->display_desc = sprintf(
          'Đến: %s | Số tiền: %s | Mã: %s%s',
          $variables['email'] ?? '-',
          isset($variables['amount']) ? format_price($variables['amount']) : '-',
          $variables['reference'] ?? '-',
          !empty($variables['note']) ? ' | Nội dung: ' . $variables['note'] : ''
        );
      } elseif ($isTransferIn) {
        $log->display_title = 'Nhận tiền (ví rút)';
        $log->display_desc = sprintf(
          'Từ: %s | Số tiền: %s | Mã: %s%s',
          $variables['email'] ?? '-',
          isset($variables['amount']) ? format_price($variables['amount']) : '-',
          $variables['reference'] ?? '-',
          !empty($variables['note']) ? ' | Nội dung: ' . $variables['note'] : ''
        );
      } else {
        $log->display_title = $resolveText($log->title);
        $log->display_desc = $resolveText($log->dessription);
      }
      $log->type = $type;

      return $log;
    });

    $logs->setCollection(
      $logs->getCollection()->filter(function ($log) {
        if ($log->computed_amount === null) {
          return false;
        }

        $descKey = Str::lower($log->dessription ?? '');
        $titleKey = Str::lower($log->title ?? '');
        $keywords = ['wallet', 'withdraw', 'rút tiền', 'deposit', 'giao hàng', 'shipping', 'commission', 'hoa hồng', 'bonus', 'profit', 'chia', 'walet', 'ship', 'transfer', 'transfer_out_wallet1', 'transfer_in_wallet1'];

        foreach ($keywords as $keyword) {
          if (Str::contains($descKey, $keyword) || Str::contains($titleKey, $keyword)) {
            return true;
          }
        }

        return false;
      })->values()
    );

    return Theme::scope(self::PATH_VIEW . 'wallet-history', compact('customer', 'logs'))->render();
  }

}
