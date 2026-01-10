<?php

use App\Http\Controllers\Admin\AdminStoreOrderController;
use App\Http\Controllers\Client\BitsgoldController;
use App\Http\Controllers\Admin\TotalRevenueController;
use App\Http\Controllers\Client\KycCustomerController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Admin\DistributeDailyOrderBonusController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\Client\WalletTransferController;

use App\Http\Controllers\StoreLevelController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\KycLogController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReferralCommissionController;
use Botble\Marketplace\Http\Middleware\LocaleMiddleware;
use Botble\Ecommerce\Http\Controllers\ExportReportController;
use App\Http\Controllers\Client\BankAccountController;
use App\Http\Controllers\Admin\RewardHistoryController;
use App\Http\Controllers\Client\CustomerWithdrawalController;
use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Client\ManagerController as ClientManagerController;


use App\Http\Controllers\RegisterReferralController;
use App\Http\Controllers\Admin\AdCustomerWithdrawalController;
use App\Http\Controllers\Admin\ConfirmVendorShipedToUserController;
use App\Http\Controllers\Admin\VendorLateDeliveryController;
use App\Http\Controllers\Client\DepositHistoryController;
use App\Http\Controllers\Client\MobileLayoutController;
use App\Http\Controllers\GhnController;
use App\Http\Controllers\Marketplace\MpManagerController;
use App\Http\Controllers\Marketplace\MpStoreOrderController;
use App\Http\Controllers\Marketplace\VendorConfirmVendorShipedToUserController;
use App\Http\Controllers\Webhook\DepositWebhookController;
use App\Http\Controllers\Webhook\StoreOrderWebhookController;
use App\Http\Controllers\Webhook\WithdrawalWebhookController;
use App\Models\CustomerNotification;
// use Botble\Base\Http\Middleware\LocaleMiddleware;
use GPBMetadata\Google\Api\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Middleware\MarketingLocaleMiddleware;
use App\Http\Middleware\CheckPermission;
use Botble\Base\Facades\BaseHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Vendor\NotificationController as VendorNotification;
use Illuminate\Support\Facades\Redirect;
use App\Models\VendorNotifications;
use Botble\Ecommerce\Http\Controllers\ExportWithdrawalController;
use Botble\Ecommerce\Models\Customer;
use Carbon\Carbon;

Route::resource('ghn', GhnController::class);
Route::put('ghn/update', [GhnController::class, 'update'])->name('ghn.update');
Route::post('/ghn/update-session', [GhnController::class, 'updateSession'])->name('ghn.update-sesion');
Route::post('/ghn/update-shipment/{id}', [GhnController::class, 'updateShipment'])->name('ghn.update-shipment');
Route::post('/ghn/cancel-order/{id}', [GhnController::class, 'cancelOrder'])->name('ghn.cancel-order');

// -----------KYC--------
Route::prefix('admin/kyc')->group(function () {
    Route::get('/identity-form', [KycController::class, 'identityForm'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.identity-form.view'])
        ->name('kyc.form');

    Route::put('update', [KycController::class, 'update'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.identity-form.edit'])
        ->name('address.update');

    Route::post('/identity-form', [KycController::class, 'storeIdentityForm'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.identity-form.create'])
        ->name('kyc.form.create');

    Route::put('/identity-form/{id}', [KycController::class, 'updateIdentityForm'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.identity-form.edit'])
        ->name('kyc.form.update');

    Route::delete('/identity-form/{id}', [KycController::class, 'deleteIdentityForm'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.identity-form.delete'])
        ->name('kyc.form.delete');


    Route::get('/pending', [KycController::class, 'showPending'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.pending.view'])
        ->name('kyc.pending');

    Route::get('pending/view/{id}', [KycController::class, 'pendingview'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.pending.view'])
        ->name('kyc.pending.view');

    Route::patch('pending/approve/{id}', [KycController::class, 'pendingapprove'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.pending.edit'])
        ->name('kyc.pending.approve');

    Route::patch('pending/reject/{id}', [KycController::class, 'pendingreject'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.pending.edit'])
        ->name('kyc.pending.reject');


    Route::get('/reward', [KycController::class, 'reward'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.log'])
        ->name('kyc.reward');

    Route::get('/reward/update', [KycController::class, 'rewardupdate'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.log'])
        ->name('update.reward');

    Route::post('/reward/store', [KycController::class, 'rewardstore'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.log'])
        ->name('store.reward');

    Route::post('/reward/store', [KycController::class, 'rewardstore'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.log'])
        ->name('store.reward');

    Route::get('/kycreward', [KycController::class, 'rewardget'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.log'])
        ->name('reward.get');

    Route::get('/kycviewreward/{id}', [KycController::class, 'rewardview'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.log'])
        ->name('reward.view');


    Route::get('/log', [KycController::class, 'logs'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.log'])
        ->name('kyc.log');

    Route::get('kyc/log/{id}', [KycController::class, 'view'])
        ->middleware([CheckPermission::class . ':packages.mlm.kyc.log'])
        ->name('kyc.log.view');
});

Route::get('/admin/customers/{id}/edit-rank', [App\Http\Controllers\Admin\AdminController::class, 'editCustomerRank'])
    ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.edit'])
    ->name('customer.edit.rank');

Route::post('/admin/customers/{id}/update-rank', [App\Http\Controllers\Admin\AdminController::class, 'updateCustomerRank'])
    ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.edit'])
    ->name('customer.update.rank');

Route::post('/admin/customers/store-rank', [App\Http\Controllers\Admin\AdminController::class, 'storeCustomerRank'])
    ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.assign'])
    ->name('customer.store.rank');

Route::delete('/admin/customers/{id}/delete-rank', [App\Http\Controllers\Admin\AdminController::class, 'deleteCustomerRank'])
    ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.delete'])
    ->name('customer.delete.rank');

Route::prefix('admin/ranks')->group(function () {
    Route::get('/add', [AdminController::class, 'addranks'])
        ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.createrank'])
        ->name('rank.add');

    Route::get('/', [AdminController::class, 'indexranks'])
        ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.view'])
        ->name('rank.index');

    Route::post('/store', [AdminController::class, 'storeranks'])
        ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.createrank'])
        ->name('rank.store');

    Route::get('/edit/{id}', [AdminController::class, 'editranks'])
        ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.editrank'])
        ->name('rank.edit');

    Route::put('/update/{id}', [AdminController::class, 'updateranks'])
        ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.editrank'])
        ->name('rank.update');

    Route::delete('/delete/{id}', [AdminController::class, 'deleteranks'])
        ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.deleterank'])
        ->name('rank.delete');

    Route::post('/update-dayofsharing', [AdminController::class, 'updateDayOfSharing'])
        ->middleware([CheckPermission::class . ':packages.mlm.ranking_sharing.editrank'])
        ->name('admin.ranks.update.dayofsharing');
});
//admin-dailybonus
Route::prefix('admin/dailybonus')->group(function () {
    Route::get('', [DistributeDailyOrderBonusController::class, 'index'])
        ->middleware([CheckPermission::class . ':packages.mlm.daily_bonus.view'])
        ->name('dailybonusorder.index');

    Route::get('/edit', [DistributeDailyOrderBonusController::class, 'edit'])
        ->middleware([CheckPermission::class . ':packages.mlm.daily_bonus.edit'])
        ->name('dailybonusorder.edit');

    Route::put('/update', [DistributeDailyOrderBonusController::class, 'update'])
        ->middleware([CheckPermission::class . ':packages.mlm.daily_bonus.edit'])
        ->name('dailybonusorder.update');

    Route::get('/viewcustomer/{id}', [DistributeDailyOrderBonusController::class, 'customerview'])
        ->middleware([CheckPermission::class . ':packages.mlm.daily_bonus.view'])
        ->name('dailybonusorder.customerview');
});

Route::get('bitsgold');

Route::prefix('admin/referralcommission')->group(function () {
    Route::get('/', [ReferralCommissionController::class, 'index'])
        ->middleware([CheckPermission::class . ':packages.mlm.referral_commission.view'])
        ->name('referralcommission.index');
    Route::get('/edit', [ReferralCommissionController::class, 'editreferral'])
        ->middleware([CheckPermission::class . ':packages.mlm.referral_commission.edit'])
        ->name('referralcommission.edit');
    Route::put('/update', [ReferralCommissionController::class, 'update'])
        ->middleware([CheckPermission::class . ':packages.mlm.referral_commission.edit'])
        ->name('referralcommission.update');
    Route::get('/list', [ReferralCommissionController::class, 'list'])
        ->middleware([CheckPermission::class . ':packages.mlm.referral_commission.list'])
        ->name('referral-commissions.indexper');

    Route::get('/{order_id}/detail', [ReferralCommissionController::class, 'detail'])
        ->middleware([CheckPermission::class . ':packages.mlm.referral_commission.detail'])
        ->name('referral-commissions.detail');
});

Route::prefix('admin/active_account')->group(function () {
    Route::get('/', [ReferralCommissionController::class, 'indexActiveAccount'])
        ->middleware([CheckPermission::class . ':packages.mlm.account_activation.view'])
        ->name('active_account.index');

    Route::get('/edit', [ReferralCommissionController::class, 'editActiveAccount'])
        ->middleware([CheckPermission::class . ':packages.mlm.account_activation.edit'])
        ->name('active_account.edit');

    Route::put('/upadte', [ReferralCommissionController::class, 'updateActiveAccount'])
        ->middleware([CheckPermission::class . ':packages.mlm.account_activation.edit'])
        ->name('active_account.update');
});

Route::prefix('admin/totalrevenue')->group(function () {
    Route::get('/', [TotalRevenueController::class, 'index'])->name('totalrevenue.index');
    Route::get('/add', [TotalRevenueController::class, 'add'])->name('totalrevenue.add');
    Route::post('/store', [TotalRevenueController::class, 'store'])->name('totalrevenue.store');
    Route::get('/edit/{id}', [TotalRevenueController::class, 'edit'])->name('totalrevenue.edit');
    Route::put('/update/{id}', [TotalRevenueController::class, 'update'])->name('totalrevenue.update');
    Route::delete('/delete/{id}', [TotalRevenueController::class, 'destroy'])->name('totalrevenue.delete');
});

Route::prefix('admin/referral')->group(function () {
    Route::get('/', [ReferralController::class, 'index'])->name('referral.index');
    Route::post('/save', [ReferralController::class, 'save'])->name('referral.save');
    Route::post('/action', [ReferralController::class, 'action'])->name('referral.action');
});
Route::prefix('admin/referrals')->group(function () {
    Route::get('/', [ReferralController::class, 'referrals'])
        ->middleware([CheckPermission::class . ':packages.mlm.referrers'])
        ->name('referrals');

    Route::get('/children', [ReferralController::class, 'children_referrals'])
        ->middleware([CheckPermission::class . ':packages.mlm.referrers'])
        ->name('children_referrals');
});

Route::get('/register/{username}', [RegisterReferralController::class, 'showRegistrationForm'])->name('register.referral.get');

Route::prefix('admin/report')->group(function () {
    Route::get('/', [ReportController::class, 'index'])
        ->middleware([CheckPermission::class . ':packages.mlm.reports'])
        ->name('report.index');

    Route::post('/', [ReportController::class, 'getChartByDays'])
        ->middleware([CheckPermission::class . ':packages.mlm.reports'])
        ->name('report.days');
});

Route::prefix('admin/export/report')->group(function () {
    Route::get('/', [ExportReportController::class, 'index'])->name('export.index');
    Route::post('/', [ExportReportController::class, 'store'])->name('export.store');
});

Route::prefix('admin/export/withdrawal')->group(function () {
    Route::get('/', [ExportWithdrawalController::class, 'index'])->name('export.withdrawal.index');
    Route::post('/', [ExportWithdrawalController::class, 'store'])->name('export.withdrawal.store');
});

Route::middleware([LocaleMiddleware::class])->get('/notifications/latest', function () {
    $customerCheck = auth('customer')->check();
    $customer = auth('customer')->user();

    if (!$customerCheck) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
    }

    $latestNotification = CustomerNotification::where('customer_id', $customer->id)
        ->where('viewed', 0)
        ->latest()
        ->first();

    if ($latestNotification) {
        $latestNotification->update(['viewed' => 1]); // Đánh dấu đã xem
    }

    if ($latestNotification && $latestNotification->dessription) {

        $vars = json_decode($latestNotification->variables, true);

        $formattedVars = [];

        foreach ($vars ?? [] as $key => $value) {
            if (
                is_numeric($value)
                && (
                    $key === 'amount'
                    || $key === 'price'
                    || $key === 'total'
                    || $key === 'fee'
                    || Str::endsWith($key, ['_amount', '_price', '_fee', '_total'])
                )
            ) {
                $formattedVars[$key] = format_price((float) $value);
            } elseif (Str::contains($key, 'date') || Str::endsWith($key, ['_at', '_date', '_time'])) {
                $formattedVars[$key] = $value instanceof \Carbon\Carbon
                    ? $value->format('d/m/Y H:i')
                    : (string) $value;
            } else {
                $formattedVars[$key] = $value;
            }
        }

        $descriptionKey = $latestNotification->dessription;
        if (! Str::contains($descriptionKey, '::')) {
            $descriptionKey = 'core/base::layouts.' . $descriptionKey;
        }

        $translatedDescription = trans($descriptionKey, $formattedVars);
    } else {
        $translatedDescription = null;
    }

    return response()->json([
        'status' => 'success',
        'notification' => $latestNotification ? [
            'id' => $latestNotification->id,
            'description' => $translatedDescription
        ] : null
    ]);
});

Route::prefix('admin/withdrawal-marketing')->group(function () {
    Route::get('', [AdCustomerWithdrawalController::class, 'index'])
        ->middleware([CheckPermission::class . ':packages.mlm.marketing_withdrawals.view'])
        ->name('withdrawals-manager.index');

    Route::get('edit/{id}', [AdCustomerWithdrawalController::class, 'edit'])
        ->middleware([CheckPermission::class . ':packages.mlm.marketing_withdrawals.edit'])
        ->name('withdrawals-manager.edit');

    Route::put('update/{id}', [AdCustomerWithdrawalController::class, 'update'])
        ->middleware([CheckPermission::class . ':packages.mlm.marketing_withdrawals.edit'])
        ->name('withdrawals-manager.update');

    Route::delete('delete/{id}', [AdCustomerWithdrawalController::class, 'destroy'])
        ->middleware([CheckPermission::class . ':packages.mlm.marketing_withdrawals.delete'])
        ->name('withdrawals-manager.destroy');
});

Route::prefix('admin/reward-history')->group(function () {
    Route::get('', [RewardHistoryController::class, 'index'])
        ->middleware([CheckPermission::class . ':packages.mlm.profit_sharing_history'])
        ->name('reward-history.index');
});


Route::post('/hooks/deposit', DepositWebhookController::class)
    ->withoutMiddleware(['web', 'auth', 'verified', 'throttle', 'csrf']);

Route::post('/hooks/store-order', StoreOrderWebhookController::class)
    ->withoutMiddleware(['web', 'auth', 'verified', 'throttle', 'csrf']);

Route::post('/sepay/webhook/withdrawals', WithdrawalWebhookController::class)
    ->withoutMiddleware(['web', 'auth', 'verified', 'throttle', 'csrf']);

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Route danh sách Manager
    Route::get('manager', [ManagerController::class, 'index'])
        ->middleware([CheckPermission::class . ':packages.mlm.region.view'])
        ->name('manager.index');

    // Route tạo mới Manager
    Route::get('manager/create', [ManagerController::class, 'create'])
        ->middleware([CheckPermission::class . ':packages.mlm.region.create'])
        ->name('manager.create');
    Route::post('manager', [ManagerController::class, 'store'])
        ->middleware([CheckPermission::class . ':packages.mlm.region.create'])
        ->name('manager.store');

    // Route sửa Manager
    Route::get('manager/{customer_id}/{manager_name}/edit', [ManagerController::class, 'edit'])
        ->middleware([CheckPermission::class . ':packages.mlm.region.edit'])
        ->name('manager.edit');
    Route::put('manager/{customer_id}/{manager_name}', [ManagerController::class, 'update'])
        ->middleware([CheckPermission::class . ':packages.mlm.region.edit'])
        ->name('manager.update');

    // Route xóa Manager
    Route::delete('manager/{hash}', [ManagerController::class, 'destroy'])
        ->middleware([CheckPermission::class . ':packages.mlm.region.delete'])
        ->name('manager.destroy');
});

foreach (['/marketing', '{locale}/marketing'] as $prefix) {
    $isLocalized = $prefix !== '/marketing';
    $suffix = $isLocalized ? '.locale' : '';

    Route::prefix($prefix)
        ->middleware(MarketingLocaleMiddleware::class)
        ->group(function () use ($suffix) {

            // BitsgoldController routes
            Route::controller(BitsgoldController::class)->group(function () use ($suffix) {
                Route::get('dashboard', 'dashboard')->name('bitsgold.dashboard' . $suffix);
                Route::get('plan', 'plan')->name('bitsgold.plan' . $suffix);
                Route::get('invest-history', 'invest_history')->name('bitsgold.invest_history' . $suffix);
                Route::get('add-fund', 'add_fund')->name('bitsgold.add_fund' . $suffix);
                Route::get('transaction', 'transaction')->name('bitsgold.transaction' . $suffix);
                Route::get('referral', 'referral')->name('bitsgold.referral' . $suffix);
                Route::get('kyc-bonus', 'kyc_bonus')->name('bitsgold.kyc_bonus' . $suffix);
                Route::get('wallet-history', 'walletHistory')->name('bitsgold.wallet_history' . $suffix);
            });


            // ClientController export
            Route::get('export-customers', [ClientController::class, 'exportCustomers'])->name('export.customers' . $suffix);

            // ClientController report (auth)

            Route::middleware(['auth:customer'])->group(function () use ($suffix) {
                Route::get('/report', [ClientController::class, 'dashboard'])->name('client.dashboard' . $suffix);
                Route::get('/banking', [WalletTransferController::class, 'index'])->name('wallet.transfer' . $suffix);
                Route::post('/banking', [WalletTransferController::class, 'store'])->name('wallet.transfer.store' . $suffix);
                Route::get('/banking/recipient', [WalletTransferController::class, 'recipient'])->name('wallet.transfer.recipient' . $suffix);
                // Route::get('/re', [ClientController::class, 'dashboard'])->name('customer.dashboard' . $suffix);
            });

            // KYC Routes
            Route::controller(KycCustomerController::class)->group(function () use ($suffix) {
                Route::get('/approvekyc', 'index')->name('kyc.index' . $suffix);
                Route::post('/submit', 'submit')->name('kyc.submit' . $suffix);
                Route::get('/rewardkyc', 'reward')->name('reward.customer' . $suffix);
            });

            // Bank account routes
            Route::prefix('bank-account')->group(function () use ($suffix) {
                Route::get('/', [BankAccountController::class, 'index'])->name('bank_accounts.index' . $suffix);
                Route::post('/', [BankAccountController::class, 'store'])->name('bank_accounts.store' . $suffix);
                Route::put('{id}', [BankAccountController::class, 'update'])->name('bank_accounts.update' . $suffix);
                Route::delete('{id}', [BankAccountController::class, 'destroy'])->name('bank_accounts.destroy' . $suffix);
            });

            // Withdrawals routes
            Route::prefix('withdrawals')->group(function () use ($suffix) {
                Route::get('customer', [CustomerWithdrawalController::class, 'index'])->name('withdrawals.index' . $suffix);
                Route::get('create', [CustomerWithdrawalController::class, 'create'])->name('withdrawals.create' . $suffix);
                Route::post('store', [CustomerWithdrawalController::class, 'store'])->name('withdrawals.store' . $suffix);
                Route::get('show/{id}', [CustomerWithdrawalController::class, 'show'])->name('withdrawals.show' . $suffix);

                Route::get('setup-sepay', [CustomerWithdrawalController::class, 'setupSepay'])->name('withdrawals.setup-sepay' . $suffix);
                Route::get('edit-setup-sepay', [CustomerWithdrawalController::class, 'editSetupSepay'])->name('withdrawals.edit-setup-sepay' . $suffix);
                Route::post('setup-sepay', [CustomerWithdrawalController::class, 'postSetupSepay'])->name('withdrawals.post-setup-sepay' . $suffix);
                Route::put('edit-setup-sepay', [CustomerWithdrawalController::class, 'putSetupSepay'])->name('withdrawals.put-setup-sepay' . $suffix);
            });

            // Deposit routes
            Route::prefix('deposit')->group(function () use ($suffix) {
                Route::get('index', [DepositHistoryController::class, 'index'])->name('deposit.index' . $suffix);
                Route::get('create', [DepositHistoryController::class, 'create'])->name('deposit.create' . $suffix);
                Route::post('store', [DepositHistoryController::class, 'store'])->name('deposit.store' . $suffix);
                Route::get('create-success/{id}', [DepositHistoryController::class, 'show'])->name('deposit.show' . $suffix);
                Route::post('check-status/{id}', [DepositHistoryController::class, 'checkStatus'])->name('deposit.checkStatus' . $suffix);
            });
        });
}

foreach (['marketing/manager', '{locale}/marketing/manager'] as $prefix) {
    $isLocalized = $prefix !== 'marketing/manager';
    $suffix = $isLocalized ? '.locale' : '';

    Route::prefix($prefix)
        ->name('maketing.manager.')
        ->middleware(MarketingLocaleMiddleware::class)
        ->group(function () use ($suffix) {
            Route::get('index', [ClientManagerController::class, 'index'])->name('index' . $suffix);
        });
}

Route::get('/referrals/{id}', [BitsgoldController::class, 'loadChildren'])
    ->middleware(MarketingLocaleMiddleware::class);

Route::get('/setting/account', [MobileLayoutController::class, 'index'])
    ->middleware(MarketingLocaleMiddleware::class)->name('setting');

// card
// Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => ['web', 'core']], function () {
//   Route::group(['prefix' => 'cards', 'as' => 'cards.'], function () {
//       Route::resource('/', CardController::class)->parameters(['' => 'card']);

//       Route::get('view/{id}', [CardController::class, 'show'])
//           ->name('view')
//           ->middleware(['permission:packages.mlm.card.view']);
//   });
// });

// foreach (['maketing/list_card', '{locale}/maketing/list_card'] as $uri) {
//   $isLocalized = $uri !== 'maketing/list_card';
//   $suffix = $isLocalized ? '.locale' : '';

//   Route::get($uri, [CardController::class, 'list_card'])
//       ->name('list_card' . $suffix)
//       ->middleware(MarketingLocaleMiddleware::class);
// }

//kho bãi
Route::resource('admin/store-levels', StoreLevelController::class)
    ->middleware([CheckPermission::class . ':packages.mlm.store-levels.index']);

Route::get('admin/liststore', [StoreLevelController::class, 'listStore'])->name('liststore')->middleware(middleware: [CheckPermission::class . ':packages.mlm.store-levels.list']);
Route::get('admin/store-levels/stores/{store}', [StoreLevelController::class, 'showStore'])
    ->name('store-levels.stores.show')
    ->middleware([CheckPermission::class . ':packages.mlm.store-levels.index']);

Route::get('admin/store-levels/assign', [StoreLevelController::class, 'assignForm'])
    ->name('store-levels.assign.form')
    ->middleware(middleware: [CheckPermission::class . ':packages.mlm.store-levels.formedit']);

Route::post('admin/store-levels/assign', [StoreLevelController::class, 'assign'])->name('store-levels.assign.edit');
// Route::post('admin/store-levels/assign',[StoreLevelController::class,'assign'])->name('store-levels.assign');


Route::middleware(['web', 'core', 'vendor', LocaleMiddleware::class])
    ->prefix('vendor/storemanager')
    ->name('marketplace.vendor.store-manager.')
    ->group(function () {
        Route::get('index', [MpManagerController::class, 'index'])
            ->name('index');

        Route::get('show/{id}', [MpManagerController::class, 'show'])
            ->name('show');
    });

Route::middleware(['web', 'core', 'vendor', LocaleMiddleware::class])
    ->prefix('vendor/store-order')
    ->name('marketplace.vendor.store-orders.')
    ->group(function () {
        Route::get('index', [MpStoreOrderController::class, 'index'])
            ->name('index');

        Route::get('create', [MpStoreOrderController::class, 'create'])
            ->name('create');

        Route::post('store', [MpStoreOrderController::class, 'store'])
            ->name('store');

        Route::get('checkout/{id}', [MpStoreOrderController::class, 'checkout'])
            ->name('checkout');

        Route::post('check-status/{id}', [MpStoreOrderController::class, 'checkStatus'])->name('check-status');

        Route::get('show/{id}', [MpStoreOrderController::class, 'show'])
            ->name('show');

        Route::get('{transaction_code}/edit', [MpStoreOrderController::class, 'edit'])->name('edit');
        Route::put('{transaction_code}', [MpStoreOrderController::class, 'update'])->name('update');
        Route::get('{transaction_code}/view', [MpStoreOrderController::class, 'view'])->name('view');
        Route::get('/store-orders/check-new', [MpStoreOrderController::class, 'checkNewOrders'])->name('check-new');
        Route::post('vendor/store-orders/{id}/confirm-import', [
            MpStoreOrderController::class,
            'confirmImportStock'
        ])->middleware('auth:customer')->name('confirm-import');
    });




Route::post('/save-fcm-token', function (Request $request) {
    $user = auth('customer')->user();
    if ($user && $request->token) {
        $user->fcm_token = $request->token;
        $user->save();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 401);
});



// Route::prefix('store-order')->name('marketplace.vendor.store-orders.')->group(function () {
//     Route::get('{transaction_code}/edit', [MpStoreOrderController::class, 'edit'])->name('edit');
//     Route::put('{transaction_code}', [MpStoreOrderController::class, 'update'])->name('update');
// });

// Route::get('store-order/{transaction_code}/view', [MpStoreOrderController::class, 'view'])->name('marketplace.vendor.store-orders.view');

// foreach (['store-order', '{locale}/store-order'] as $prefix) {
//   $isLocalized = $prefix !== 'store-order';
//   $namePrefix = $isLocalized ? 'marketplace.vendor.store-orders.localized.' : 'marketplace.vendor.store-orders.';

//   Route::prefix($prefix)
//     ->name($namePrefix)
//     ->group(function () {
//       Route::get('{transaction_code}/edit', [MpStoreOrderController::class, 'edit'])->name('edit');
//       Route::put('{transaction_code}', [MpStoreOrderController::class, 'update'])->name('update');
//       Route::get('{transaction_code}/view', [MpStoreOrderController::class, 'view'])->name('view');
//     });
// }

//xác nhận nahap kho

// Route::post('vendor/store-orders/{id}/confirm-import', [
//     MpStoreOrderController::class,
//     'confirmImportStock'
// ])->middleware('auth:customer')->name('marketplace.vendor.store-orders.confirm-import');



Route::get('/admin/store-orders/pending-bonus', [AdminStoreOrderController::class, 'listPendingBonus'])
    ->name('admin.store-orders.pending-bonus')
    ->middleware(middleware: [CheckPermission::class . ':packages.mlm.store-orders.pending-bonus']);

Route::get('/admin/store-orders/{id}/view', [AdminStoreOrderController::class, 'view'])
    ->name('admin.store-orders.view')
    ->middleware(middleware: [CheckPermission::class . ':packages.mlm.store-orders.pending-bonus.view']);

Route::post('/admin/store-orders/{id}/confirm-bonus', [AdminStoreOrderController::class, 'confirmBonus'])
    ->name('admin.store-orders.confirm-bonus')
    ->middleware(middleware: [CheckPermission::class . ':packages.mlm.store-orders.pending-bonus.confirm-bonus']);



Route::get('/admin/store-orders/history-bonus', [AdminStoreOrderController::class, 'listHistoryBonus'])
    ->name('admin.store-orders.history-bonus')
    ->middleware(middleware: [CheckPermission::class . ':packages.mlm.store-orders.history-bonus']);

Route::get('/admin/store-orders/expired-pending', [AdminStoreOrderController::class, 'listExpiredPending'])
    ->name('admin.store-orders.expired-pending')
    ->middleware(middleware: [CheckPermission::class . ':packages.mlm.store-orders.expired-pending']);

Route::get('/admin/store-orders/{id}/edit-from-store', [AdminStoreOrderController::class, 'editFromStore'])
    ->name('admin.store-orders.edit-from-store')
    ->middleware(middleware: [CheckPermission::class . ':packages.mlm.store-orders.edit-from-store']);

Route::post('/admin/store-orders/{id}/update-from-store', [AdminStoreOrderController::class, 'updateFromStore'])
    ->name('admin.store-orders.update-from-store')
    ->middleware(middleware: [CheckPermission::class . ':packages.mlm.store-orders.update-from-store']);




Route::prefix('admin/store-orders')->name('admin.store-orders.')->group(function () {
    Route::get('factory-orders', [AdminStoreOrderController::class, 'listFactoryOrders'])->name('factory-orders')
        ->middleware(middleware: [CheckPermission::class . ':packages.mlm.factory-orders']);
    Route::get('factory-orders/{id}', [AdminStoreOrderController::class, 'viewFactoryOrder'])->name('factory-view')
        ->middleware(middleware: [CheckPermission::class . ':packages.mlm.factory-view']);
    Route::post('factory-orders/{id}/update-status', [AdminStoreOrderController::class, 'updateFactoryOrderStatus'])->name('factory-update-status')
        ->middleware(middleware: [CheckPermission::class . ':packages.mlm.factory-update-status']);
    Route::post('factory-orders/{id}/update-payment-status', [AdminStoreOrderController::class, 'updateFactoryPaymentStatus'])->name('factory-update-payment-status')
        ->middleware(middleware: [CheckPermission::class . ':packages.mlm.factory-update-status']);
});

Route::middleware(['web', 'core', 'vendor', LocaleMiddleware::class])->prefix('vendor')->name('marketplace.vendor.')->controller(VendorNotification::class)->group(function () {
    Route::get('notification', 'notification')->name('notification');
});

Route::prefix('admin/store-to-user')->group(function () {
    Route::get('/', [ConfirmVendorShipedToUserController::class, 'index'])
        ->middleware([CheckPermission::class . ':packages.mlm.store-to-user.index'])
        ->name('store-to-user.index');

    Route::get('edit/{id}', [ConfirmVendorShipedToUserController::class, 'edit'])
        ->middleware([CheckPermission::class . ':packages.mlm.store-to-user.edit'])
        ->name('store-to-user.edit');

    Route::put('update/{id}', [ConfirmVendorShipedToUserController::class, 'update'])
        ->middleware([CheckPermission::class . ':packages.mlm.store-to-user.update'])
        ->name('store-to-user.update');

    Route::delete('delete/{id}', [ConfirmVendorShipedToUserController::class, 'delete'])
        ->middleware([CheckPermission::class . ':packages.mlm.store-to-user.delete'])
        ->name('store-to-user.delete');
});


Route::middleware(['web', 'core', 'vendor', LocaleMiddleware::class])
    ->prefix('vendor/store-order')
    ->name('store-orders.')
    ->group(function () {

        // Xác nhận giao và nhập kho tự động
        Route::post('{id}/auto-delivery', [MpStoreOrderController::class, 'buconfirmDelivery'])->name('auto-delivery');
        Route::post('{id}/auto-import', [MpStoreOrderController::class, 'confirmImportStock'])->name('auto-import');

        // Danh sách các đơn chờ xử lý
        Route::get('auto-delivery-list', [MpStoreOrderController::class, 'listPendingDelivery'])->name('auto-delivery-list');
        Route::get('auto-import-list', [MpStoreOrderController::class, 'listPendingImport'])->name('auto-import-list');

        // View nhập kho tự động
        Route::get('auto-import-view/{id}', [MpStoreOrderController::class, 'autoImportView'])->name('auto-import-view');
    });


Route::middleware(['web', 'core', 'vendor', LocaleMiddleware::class])
    ->prefix('vendor/replenish-orders')
    ->name('marketplace.replenish-orders.')
    ->group(function () {
        Route::get('{transaction_code}/edit', [MpStoreOrderController::class, 'buedit'])->name('buedit');
        Route::put('{transaction_code}', [MpStoreOrderController::class, 'buupdate'])->name('buupdate');
    });

Route::middleware(['web', 'core', 'vendor', LocaleMiddleware::class])
    ->prefix('vendor/shipped-to-user')
    ->name('marketplace.shipped-to-user.')
    ->group(function () {
        Route::get('', [VendorConfirmVendorShipedToUserController::class, 'index'])->name('index');
        Route::get('show/{id}', [VendorConfirmVendorShipedToUserController::class, 'edit'])->name('edit');
    });
Route::get('/notifications/redirect/{id}', function ($id) {
    $notification = VendorNotifications::findOrFail($id);

    // Tăng view (tuỳ theo tên trường bạn dùng)
    $notification->increment('viewed');

    $url = $notification->url;

    // Kiểm tra nếu là URL nội bộ thì đảm bảo an toàn
    if (!Str::startsWith($url, ['http://', 'https://'])) {
        $url = url($url); // Chuyển về absolute URL
    }

    return Redirect::to($url);
})->name('notifications.redirect');

Route::prefix('admin/vendor-late-deliveries')->group(function () {
    Route::match(['GET', 'POST'], '/', [VendorLateDeliveryController::class, 'index'])
        ->middleware([CheckPermission::class . ':packages.mlm.vendor-late-delivery.index'])
        ->name('vendor-late-delivery.index');

    Route::get('edit/{id}', [VendorLateDeliveryController::class, 'edit'])
        ->middleware([CheckPermission::class . ':packages.mlm.vendor-late-delivery.edit'])
        ->name('vendor-late-delivery.edit');

    Route::put('update/{token}', [VendorLateDeliveryController::class, 'update'])
        ->middleware([CheckPermission::class . ':packages.mlm.vendor-late-delivery.update'])
        ->name('vendor-late-delivery.update');

    Route::delete('delete/{id}', [VendorLateDeliveryController::class, 'delete'])
        ->middleware([CheckPermission::class . ':packages.mlm.vendor-late-delivery.delete'])
        ->name('vendor-late-delivery.delete');
});

Route::get('/api/customer/search-by-uuid', function (\Illuminate\Http\Request $request) {
    $uuid = $request->get('uuid');

    $customer = \Botble\Ecommerce\Models\Customer::where('uuid_code', $uuid)->first();

    if (!$customer) {
        return response()->json(['message' => 'Không tìm thấy'], 404);
    }

    return response()->json([
        'name' => $customer->name,
        'phone' => $customer->phone,
    ]);
});

// Route::get('/api/customer/search-by-phone', function (\Illuminate\Http\Request $request) {
//     $phone = $request->get('phone');

//     $customer = \Botble\Ecommerce\Models\Customer::where('phone', $phone)->first();

//     if (!$customer) {
//         return response()->json(['message' => 'Không tìm thấy'], 404);
//     }

//     return response()->json([
//         'name' => $customer->name,
//         'phone' => $customer->phone,
//     ]);
// });

Route::get('/api/customer/search-by-phone', function (Request $request) {
    $phone = $request->get('phone');

    if (! $phone) {
        return response()->json(['message' => 'Thiếu số điện thoại'], 400);
    }

    // Chuẩn hóa số điện thoại: nếu bắt đầu bằng 0, đổi thành +84
    $normalizedPhone = Str::startsWith($phone, '0')
        ? preg_replace('/^0/', '+84', $phone)
        : $phone;

    // Tìm theo dạng +84 hoặc 0
    $customer = Customer::where(function ($query) use ($phone, $normalizedPhone) {
        $query->where('phone', $phone)
            ->orWhere('phone', $normalizedPhone)
            ->orWhere('phone', preg_replace('/^\+84/', '0', $normalizedPhone)); // Trường hợp ngược lại
    })->first();

    if (! $customer) {
        return response()->json(['message' => 'Không tìm thấy'], 404);
    }

    return response()->json([
        'name' => $customer->name,
        'phone' => $customer->phone,
    ]);
});
