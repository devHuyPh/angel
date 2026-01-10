@if (theme_option('enabled_bottom_menu_bar_on_mobile', true))
<style>
/* Mobile Bottom Nav - Center Raised */
.tp-mobile-menu {
    position: fixed !important;
    bottom: 0 !important;
    left: 0;
    right: 0;
    background: #fff;
    padding: 10px 0 8px;
    padding-bottom: calc(8px + env(safe-area-inset-bottom, 0px));
    z-index: 999;
    border-top: 1px solid #e5e7eb;
}

.tp-mobile-menu .row {
    align-items: flex-end;
}

/* Center item - raised */
.tp-mobile-item-center {
    position: relative;
    margin-top: -20px;
}

.tp-mobile-item-center .tp-mobile-item-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: var(--tp-theme-primary);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    margin: 0 auto;
    border: 3px solid #fff;
}

.tp-mobile-item-center .tp-mobile-item-btn svg {
    width: 22px;
    height: 22px;
    stroke-width: 1.5;
}

.tp-mobile-item-center .tp-mobile-item-btn .cart-count-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    min-width: 18px;
    height: 18px;
    line-height: 18px;
    font-size: 10px;
    background: #ef4444;
    color: #fff;
    border-radius: 50%;
    text-align: center;
}

.tp-mobile-item-center .tp-mobile-item-label {
    display: block;
    text-align: center;
    font-size: 11px;
    margin-top: 4px;
    color: #374151;
    position: absolute;
    bottom: -18px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
}

/* Regular items */
.tp-mobile-item:not(.tp-mobile-item-center) .tp-mobile-item-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #6b7280;
}

.tp-mobile-item:not(.tp-mobile-item-center) .tp-mobile-item-btn svg {
    width: 24px;
    height: 24px;
    stroke-width: 1.5;
    margin-bottom: 2px;
}

.tp-mobile-item:not(.tp-mobile-item-center) .tp-mobile-item-btn span {
    font-size: 11px;
    line-height: 1.2;
}

.tp-mobile-item:not(.tp-mobile-item-center) .tp-mobile-item-btn:hover {
    color: var(--tp-theme-primary, #8b5cf6);
}
</style>
    <div id="tp-bottom-menu-sticky" class="tp-mobile-menu d-lg-none">
        <div class="container">
            <div class="row row-cols-5">
                {{-- 1. Home --}}
                <div class="col">
                    <div class="text-center tp-mobile-item">
                        <a href="{{ route('public.index') }}" class="tp-mobile-item-btn">
                            <x-core::icon name="ti ti-home" />
                            <span>{{ __('Trang chủ') }}</span>
                        </a>
                    </div>
                </div>

                {{-- 2. Search (đổi chỗ với Store) --}}
                <div class="col">
                    <div class="text-center tp-mobile-item">
                        <button class="tp-mobile-item-btn tp-search-open-btn">
                            <x-core::icon name="ti ti-search" />
                            <span>{{ __('Tìm kiếm') }}</span>
                        </button>
                    </div>
                </div>

                {{-- 3. Cart - Center Raised --}}
                @if (is_plugin_active('ecommerce'))
                <div class="col">
                    <div class="text-center tp-mobile-item tp-mobile-item-center">
                        <a href="{{ route('public.cart') }}" class="tp-mobile-item-btn position-relative">
                            <x-core::icon name="ti ti-shopping-cart" />
                            <span class="cart-count-badge cart-count">{{ Cart::instance('cart')->count() }}</span>
                        </a>
                        <!-- <span class="tp-mobile-item-label">{{ __('Giỏ hàng') }}</span> -->
                    </div>
                </div>
                @endif

                {{-- 4. Wishlist --}}
                @if (is_plugin_active('ecommerce') && EcommerceHelper::isWishlistEnabled())
                <div class="col">
                    <div class="text-center tp-mobile-item">
                        <a href="{{ route('public.wishlist') }}" class="tp-mobile-item-btn">
                            <x-core::icon name="ti ti-heart" />
                            <span>{{ __('Yêu thích') }}</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- 5. Account --}}
                @if (is_plugin_active('ecommerce'))
                <div class="col">
                    <div class="text-center tp-mobile-item">
                        <a href="{{ auth('customer')->check() ? route('setting') : route('customer.login') }}"
                            class="tp-mobile-item-btn">
                            <x-core::icon name="ti ti-user" />
                            <span>{{ __('Tài khoản') }}</span>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endif
