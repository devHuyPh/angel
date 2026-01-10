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

                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-referral')
                        ->priority(-9999)
                        ->name('Referral')
                        ->icon('ti ti-user')
                        ->route('referral.index')
                        ->permissions(false)

                )

                ->registerItem(
                    [
                        'id' => 'cms-core-kyc',
                        'priority' => 0,
                        'name' => 'core/base::layouts.kyc',
                        'icon' => 'ti ti-brand-ycombinator',
                        'url' => fn() => route('dashboard.index'),
                        'permissions' => ['plugins.ecommerce'],
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
                        'permissions' => ['plugins.ecommerce'],
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
                        'permissions' => ['plugins.ecommerce'],
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
                        'permissions' => ['plugins.ecommerce'],
                    ])

                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-rank')
                        ->priority(-9999)
                        ->name('User Rankings')
                        ->icon('ti ti-brand-sketch')
                        ->route('rank.index')
                        ->permissions(false)

                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-referral-commission')
                        ->priority(-9999)
                        ->name('Referral Commissions')
                        ->icon('ti ti-percentage')
                        ->route('referralcommission.index')
                        ->permissions(false)
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-core-total-revenue')
                        ->priority(-9999)
                        ->name('Total Revenue')
                        ->icon('ti ti-coin')
                        ->route('totalrevenue.index')
                        ->permissions(false)
                )
                ;
        });
    }
}
