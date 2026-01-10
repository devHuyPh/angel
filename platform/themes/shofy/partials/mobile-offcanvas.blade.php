<div class="offcanvas__area offcanvas__radius">
    <div class="offcanvas__wrapper" style="background: #228822; !important">
        <div class="offcanvas__close">
            <button class="offcanvas__close-btn offcanvas-close-btn" title="Search">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M1 1L11 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <div class="offcanvas__content">
            <div class="offcanvas__top mb-70 d-flex justify-content-between align-items-center">
                <div class="offcanvas__logo logo" style="max-width: 75%;">
                    {!! Theme::partial('header.logo') !!}
                </div>
            </div>

            @auth('customer')
                <div class="row">
                    <div class="pb-40 offcanvas__category text-white col-md-3">
                        <button class="btn btn-primary" style="background: #2C9634;color: white;">
                            {{-- <x-core::icon name="ti ti-menu-2" /> --}}
                            <a href="{{route('bitsgold.dashboard')}}">{{ __('Dashboard') }}
                                <br>{{ __('Balance') }}: {{format_price(auth('customer')->user()->walet_1)}}
                            </a>
                        </button>
                        {{-- <div class="tp-category-mobile-menu text-white"></div> --}}
                    </div>
                </div>
            @endauth

                    {{--  @if (is_plugin_active('ecommerce') && theme_option('enabled_header_categories_dropdown_on_mobile', 'yes') === 'yes')
                <div class="pb-40 offcanvas__category text-white">
                    <button class="tp-offcanvas-category-toggle" style="background: #2C9634;color: white;">
                        <x-core::icon name="ti ti-menu-2" />
                        {{ __('All Categories') }}
                    </button>
                    <div class="tp-category-mobile-menu text-white"></div>
                </div>
            @endif --}}

            <div class="mb-40 tp-main-menu-mobile fix d-xl-none"></div>

            @if ($hotline = theme_option('hotline'))
                <div class="offcanvas__btn p-0">
                    <a href="tel:{{ $hotline }}" class="tp-btn-2 text-light"  style="width:100%;background: #2C9634;display: inline-block;">
                       <svg class="icon svg-icon-ti-ti-phone-call" style="margin-right: 14px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                          <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>
                          <path d="M15 7a2 2 0 0 1 2 2"></path>
                          <path d="M15 3a6 6 0 0 1 6 6"></path>
                        </svg> 
                        {{ __('Contact Us') }}
                    </a>
                </div>
            @endif
            @guest('customer')
                <div class="offcanvas__btn p-0 mt-3">
                    <a href="{{ route('customer.login') }}" class="tp-btn-2 tp-btn-border-2 text-light ms-10" style="width:100%;background: #2C9634;display: inline-block;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user me-3"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
                            </svg>
                            {{ __('Login') }}
                    </a>
                </div>
            @endguest
            @auth('customer')
                <div class="offcanvas__btn p-0 mt-3">
                    <a href="{{ route('customer.logout') }}" class="tp-btn-2 tp-btn-border-2 text-light ms-10"
                        style="width:100%;background: #2C9634;display: inline-block;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon svg-icon-ti-ti-logout me-3">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                        </svg>
                        </svg>
                        {{ __('Logout') }}
                    </a>
                </div>
            @endauth
        </div>
        <div class="offcanvas__bottom">
            <div class="offcanvas__footer d-flex align-items-center justify-content-between">
                @if (is_plugin_active('ecommerce') && ($currencies = get_all_currencies()) && $currencies->count() > 1)
                    <div class="offcanvas__currency-wrapper currency">
                        <span class="offcanvas__currency-selected-currency tp-currency-toggle text-light" id="tp-offcanvas-currency-toggle">
                            {{ __('Currency: :currency', ['currency' => get_application_currency()->title]) }}
                        </span>
                        {!! Theme::partial('currency-switcher', ['class' => 'offcanvas__currency-list tp-currency-list']) !!}
                    </div>
                @endif

                {!! Theme::partial('language-switcher', ['type' => 'mobile']) !!}
            </div>
        </div>
    </div>
</div>