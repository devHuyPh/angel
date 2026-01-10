<?php

namespace Botble\Dashboard\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Dashboard\Models\DashboardWidget;
use Botble\Dashboard\Models\DashboardWidgetSetting;
use Botble\Dashboard\Repositories\Eloquent\DashboardWidgetRepository;
use Botble\Dashboard\Repositories\Eloquent\DashboardWidgetSettingRepository;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetSettingInterface;

/**
 * @since 02/07/2016 09:50 AM
 */
class DashboardServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(DashboardWidgetInterface::class, function () {
            return new DashboardWidgetRepository(new DashboardWidget());
        });

        $this->app->bind(DashboardWidgetSettingInterface::class, function () {
            return new DashboardWidgetSettingRepository(new DashboardWidgetSetting());
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('core/dashboard')
            ->loadHelpers()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets()
            ->loadMigrations();

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-dashboard')
                        ->priority(-9999)
                        ->name('core/base::layouts.dashboard')
                        ->icon('ti ti-home')
                        ->route('dashboard.index')
                        ->permissions(false)
                )

                //kyc
                ->registerItem(
                    [
                        'id' => 'cms-core-kyc',
                        'priority' => 0,
                        'name' => 'core/base::layouts.kyc',
                        'icon' => 'ti ti-brand-ycombinator',
                        'url' => fn() => route('dashboard.index'),
                        'permissions' => ['packages.mlm.kyc'],
                    ]
                )
                ->registerItem(
                    [
                        'id' => 'cms-core-kyc-form',
                        'priority' => 0,
                        'name' => 'core/base::layouts.kyc_form',
                        'parent_id' => 'cms-core-kyc',
                        'icon' => 'ti ti-brand-ycombinator',
                        'url' => fn() => route('kyc.form'),
                        'permissions' => ['packages.mlm.kyc.identity-form.view'],
                    ]
                )
                ->registerItem(
                    [
                        'id' => 'cms-core-kyc-pending',
                        'priority' => 0,
                        'name' => 'core/base::layouts.kyc_pending',
                        'parent_id' => 'cms-core-kyc',
                        'icon' => 'ti ti-loader-3',
                        'url' => fn() => route('kyc.pending'),
                        'permissions' => ['packages.mlm.kyc.pending.view'],
                    ]
                )
                ->registerItem(
                    [
                        'id' => 'cms-core-kyc-log',
                        'priority' => 0,
                        'name' => 'core/base::layouts.kyc_log',
                        'parent_id' => 'cms-core-kyc',
                        'icon' => 'ti ti-file',
                        'url' => fn() => route('kyc.log'),
                        'permissions' => ['packages.mlm.kyc.log'],
                    ]
                )
                //endkyc
                // ->registerItem(
                //     [
                //         'id' => 'kyc_reward_history_type',
                //         'priority' => 0,
                //         'name' => 'core/base::layouts.kyc_reward_history',
                //         'parent_id' => 'cms-core-kyc',
                //         'icon' => 'ti ti-gift',
                //         'url' => fn() => route('reward.get'),
                //         'permissions' => ['plugins.ecommerce'],
                //     ])

                //daily-bonus
                // ->registerItem(
                // [
                // 'id' => 'cms-core-daily-bonus',
                // 'priority' => 0,
                // 'name' => 'core/base::layouts.daily-bonus-order',
                // 'icon' => 'ti ti-wallet',
                // 'url' => fn() => route('dashboard.index'),
                // 'permissions' => ['plugins.ecommerce'],
                // ]
                // )
                //            ->registerItem(
                //                         [
                //                             'id' => 'cms-core-daily-bonus',
                //                             'priority' => 0,
                //                             'name' => 'core/base::layouts.daily-bonus-order',
                //                             'icon' => 'ti ti-brand-ycombinator',
                //                             'url' => fn() => route('dashboard.index'),
                //                             'permissions' => ['plugins.ecommerce'],
                //                         ]
                //                     )

                // ->registerItem(
                //     [
                //         'id' => 'cms-core-daily-bonus_log',
                //         'priority' => 0,
                //         'name' => 'core/base::layouts.daily_bonus',
                //         'parent_id'=> 'cms-core-daily-bonus',
                //         'icon' => 'fa fa-trophy',
                //         'url' => fn() => route('dailybonusorder.log'),
                //         'permissions' => ['plugins.ecommerce'],
                //     ]
                // )
                //-------mlm
                ->registerItem(
                    [
                        'id' => 'cms-core-maketing',
                        'priority' => 0,
                        'name' => 'core/base::layouts.marketing_management',
                        'icon' => 'ti ti-ad-2',
                        'url' => fn() => route('dashboard.index'),
                        'permissions' => ['packages.mlm'],
                    ]
                )
                // report
                ->registerItem(
                    [
                        'id' => 'cms-core-report',
                        'priority' => 0,
                        'name' => 'core/base::layouts.report',
                        'parent_id' => 'cms-core-maketing',
                        'icon' => 'ti ti-report',
                        'url' => fn() => route('report.index'),
                        'permissions' => ['packages.mlm.reports'],
                    ]
                )
                // rank_and_profit
                ->registerItem(
                    [
                        'id' => 'cms-core-rank',
                        'priority' => 1,
                        'name' => 'core/base::layouts.rank_and_profit',
                        'parent_id' => 'cms-core-maketing',
                        'icon' => 'ti ti-brand-sketch',
                        'url' => fn() => route('rank.index'),
                        'permissions' => ['packages.mlm.ranking_sharing.view'],
                    ]
                )

                ->registerItem(
                    [
                        'id' => 'cms-core-store-kho',
                        'priority' => 1,
                        'name' => 'core/base::kho.Kho Bai',
                        // 'parent_id' => 'cms-core-maketing',
                        'icon' => 'ti ti-building-warehouse',
                        'url' => fn() => route('store-levels.index'),
                        'permissions' => ['packages.mlm.store-levels'],
                    ]
                )

                //kho
                ->registerItem(
                    [
                        'id' => 'cms-core-store-levels',
                        'priority' => 1,
                        'name' => 'core/base::kho.Danh Sach Kho Bai',
                        'parent_id' => 'cms-core-store-kho',
                        'icon' => 'ti ti-building-warehouse',
                        'url' => fn() => route('store-levels.index'),
                        'permissions' => ['packages.mlm.store-levels.index'],
                    ]
                )
                ->registerItem(
                    [
                        'id' => 'cms-core-delivery-bonus',
                        'priority' => 1,
                        'name' => 'core/base::kho.Thuong Giao Hang',
                        'parent_id' => 'cms-core-store-kho',
                        'icon' => 'ti ti-sort-ascending-shapes',
                        'url' => fn() => route('admin.store-orders.pending-bonus'),
                        'permissions' => ['packages.mlm.store-orders.pending-bonus'],
                    ]
                )
                // ->registerItem(
                //     [
                //         'id' => 'cms-core-kho-peding',
                //         'priority' => 1,
                //         'name' => 'core/base::kho.Kho chua xac nhan giao hang',
                //         'parent_id' => 'cms-core-store-kho',
                //         'icon' => 'ti ti-calendar',
                //         'url' => fn() => route('admin.store-orders.expired-pending'),
                //         'permissions' => ['packages.mlm.store-orders.expired-pending'],
                //     ]
                // )
                ->registerItem(
                    [
                        'id' => 'cms-core-nhamay',
                        'priority' => 1,
                        'name' => 'core/base::kho.Don hang tu nha may',
                        'parent_id' => 'cms-core-store-kho',
                        'icon' => 'ti ti-truck',
                        'url' => fn() => route('admin.store-orders.factory-orders'),
                        'permissions' => ['packages.mlm.factory-orders'],
                    ]
                )
            ->registerItem(
          [
            'id' => 'cms-core-history-bonus',
            'priority' => 1,
            'name' => 'Lịch sử thưởng giao hàng',
            'parent_id' => 'cms-core-store-kho',
            'icon' => 'ti ti-server',
            'url' => fn() => route('admin.store-orders.history-bonus'),
            'permissions' => ['packages.mlm.store-orders.history-bonus'],
          ]
        )
                ->registerItem(
                    [
                        'id' => 'cms-core-store-to-user',
                        'priority' => 1,
                        'name' => 'core/base::kho.order-store-to-user',
                        'parent_id' => 'cms-core-store-kho',
                        'icon' => 'ti ti-truck',
                        'url' => fn() => route('store-to-user.index'),
                        'permissions' => ['packages.mlm.store-to-user.index'],
                    ]
                )
                ->registerItem(
                    [
                        'id' => 'cms-core-vendor-late-delivery',
                        'priority' => 1,
                        'name' => 'core/base::kho.order-vendor-late-delivery',
                        'parent_id' => 'cms-core-store-kho',
                        'icon' => 'ti ti-target',
                        'url' => fn() => route('vendor-late-delivery.index'),
                        'permissions' => ['packages.mlm.vendor-late-delivery.index'],
                    ]
                )

                // referral
                ->registerItem(
                    [
                        'id' => 'cms-core-referrals',
                        'priority' => 2,
                        'name' => 'core/base::layouts.referrals',
                        'parent_id' => 'cms-core-maketing',
                        'icon' => 'ti ti-users',
                        'url' => fn() => route('referrals'),
                        'permissions' => ['packages.mlm.referrers'],
                    ]
                )
                //referral-commision
                ->registerItem(
                    [
                        'id' => 'cms-core-referral-commission',
                        'priority' => 3,
                        'name' => 'core/base::layouts.referral_commissions',
                        'parent_id' => 'cms-core-maketing',
                        'icon' => 'ti ti-percentage',
                        'url' => fn() => route('referralcommission.index'),
                        'permissions' => ['packages.mlm.referral_commission.view'],
                    ]
                )
                ->registerItem(
                    [
                        'id' => 'cms-core-referral-commissionper',
                        'priority' => 3,
                        'name' => 'core/base::layouts.referral_commissionsper',
                        'parent_id' => 'cms-core-maketing',
                        'icon' => 'ti ti-align-right',
                        'url' => fn() => route('referral-commissions.indexper'),
                        'permissions' => ['packages.mlm.referral_commission.list'],
                    ]
                )
                //card
                // ->registerItem(
                // [
                // 'id' => 'cms-core-card',
                // 'priority' => 1,
                // 'name' => 'core/base::layouts.card',
                // 'parent_id' => 'cms-core-maketing',
                // 'icon' => 'ti ti-credit-card',
                // 'url' => fn() => route('cards.index'),
                // 'permissions' => ['packages.mlm.card'],
                // ]
                // )
                //daily-bonus
                ->registerItem(
                    [
                        'id' => 'cms-core-bonus-index',
                        'priority' => 4,
                        'name' => 'core/base::layouts.daily-bonus',
                        'parent_id' => 'cms-core-maketing',
                        'icon' => 'ti ti-brand-ycombinator',
                        'url' => fn() => route('dailybonusorder.index'),
                        'permissions' => ['packages.mlm.daily_bonus.view'],
                    ]
                )
                //activeaccount
                ->registerItem(
                    [
                        'id' => 'cms-core-active-account',
                        'priority' => 5,
                        'name' => 'core/base::layouts.active_account',
                        'parent_id' => 'cms-core-maketing',
                        'icon' => 'fa fa-trophy',
                        'url' => fn() => route('active_account.index'),
                        'permissions' => ['packages.mlm.account_activation.view'],
                    ]
                )
                //reward-histories
                ->registerItem(
                    [
                        'id' => 'cms-core-reward-history',
                        'priority' => 6,
                        'name' => 'core/base::layouts.reward_history',
                        'parent_id' => 'cms-core-maketing',
                        'icon' => 'ti ti-bell-dollar',
                        'url' => fn() => route('reward-history.index'),
                        'permissions' => ['packages.mlm.profit_sharing_history'],
                    ]
                )
                //om
                // ->registerItem(
                //     [
                //         'id' => 'cms-core-manager',
                //         'priority' => 7,
                //         'name' => 'core/base::layouts.manager',
                //         'icon' => 'ti ti-brand-ycombinator',
                //         'parent_id' => 'cms-core-maketing',
                //         'url' => fn() => route('admin.manager.index'),
                //         'permissions' => ['packages.mlm.region.view'],
                //     ]
                // )
                //withdrawals_manager
                ->registerItem(
                    [
                        'id' => 'cms-core-withdrawals-manager',
                        'priority' => 8,
                        'name' => 'core/base::layouts.withdrawals_manager',
                        'parent_id' => 'cms-core-maketing',
                        'icon' => 'ti ti-bell-dollar',
                        'url' => fn() => route('withdrawals-manager.index'),
                        'permissions' => ['packages.mlm.marketing_withdrawals.view'],
                    ]
                )
                // ->registerItem(
                //     [
                //         'id' => 'cms-core-reward',
                //         'priority' => 0,
                //         'name' => 'core/base::layouts.kyc_reward',
                //         'parent_id' => 'cms-core-maketing',
                //         'icon' => 'ti ti-gift',
                //         'url' => fn() => route('kyc.reward'),
                //         'permissions' => ['plugins.ecommerce'],
                //     ]
                // )

                // ->registerItem(
                //     [
                //         'id' => 'cms-core-referral-commission',
                //         'priority' => 3,
                //         'name' => 'core/base::layouts.referral_commissions',
                //         'parent_id' => 'cms-core-maketing',
                //         'icon' => 'ti ti-percentage',
                //         'url' => fn() => route('referralcommission.index'),
                //         'permissions' => ['plugins.ecommerce'],
                //     ]
                // )

                // ->registerItem(
                //     [
                //         'id' => 'cms-core-total-revenue',
                //         'priority' => 0,
                //         'name' => 'core/base::layouts.revenue_based_discount',
                //         'parent_id' => 'cms-core-maketing',
                //         'icon' => 'ti ti-coin',
                //         'url' => fn() => route('totalrevenue.index'),
                //         'permissions' => ['plugins.ecommerce'],
                //     ]
                // )

                // ->registerItem(
                //     [
                //         'id' => 'cms-core-referral',
                //         'priority' => 0,
                //         'name' => 'core/base::layouts.referral',
                //         'parent_id' => 'cms-core-maketing',
                //         'icon' => 'ti ti-user',
                //         'url' => fn() => route('referral.index'),
                //         'permissions' => ['plugins.ecommerce'],
                //     ]
                // )

            ;
        });
    }
}
