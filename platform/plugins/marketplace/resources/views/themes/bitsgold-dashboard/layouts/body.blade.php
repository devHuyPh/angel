@php
  $customer = auth('customer')->user();
  if (!empty($customer->rank_id)) {
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
    <a class="ps-logo" href="{{ url('/') }}">
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
<header class="header--mobile" style="background: #228822">
    <div class="header__left">
        <button class="ps-drawer-toggle">
            <x-core::icon name="ti ti-menu-2" />
        </button>
    </div>
    <div class="header__center">
        <a class="ps-logo" href="{{ url('/') }}">
            @php $logo = theme_option('logo_vendor_dashboard', theme_option('logo')); @endphp
            @if ($logo)
                <img src="{{ RvMedia::getImageUrl($logo) }}" alt="{{ theme_option('site_title') }}">
            @endif
        </a>
    </div>
    <div class="header__right">
        <a class="header__site-link text-black" href="https://mocthienan.vn/">
            <x-core::icon name="ti ti-logout" />
        </a>
    </div>
</header>

<!--mobile-->
<aside class="ps-drawer--mobile">
    <div class="ps-drawer__header p-0">
        <h4 class="fs-3 mb-0 w-100">
            <div class="card shadow-sm border-0">
                <!-- User Profile Section - Optimized for Mobile -->
                <div class="card-header bg-primary bg-gradient text-white" style="justify-content: center;">
                    <div class="d-flex flex-column flex-md-row align-items-center">
                        <div class="mb-3 mb-md-0 text-center text-md-start">
                            <img src="{{ empty($customer->avatar) ? asset('storage/main/customers/6.jpg') : asset('storage/' . $customer->avatar) }}" class="rounded-circle" style="width: 70px; height: 70px">
                        </div>
                        <div class="ms-md-3 text-center text-md-start">
                            <h5 class="mb-0">{{ __('Hello') }}, {{ $customer->name }}</h5>
                            <small class="text-white-50">{{ __('Joined on :date', ['date' => $customer->created_at->translatedFormat('M d, Y')]) }}</small>
                        </div>
                    </div>
                </div>

                <!-- Rank Section - Optimized for Mobile -->
                <div class="card-body border-bottom">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-2 mb-md-0 @empty($ranking) d-none @endempty">
                            <img src="{{ asset($ranking->rank_icon ?? '') }}" width="40" alt="{{ $ranking->rank_name ?? '' }}" class="me-2 rank-icon">
                            <div>
                                <h4 class="mb-0">{{ $ranking->rank_name ?? '' }}</h4>
                            </div>
                        </div>
                        <span class="badge border-success text-black border-1 rounded-pill shadow-lg position-relative d-inline-block" style="background: #6bdf13 linear-gradient(263deg, #dbdab8, #0a583c00);">
                            {{ __('Active') }}
                        </span>
                    </div>

                    <!-- KYC Status Section -->
                    @if ($customer->kyc_status==0)
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mt-3">
                            <!-- KYC Status -->
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <x-core::icon name="ti ti-shield-x" class="me-2 text-danger" style="font-size: 40px;" />
                                <div>
                                   <a href="{{route('kyc.index')}}" ><h4 class="mb-0 text-danger">{{ trans('core/base::layouts.kyc_status_dash') }}</h4></a>
                                </div>
                            </div>

                            <!-- Badge "Chưa xác minh" -->
                            <span class="badge border-danger text-black border-1 rounded-pill shadow-lg position-relative d-inline-block" style="background: #ff4d4f linear-gradient(263deg, #ff9999, #ff0000);">
<a href="{{route('kyc.index')}}">{{ trans('core/base::layouts.not_verified_kyc') }}</a>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </h4>
    </div>
    <div class="ps-drawer__content overflow-y-auto p-0" style="max-height: 60vh; overflow-x: hidden; -webkit-overflow-scrolling: touch; scrollbar-width: auto; scrollbar-color: #888 #f1f1f1; height: calc(125vh - 300px);">
            <div style="width: 100%; min-height: 100%;">
                @include(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.menu'))
            </div>
        </div>
</aside>
<div class="ps-site-overlay"></div>
<!--desktop-->
<main class="ps-main">
    <div class="ps-main__sidebar pt-0 pb-0">
        <div class="ps-sidebar pb-0">
            <!-- Bootstrap Responsive Sidebar with Mobile Optimizations -->
            <div class="card shadow-sm border-0">
                <!-- User Profile Section - Optimized for Mobile -->
                <div class="card-header bg-primary bg-gradient text-white">
                    <div class="d-flex flex-column flex-md-row align-items-center">
                        <div class="mb-3 mb-md-0 text-center text-md-start">
                            <img src="{{ empty($customer->avatar) ? asset('storage/main/customers/6.jpg') : asset('storage/' . $customer->avatar) }}" class="rounded-circle" style="width: 70px; height: 70px">
                        </div>
                        <div class="ms-md-3 text-center text-md-start">
                            <h5 class="mb-0">{{ __('Hello') }}, {{ $customer->name }} </h5>
                            <small class="text-white-50">{{ __('Joined on :date', ['date' => $customer->created_at->translatedFormat('M d, Y')]) }}</small>
                        </div>
                    </div>
                </div>

                <!-- Rank Section - Optimized for Mobile -->
                <div class="card-body border-bottom">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <!-- Rank -->
                        <div class="d-flex align-items-center">
                            <img src="{{ asset($ranking->rank_icon ?? '') }}" width="40" alt="{{ $ranking->rank_name ?? '' }}" class="me-2 rank-icon">
                            <h4 class="mb-0 ">{{ $ranking->rank_name ?? '' }}</h4>
                        </div>

                        <!-- Badge "Hoạt động" -->
                        <span class="badge border-success text-black border-1 rounded-pill shadow-lg badge-glow" style="background: #6bdf13 linear-gradient(263deg, #dbdab8, #0a583c00);">
                            {{ __('Active') }}
                        </span>
                    </div>

                    <!-- KYC Status Section -->
                    @if ($customer->kyc_status==0)
                        <div class="d-flex align-items-center justify-content-between w-100 mt-3">
                            <!-- KYC Status -->
                            <div class="d-flex align-items-center">
                                <x-core::icon name="ti ti-shield-x" class="me-2 text-danger" style="font-size: 40px;" />
                                <a href="{{route('kyc.index')}}"><h4 class="mb-0 text-danger">{{ trans('core/base::layouts.kyc_status_dash') }}</h4></a>
                            </div>

                            <!-- Badge "Chưa xác minh" -->
                            <span class="badge border-danger text-black border-1 rounded-pill shadow-lg badge-glow" style="background: #ff4d4f linear-gradient(263deg, #ff9999, #ff0000);">
                               <a href="{{route('kyc.index')}}"> {{ trans('core/base::layouts.not_verified_kyc') }}</a>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="ps-sidebar__content">
                <div class="ps-sidebar__center">
                    @include(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.menu'))
                </div>
            </div>
        </div>
    </div>
    <div class="ps-main__wrapper" id="bitsgold-dashboard">
        <header class="d-none d-md-block mb-4 py-3 border-bottom">
            <div class="container">
                <div class="row align-items-center flex-column flex-md-row">
                    <!-- Left Side: Title -->
                    <div class="col-12 col-md-4 mb-3 mb-md-0 text-center text-md-start">
                        <h3 class="fs-2 fw-bold text-uppercase mb-0" style="font-family: 'Inter';">
                            @lang('plugins/marketplace::marketplace.'.ucfirst(Request::segment(2)))
                        </h3>
                    </div>

                    
                    <!-- Right Side: Language Switcher and Homepage Link -->
                    <div class="col-12 col-md-8">
                        <div class="d-flex justify-content-center justify-content-md-end align-items-center flex-wrap gap-3">
                            <!-- Language Switcher -->
                            @if (is_plugin_active('language'))
                                <div class="d-flex align-items-center">
                                    {!! apply_filters(
                                        'marketplace_vendor_dashboard_language_switcher',
                                        view(MarketplaceHelper::viewPath('bitsgold-dashboard.partials.language-switcher'))->render(),
                                    ) !!}
                                </div>
                            @endif

                            <!-- Homepage Link -->
                            <div>
                                <a href="{{ BaseHelper::getHomepageUrl() }}" target="_blank" class="btn btn-outline-primary text-uppercase d-flex align-items-center gap-2">
                                    <x-core::icon name="ti ti-home" /> 
                                    <span class="d-none d-md-inline">@lang('plugins/marketplace::marketplace.home')</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- notification --}}
                    @if(!$customer->is_active_account)
                    <div class="col-12 mt-3 mb-md-0 text-center text-md-start">
                        <h3 class="fs-2 fw-bold text-uppercase mb-0" style="font-family: 'Inter';">
                            <div class="mt-2">
                                <div role="alert" class="alert alert-danger">
                                    <div class="d-flex">
                                        <div>
                                            <svg class="icon alert-icon svg-icon-ti-ti-info-circle" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                                <path d="M12 9h.01"></path>
                                                <path d="M11 12h1v4h1"></path>
                                            </svg>
                                        </div>
                                        <div class="w-100">
                                            <h4 class="alert-title mb-0">
                                                {{trans('core/base::layouts.account-activation-instruction')}} <a href="/products">Mua ngay</a>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </h3>
                    </div>
                    @endif
                </div>
            </div>
        </header>
        <div id="app">
            @yield('content')
        </div>
    </div>
</main>
