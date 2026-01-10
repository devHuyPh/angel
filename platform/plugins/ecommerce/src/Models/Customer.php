<?php

namespace Botble\Ecommerce\Models;

use App\Models\BankAccount;
use App\Models\ConfirmVendorShipedToUser;
use App\Models\CustomerNotification;
use App\Models\CustomerWithdrawal;
use App\Models\DepositHistory;
use App\Models\ProfitHistory;
use App\Models\Ranking;
use App\Models\ReferralCommission;
use App\Models\TotalRevenue;
use App\Models\VendorLateDelivery;
use Botble\Base\Facades\MacroableModels;
use Botble\Base\Models\BaseModel;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Base\Supports\Avatar;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Enums\DiscountTypeEnum;
use Botble\Ecommerce\Notifications\ConfirmEmailNotification;
use Botble\Ecommerce\Notifications\ResetPasswordNotification;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\VendorNotifications;
use Botble\Payment\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class Customer extends BaseModel implements
  AuthenticatableContract,
  AuthorizableContract,
  CanResetPasswordContract
{
  use Authenticatable;
  use Authorizable;
  use CanResetPassword;
  use MustVerifyEmail;
  use HasApiTokens;
  use Notifiable;

  protected $table = 'ec_customers';

  protected $fillable = [
    'name',
    'uuid_code',
    'email',
    'referrals_id',
    'uuid_code',
    'password',
    'avatar',
    'phone',
    'status',
    'private_notes',
    'referral_ids',
    'rank_id',
    'total_dowline',
    'walet_1',
    'walet_2',
    'total_revenue_id',
    'rank_assigned_at',
    'total_dowline_on_rank',
    'total_dowline_day',
    'total_dowline_month',
    'is_admin_active',
    'kyc_status',
    'is_active_account',
    'is_webhook_sepay_active',
    'fcm_token',
    'total_warehouse_referral',
   	'rank_expires_at',
    'rank_reset_at'
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'status' => CustomerStatusEnum::class,
    'dob' => 'date',
  ];

  public function sendPasswordResetNotification($token): void
  {
    $this->notify(new ResetPasswordNotification($token));
  }

  public function sendEmailVerificationNotification(): void
  {
    $this->notify(new ConfirmEmailNotification());
  }

  public function orders(): HasMany
  {
    return $this->hasMany(Order::class, 'user_id', 'id');
  }

  public function deposit(): HasMany
  {
    return $this->hasMany(DepositHistory::class, 'user_id', 'id');
  }

  public function referralCommissions()
  {
    return $this->hasMany(ReferralCommission::class, 'customer_id');
  }

  public function rank(): BelongsTo
  {
    return $this->belongsTo(Ranking::class, 'rank_id', 'id');
  }

  public function referrer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'referral_ids', 'id');
  }

  public function getTotalRevenue(): BelongsTo
  {
    return $this->belongsTo(TotalRevenue::class, 'total_revenue_id');
  }

  public function referrers(): HasMany
  {
    return $this->hasMany(Customer::class, 'referral_ids', 'id');
  }

  public static function resetAllTotalDMonth()
  {
    self::query()->update(['total_dowline_month' => 0]);
  }

  public static function resetAllTotalDDay()
  {
    self::query()->update(['total_dowline_day' => 0]);
  }

  public function receivedProfits()
  {
    return $this->hasMany(ProfitHistory::class, 'recipient_id');
  }

  public function referredProfits()
  {
    return $this->hasMany(ProfitHistory::class, 'referrer_id');
  }

  public function directReferralsCount()
  {
    return $this->hasMany(Customer::class, 'referral_ids', 'id')->count();
  }

  public function directActiveReferralsCount()
  {
    return $this->hasMany(Customer::class, 'referral_ids', 'id')
      ->where('is_active_account', 1)
      ->where('kyc_status', 1)
      ->count();
  }

  public function notifications()
  {
    return $this->hasMany(CustomerNotification::class, 'customer_id', 'id')->orderBy('created_at', 'desc');
  }

  public function notificationCount()
  {
    return $this->hasMany(CustomerNotification::class, 'customer_id', 'id')->count();
  }

  public function withdrawals()
  {
    return $this->hasMany(CustomerWithdrawal::class, 'customer_id');
  }

  public function bankAccounts()
  {
    return $this->hasMany(BankAccount::class, 'user_id');
  }

  public function directReferralsCountAt(): Attribute
  {
    return Attribute::make(
      get: fn() => (int) $this->hasMany(Customer::class, 'referral_ids', 'id')->count(),
    );
  }

  public function updateTotalRevenue()
  {
    // Lấy total_revenue cao nhất mà user có thể đạt được
    $total_revenue = TotalRevenue::where('amount', '<=', $this->total_dowline)
      ->orderByDesc('amount')
      ->first();

    if ($total_revenue) {
      $this->total_revenue_id = $total_revenue->id;
      $this->save();

      CustomerNotification::create([ //You enjoy a 10% instant discount on all your orders.
        'title' => 'core/base::layouts.you_enjoy_a ' . $total_revenue->percentage . '% core/base::layouts.instant_discount_on_all_order',
        'dessription' => 'core/base::layouts.desc_totalrenue ' . format_price($total_revenue->amount)
          . '. core/base::layouts.starting_now_your_orders :' . $total_revenue->percentage . '% ',
        'customer_id' => $this->id,
        'url' => '/marketing/dashboard'
      ]);
    }
  }


  // public function updateRank()
  // {
  //   $rankQuery = Ranking::where(function ($query) {
  //     $query->where('number_referrals', '<=', $this->directActiveReferralsCount())
  //       ->orWhere('total_revenue', '<=', $this->total_dowline);
  //   });

  //   if (!is_null($this->rank) && !is_null($this->rank->rank_lavel)) {
  //     $rankQuery->where('rank_lavel', '>', $this->rank->rank_lavel);
  //   }

  //   $rank = $rankQuery->orderByDesc('number_referrals')->first();

  //   // dd($rank);
  //   if ($rank) {
  //     // if ($this->rank && $this->rank()->first()->rank_lavel < $rank->rank_lavel) {
  //     // if ($this->rank && $this->rank()->first()->rank_lavel) {
  //     //   if($this->rank()->first()->rank_lavel < $rank->rank_lavel){
  //     if ($rank->id != $this->rank_id) {
  //       $this->rank_assigned_at = Carbon::now();
  //       $this->total_dowline_on_rank = 0;

  //       CustomerNotification::create([
  //         'title' => 'core/base::layouts.rank_upgrade_notification',
  //         'dessription' => 'core/base::layouts.you_have_qualified_for_a_rank_upgrade ' . $rank->rank_name
  //           . ' core/base::layouts.with_total_earnings_from_your_downline_of ' . format_price($this->total_dowline)
  //           . ' core/base::layouts.and_the_count_of_your_direct_referrals ' . $rank->number_referrals,
  //         'customer_id' => $this->id,
  //         'url' => '/marketing/dashboard'
  //       ]);
  //     }
  //     $this->rank_id = $rank->id;
  //     $this->save();
  //     // }
  //     // && $this->is_default != 0

  //     // }
  //     // dd('rank');
  //   }
  // }
  
  public function sumRevenueOfF1Between(Carbon $start, Carbon $end): float
{
    $f1Ids = self::query()
        ->where('referral_ids', $this->id)   
        ->pluck('id');

    if ($f1Ids->isEmpty()) {
        return 0.0;
    }

    return (float) Payment::query()
        ->whereIn('customer_id', $f1Ids)
        ->where('status', PaymentStatusEnum::COMPLETED)
        ->whereBetween('created_at', [$start, $end])
        ->sum(DB::raw('COALESCE(amount, 0) - COALESCE(refunded_amount, 0)'));
}  
  public function updateRank()
  {
    $now = Carbon::now();

    // Số F1 active hiện tại (bố để number_referrals = 0 thì coi như luôn đủ)
    $directReferrals = $this->directActiveReferralsCount();

    // 1. Lấy danh sách các rank CÓ THỂ lên được về mặt "level"
    $rankQuery = Ranking::query();

    if (!is_null($this->rank) && !is_null($this->rank->rank_lavel)) {
      // Chỉ xét rank cao hơn rank hiện tại
      $rankQuery->where('rank_lavel', '>', $this->rank->rank_lavel);
    }

    // Có thể thêm where('status', 1) nếu bố có dùng status active/inactive
    // $rankQuery->where('status', 1);

    // 2. Lọc sơ bộ theo số F1 (điều kiện cứng, độc lập với doanh thu)
    $rankQuery->where('number_referrals', '<=', $directReferrals);
    Log::info('Direct Referrals: ' . $directReferrals);

    // Lấy tất cả rank đủ điều kiện sơ bộ, sắp xếp theo level tăng dần
    $candidateRanks = $rankQuery
      ->orderBy('rank_lavel', 'asc')
      ->get();
    Log::info('Candidate Ranks: ' . $candidateRanks->count());

    $qualifiedRank = null;
    $monthly_repurchase = (float) setting('monthly_repurchase');
    $expired = $this->rank_reset_at && Carbon::parse($this->rank_reset_at)->lte($now);
    foreach ($candidateRanks as $rank) {
      // 3. Số ngày cần tính doanh thu cho CHÍNH rank này
      $days = (int) $rank->ranking_date_conditions;
      if ($days <= 0) {
        $days = 30; // fallback nếu cấu hình ngu
      }
      $f1Start = $now->copy()->subDays($days);
      $f1End   = $now;
      if ($expired) {
        // F0 chỉ tính từ sau thời điểm hết hạn (giữ giờ/phút/giây)
        $selfStart = Carbon::parse($this->rank_reset_at);
        $selfEnd   = $now;

        // Nếu bố muốn F0 chỉ được tính trong 30 ngày sau hết hạn (chu kỳ mới), bật đoạn này:
        // $selfEnd = $selfStart->copy()->addDays(30);
        // if ($selfEnd->gt($now)) $selfEnd = $now;

    } else {
        // Chưa hết hạn / lần đầu: F0 theo window rank (rolling)
        $selfStart = $now->copy()->subDays($days);
        $selfEnd   = $now;
    }
      /**
       * 4. Xác định khoảng thời gian [start, end] để tính doanh thu
       *
       * - Nếu đã có rank_assigned_at: start = rank_assigned_at
       * - Nếu chưa có: start = now - days (ngày gần nhất theo days của rank đang xét)
       * - end = start + days, nhưng không được vượt quá hiện tại
       */
      // if ($this->rank_assigned_at) {
      //   Log::info('Rank Assigned At: ' . $this->rank_assigned_at);
      //   $start = Carbon::parse($this->rank_assigned_at);
      // } else {
      // $start = $now->copy()->subDays($days);
      // }

      // $end = $now;
      // $start->copy()->addDays($days);
      // if ($end->gt($now)) {
      //   $end = $now;
      // }

      // Nếu end <= start thì khoảng thời gian vô nghĩa, bỏ qua rank này
      if ($selfEnd->lte($selfStart)) {
        continue;
      }

      /**
       * 5. Tính doanh thu của CHÍNH user trong khoảng [start, end]
       *    - Lấy trực tiếp từ bảng payments
       *    - Theo customer_id
       *    - Chỉ tính payment COMPLETED
       */
      $selfRevenue = Payment::query()
        ->where('customer_id', $this->id)
        ->where('status', PaymentStatusEnum::COMPLETED)
        ->whereBetween('created_at', [$selfStart, $selfEnd])
        ->sum(DB::raw('COALESCE(amount, 0) - COALESCE(refunded_amount, 0)'));

     $f1Revenue = $this->sumRevenueOfF1Between($selfStart, $selfEnd);
      // $monthly_repurchase = setting('monthly_repurchase');

      Log::info('Evaluating Rank: ' . $rank->rank_name . ' | Self Revenue: ' . $selfRevenue . ' | F1 Revenue: ' . $f1Revenue);
      // Log::info('Start: ' . $start . ' | End: ' . $end);
      // dd( $selfRevenue,$f1Revenue);
      $totalRevenue = $selfRevenue + $f1Revenue;
      $repurchaseOk = $selfRevenue >= $monthly_repurchase;
      // 6. Kiểm tra điều kiện doanh thu của rank này
      if ($totalRevenue >= $rank->total_revenue && $repurchaseOk) {
        // Đủ điều kiện rank này → gán, nhưng tiếp tục vòng lặp
        // để nếu có rank level cao hơn cũng đủ thì lấy rank cao nhất
        $qualifiedRank = $rank;
      }
    }

    // 7. Sau khi duyệt hết, nếu không có rank nào đủ điều kiện -> thôi
    if (!$qualifiedRank) {
      return;
    }

    $rank = $qualifiedRank;

    // dd($rank);
    if ($rank) {
      // if ($this->rank && $this->rank()->first()->rank_lavel < $rank->rank_lavel) {
      // if ($this->rank && $this->rank()->first()->rank_lavel) {
      //   if($this->rank()->first()->rank_lavel < $rank->rank_lavel){
      if ($rank->id != $this->rank_id) {
        // LÊN RANK MỚI:
        // - rank_assigned_at = bây giờ
        // - lần sau tính tiếp theo ranking_date_conditions của rank tiếp theo từ mốc này
        $this->rank_assigned_at = $now;
        $this->rank_expires_at = $now->copy()->addDays(30);
        $this->total_dowline_on_rank = 0;

        CustomerNotification::create([
          'title' => 'core/base::layouts.rank_upgrade_notification',
          'dessription' => 'up_rank_notification',
          'variables' => json_encode([
            'text_rank_name' => $rank->rank_name,
            'total_dowline' => $this->total_dowline,
            'reward_percentage' => $rank->percentage_reward,
          ]),
          'customer_id' => $this->id,
          'url' => '/marketing/dashboard',
        ]);
      }

      $this->rank_id = $rank->id;
      $this->save();
      // }
      // && $this->is_default != 0

      // }
      // dd('rank');
    }
  }



  public function completedOrders(): HasMany
  {
    return $this->orders()->whereNotNull('completed_at');
  }

  public function addresses(): HasMany
  {
    return $this
      ->hasMany(Address::class, 'customer_id', 'id')
      ->when(is_plugin_active('location'), function (HasMany|BaseQueryBuilder $query) {
        return $query->with(['locationCountry', 'locationState', 'locationCity']);
      });
  }

  public function payments(): HasMany
  {
    return $this->hasMany(Payment::class, 'customer_id', 'id');
  }

  public function discounts(): BelongsToMany
  {
    return $this->belongsToMany(Discount::class, 'ec_discount_customers', 'customer_id', 'id');
  }

  public function wishlist(): HasMany
  {
    return $this->hasMany(Wishlist::class, 'customer_id');
  }

  protected static function booted(): void
  {
    self::deleted(function (Customer $customer): void {
      $customer->discounts()->detach();
      $customer->usedCoupons()->detach();
      $customer->orders()->update(['user_id' => 0]);
      $customer->addresses()->delete();
      $customer->wishlist()->delete();
      $customer->reviews()->each(fn(Review $review) => $review->delete());
    });

    static::deleted(function (Customer $customer): void {
      $folder = Storage::path($customer->upload_folder);
      if (File::isDirectory($folder) && Str::endsWith($customer->upload_folder, '/' . $customer->id)) {
        File::deleteDirectory($folder);
      }
    });
  }

  public function __get($key)
  {
    if (class_exists('MacroableModels')) {
      $method = 'get' . Str::studly($key) . 'Attribute';
      if (MacroableModels::modelHasMacro(get_class($this), $method)) {
        return call_user_func([$this, $method]);
      }
    }

    return parent::__get($key);
  }

  public function reviews(): HasMany
  {
    return $this->hasMany(Review::class, 'customer_id');
  }

  public function promotions(): BelongsToMany
  {
    return $this
      ->belongsToMany(Discount::class, 'ec_discount_customers', 'customer_id')
      ->where('type', DiscountTypeEnum::PROMOTION)
      ->where('start_date', '<=', Carbon::now())
      ->where('target', 'customer')
      ->where(function ($query) {
        return $query
          ->whereNull('end_date')
          ->orWhere('end_date', '>=', Carbon::now());
      })
      ->where('product_quantity', 1);
  }

  public function viewedProducts(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'ec_customer_recently_viewed_products');
  }

  public function usedCoupons(): BelongsToMany
  {
    return $this->belongsToMany(Discount::class, 'ec_customer_used_coupons');
  }

  public function deletionRequest(): HasOne
  {
    return $this->hasOne(CustomerDeletionRequest::class, 'customer_id');
  }
  public function vendorNotifications()
  {
    return $this->hasMany(VendorNotifications::class, 'vendor_id')->orderByDesc('created_at');
  }
  protected function avatarUrl(): Attribute
  {
    return Attribute::get(function () {
      if ($this->avatar) {
        return RvMedia::getImageUrl($this->avatar, 'thumb');
      }

      try {
        return (new Avatar())->create(Str::ucfirst($this->name))->toBase64();
      } catch (Exception) {
        return RvMedia::getDefaultImage();
      }
    });
  }

  protected function uploadFolder(): Attribute
  {
    return Attribute::get(function () {
      $folder = $this->id ? 'customers/' . $this->id : 'customers';

      return apply_filters('ecommerce_customer_upload_folder', $folder, $this);
    });
  }

  public $allusers = [];

  function getAllLevelUser($user_id, $perPage = 5)
  {
    $this->allusers = [];

    $this->referralUsers([$user_id], 1, $perPage); // Bắt đầu từ Level 1

    return $this->allusers;
  }
  static public function getAllLevelUserFromAdmin()
  {
    $instance = new self();
    $instance->allusers = [];

    // Lấy tất cả người dùng không có referral_ids (level 1)
    $levelUsers = Customer::whereNull('referral_ids')
      ->select(['id', 'name', 'phone', 'email', 'referral_ids', 'rank_id', 'total_dowline', 'created_at'])
      ->get();

    if ($levelUsers->count() > 0) {
      // Thêm người dùng level 1
      $instance->allusers[1] = $levelUsers;
      // Lấy ID của người dùng level 1 để lấy tuyến dưới
      $levelIds = $levelUsers->pluck('id')->toArray();
      // Bắt đầu từ level 2
      $instance->referralUsers($levelIds, 2);
    }

    return $instance->allusers;
  }

  public function referralUsers($ids, $currentLevel = 1, $perPage = 5)
  {
    $users = $this->getUsers($ids, $perPage);
    if ($users['status']) {
      $this->allusers[$currentLevel] = $users['user']; // Lưu danh sách user của Level hiện tại


      if (!empty($users['ids'])) {  // Nếu có tuyến dưới, tiếp tục đệ quy để lấy cấp tiếp theo
        $this->referralUsers($users['ids'], $currentLevel + 1, $perPage);
      }
    }
  }

  public function getUsers($ids, $perPage)
  {
    $data = ['status' => false, 'user' => null, 'ids' => []];
    if (!empty($ids) && is_array($ids)) {
      // Lấy toàn bộ ID tuyến dưới không bị giới hạn bởi phân trang
      $allIds = Customer::whereIn('referral_ids', $ids)->pluck('id')->toArray();
      //  Chỉ phân trang dữ liệu hiển thị
      $users = Customer::whereIn('referral_ids', $ids)
        ->select(['id', 'name', 'phone', 'email', 'referral_ids', 'rank_id', 'total_dowline', 'created_at'])
        ->get();

      if ($users->count() > 0) {
        $data['status'] = true;
        $data['user'] = $users; // user để phân trang
        $data['ids'] = $allIds; // Danh sách ID đầy đủ để tiếp tục đệ quy

      }
    }

    return $data;
  }
  public static function getRootUsers()
  {
    return Customer::where('referral_ids', '=', null)->orderBy('id', 'asc')
      ->select(['*'])
      ->get();
  }
  public static function getChildren($parentId, $level)
  {
    $users = Customer::where('referral_ids', $parentId)
      ->select(['*'])
      ->get();

    return [
      'status' => $users->count() > 0,
      'level' => $level,
      'users' => $users
    ];
  }

  public function confirmedShipmentsAsCustomer()
  {
    return $this->hasMany(ConfirmVendorShipedToUser::class, 'customer_id');
  }

  public function lateDeliveries(): HasMany
  {
    return $this->hasMany(VendorLateDelivery::class, 'customer_id');
  }
}