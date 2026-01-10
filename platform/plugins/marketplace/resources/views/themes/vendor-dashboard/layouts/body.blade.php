@php
  $customer = auth('customer')->user();
  if(!empty($customer->rank_id)) {

  $ranking = \App\Models\Ranking::findOrFail($customer->rank_id);
  }

@endphp

<header class="header--mobile" style="background: #228822">
  <div class="header__left">
    <button class="ps-drawer-toggle">
      <x-core::icon name="ti ti-menu-2" />
    </button>
  </div>
  <div class="header__center">
    <a class="ps-logo" href="{{ route('marketplace.vendor.dashboard') }}">
      @php $logo = theme_option('logo_vendor_dashboard', theme_option('logo')); @endphp
      @if ($logo)
        <img src="{{ RvMedia::getImageUrl($logo) }}" alt="{{ theme_option('site_title') }}">
      @endif
    </a>
  </div>
  <div class="header__right">
    <a class="header__site-link text-black" href="{{ route('customer.logout') }}">
      <x-core::icon name="ti ti-logout" />
    </a>
  </div>
</header>
<aside class="ps-drawer--mobile">
  <div class="ps-drawer__header">
    <h4 class="fs-3 mb-0">
      <div class="ps-main__sidebar">
        <div class="ps-sidebar">
          <!-- Bootstrap Responsive Sidebar with Mobile Optimizations -->
          <div class="card shadow-sm border-0">
            <!-- User Profile Section - Optimized for Mobile -->
            <div class="card-header bg-primary bg-gradient text-white">
              <div class="d-flex flex-column flex-md-row align-items-center">
                <div class="mb-3 mb-md-0 text-center text-md-start">
                  <img src="{{ $customer->store->logo_url }}" alt="{{ $customer->store->name }}" class="rounded-circle"
                    width="60" height="60">
                </div>
                <div class="ms-md-3 text-center text-md-start">
                  <h5 class="mb-0">{{ __('Hello') }}, {{ $customer->name }}</h5>
                  <small
                    class="text-white-50">{{ __('Joined on :date', ['date' => $customer->created_at->translatedFormat('M d, Y')]) }}</small>
                </div>
              </div>
            </div>
          </div>
          <div class="ps-sidebar__content">
            <div class="ps-sidebar__center">
              @include(MarketplaceHelper::viewPath('vendor-dashboard.layouts.menu'))
            </div>
            <div class="ps-sidebar__footer">
              <div class="ps-copyright">
                <!--@if ($logo)-->
                <!--  <a href="{{ BaseHelper::getHomepageUrl() }}" title="{{ $siteTitle = theme_option('site_title') }}">-->
                <!--    <img src="{{ RvMedia::getImageUrl($logo) }}" alt="{{ $siteTitle }}" style="max-height: 40px;">-->
                <!--  </a>-->
                <!--@endif-->
                <p>{!! BaseHelper::clean(str_replace('%Y', Carbon\Carbon::now()->year, theme_option('copyright'))) !!}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </h4>
    <button class="ps-drawer__close">
      <x-core::icon name="ti ti-x" />
    </button>
  </div>
  <div class="ps-drawer__content">
    @include(MarketplaceHelper::viewPath('vendor-dashboard.layouts.menu'))
  </div>
</aside>
<div class="ps-site-overlay"></div>
<main class="ps-main">
  <div class="ps-main__sidebar" style="margin-top: -60px;">
    <div class="ps-sidebar">
      <!-- Bootstrap Responsive Sidebar with Mobile Optimizations -->
      <div class="card shadow-sm border-0">
        <!-- User Profile Section - Optimized for Mobile -->
        <div class="card-header bg-primary bg-gradient text-white">
          <div class="d-flex flex-column flex-md-row align-items-center">
            <div class="mb-3 mb-md-0 text-center text-md-start">
              <img src="{{ $customer->store->logo_url }}" alt="{{ $customer->store->name }}" class="rounded-circle"
                width="60" height="60">
            </div>
            <div class="ms-md-3 text-center text-md-start">
              <h5 class="mb-0">{{ __('Hello') }}, {{ $customer->name }}</h5>
              <small
                class="text-white-50">{{ __('Joined on :date', ['date' => $customer->created_at->translatedFormat('M d, Y')]) }}</small>
            </div>
          </div>
        </div>
      </div>


      <div class="ps-sidebar__content">
        <div class="ps-sidebar__center">
          @include(MarketplaceHelper::viewPath('vendor-dashboard.layouts.menu'))
        </div>
        <div class="ps-sidebar__footer">
          <div class="ps-copyright">
            <!--@if ($logo)-->
            <!--  <a href="{{ BaseHelper::getHomepageUrl() }}" title="{{ $siteTitle = theme_option('site_title') }}">-->
            <!--    <img src="{{ RvMedia::getImageUrl($logo) }}" alt="{{ $siteTitle }}" style="max-height: 40px;">-->
            <!--  </a>-->
            <!--@endif-->
            <p>{!! BaseHelper::clean(str_replace('%Y', Carbon\Carbon::now()->year, theme_option('copyright'))) !!}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="ps-main__wrapper" id="vendor-dashboard">
    <header class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="fs-1 mb-0 text-truncate me-3 py-2">{{ mb_strtoupper(__(Request::segment(2))) }}</h3>
      <div class="d-flex align-items-center gap-4">
        @if (is_plugin_active('language'))
          {!! apply_filters(
              'marketplace_vendor_dashboard_language_switcher',
              view(MarketplaceHelper::viewPath('vendor-dashboard.partials.language-switcher'))->render(),
          ) !!}
        @endif

        <div class="d-none d-md-inline-block">
          <a href="{{ BaseHelper::getHomepageUrl() }}" target="_blank" class="text-uppercase d-block">
            <span>{{ __('Go to homepage') }}</span>
            <x-core::icon name="ti ti-arrow-right" />
          </a>
        </div>
      </div>
    </header>

    <div id="app">
      @yield('content')

    </div>
  </div>
</main>
