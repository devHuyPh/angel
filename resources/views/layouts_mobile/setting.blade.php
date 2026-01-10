@php
    Theme::set('breadcrumbStyle', 'none');
    Theme::layout('full-width');

    Theme::set(
        'add_head',
        '
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
        ',
    );

    $menuItems = DashboardMenu::getAll('customer');
    $menuItems = $menuItems instanceof \Illuminate\Support\Collection ? $menuItems : collect($menuItems);

    $bitsgoldId = 'cms-customer-bitsgold';
    $ordersItem = $menuItems->firstWhere('id', 'cms-customer-orders');
    $editAccountItem = $menuItems->firstWhere('id', 'cms-customer-edit-account');
    $quickAction = $menuItems->firstWhere('id', 'cms-customer-overview') ?? $ordersItem ?? $editAccountItem;
    $addressItem = $menuItems->firstWhere('id', 'cms-customer-address');
    $downloadsItem = $menuItems->firstWhere('id', 'cms-customer-downloads');
    $reviewsItem = $menuItems->firstWhere('id', 'cms-customer-product-reviews');

    $addressUrl = $addressItem['url'] ?? ($ordersItem['url'] ?? '#');
    $downloadsUrl = $downloadsItem['url'] ?? ($ordersItem['url'] ?? '#');
    $reviewsUrl = $reviewsItem['url'] ?? ($ordersItem['url'] ?? '#');

    $childSections = $menuItems->filter(function ($item) {
        return !empty($item['children'])
            && $item['children'] instanceof \Illuminate\Support\Collection
            && $item['children']->isNotEmpty();
    });

    $accountItems = $menuItems->filter(function ($item) use ($bitsgoldId) {
        $children = $item['children'] ?? null;
        $hasChildren = $children instanceof \Illuminate\Support\Collection && $children->isNotEmpty();

        return ! $hasChildren && ($item['id'] ?? '') !== $bitsgoldId;
    });

    $childItems = $childSections->flatMap(fn ($section) => $section['children']);

    $excludeIds = collect([
        $quickAction['id'] ?? null,
        $ordersItem ? 'cms-customer-orders' : null,
        $addressItem ? 'cms-customer-address' : null,
        $downloadsItem ? 'cms-customer-downloads' : null,
        $reviewsItem ? 'cms-customer-product-reviews' : null,
    ])->filter();

    $combinedItems = $childItems->merge($accountItems);

    if ($excludeIds->isNotEmpty()) {
        $combinedItems = $combinedItems->reject(fn ($item) => $excludeIds->contains($item['id'] ?? null));
    }

    $combinedItems = $combinedItems->unique('id')->values();

    $logoutItem = $combinedItems->firstWhere('id', 'cms-customer-logout');
    if ($logoutItem) {
        $combinedItems = $combinedItems->reject(fn ($item) => ($item['id'] ?? null) === 'cms-customer-logout');
    }

    $combinedItems = $combinedItems
        ->sortBy(fn ($item) => (int) ($item['priority'] ?? 1000))
        ->values();

    if ($logoutItem) {
        $combinedItems = $combinedItems->push($logoutItem);
    }

    $isMember = (int) ($customer->is_active_account ?? 0) === 1;
    $customerMeta = $customer->phone ?: $customer->email;
    $ranking = $customer->rank;
    $rankIconUrl = $ranking && $ranking->rank_icon ? asset($ranking->rank_icon) : null;
    $rankName = optional($ranking)->rank_name;
@endphp
<style>
    #header-sticky,
    .tp-subscribe-area,
    footer {
        display: none !important;
    }

    body {
        background: rgba(var(--primary-color-rgb), 0.06) !important;
        font-family: var(--primary-font) !important;
        color: #1c1b23 !important;
    }

    .account-screen {
        min-height: 100vh;
        background: rgba(var(--primary-color-rgb), 0.06);
    }

    .account-hero {
        background:
            linear-gradient(145deg, rgba(255, 255, 255, 0.18), rgba(0, 0, 0, 0.18)),
            var(--primary-color);
        color: #fff;
        padding: 20px 18px 26px;
        border-bottom-left-radius: 24px;
        border-bottom-right-radius: 24px;
        position: relative;
        overflow: hidden;
    }

    .account-hero::after {
        content: '';
        position: absolute;
        right: -120px;
        top: -120px;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0));
        pointer-events: none;
    }

    .hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .hero-back,
    .hero-action {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.4);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.12);
    }

    .hero-back:hover,
    .hero-action:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.18);
    }

    .hero-title {
        flex: 1;
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-align: center;
    }

    .hero-profile {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .hero-avatar {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        overflow: hidden;
        border: 2px solid rgba(255, 255, 255, 0.7);
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.2);
    }

    .hero-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .hero-info {
        flex: 1;
    }

    .hero-rank {
        margin-left: auto;
        min-width: 56px;
        min-height: 56px;
        border-radius: 14px;
        padding: 6px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.35);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        flex-shrink: 0;
        text-align: center;
    }

    .hero-rank img {
        width: 32px;
        height: 32px;
        object-fit: contain;
        display: block;
    }

    .hero-rank-name {
        font-size: 0.62rem;
        line-height: 1;
        font-weight: 600;
        color: #fff;
        opacity: 0.9;
    }

    .hero-name {
        font-weight: 600;
        font-size: 1rem;
    }

    .hero-meta {
        font-size: 0.85rem;
        opacity: 0.8;
        margin-top: 2px;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        margin-top: 8px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.2px;
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .hero-badge.is-member {
        background: rgba(34, 195, 166, 0.25);
        border-color: rgba(34, 195, 166, 0.6);
    }

    .hero-score {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.9);
        color: var(--primary-color);
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: 0 8px 18px rgba(var(--primary-color-rgb), 0.35);
    }

    .hero-score i {
        color: #f5c542;
    }

    .account-content {
        display: grid;
        gap: 12px;
        padding: 0 16px calc(56px + env(safe-area-inset-bottom));
        margin-top: 12px;
    }

    .account-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
        padding: 14px;
    }

    .account-card--quick {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: inherit;
    }

    .account-card--quick:hover {
        color: inherit;
        box-shadow: 0 18px 46px rgba(var(--primary-color-rgb), 0.18);
    }

    .card-arrow {
        margin-left: auto;
        color: #8c90a6;
        font-size: 0.9rem;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .card-title {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .card-link {
        font-size: 0.8rem;
        text-decoration: none;
        color: var(--primary-color);
        font-weight: 600;
    }

    .status-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }

    .status-item {
        display: grid;
        gap: 6px;
        text-decoration: none;
        color: #8c90a6;
        font-size: 0.75rem;
        text-align: center;
    }

    .status-item i {
        font-size: 1.1rem;
        color: var(--primary-color);
    }

    .card-list {
        display: grid;
        gap: 10px;
    }

    .list-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 6px;
        text-decoration: none;
        color: inherit;
        border-radius: 12px;
        transition: background 0.2s ease;
    }

    .list-item:hover {
        background: rgba(var(--primary-color-rgb), 0.08);
        color: inherit;
    }

    .list-item--logout {
        color: #d6336c;
        border: 1px solid rgba(214, 51, 108, 0.45);
    }

    .item-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        background: rgba(var(--primary-color-rgb), 0.12);
        display: grid;
        place-items: center;
        color: var(--primary-color);
        flex-shrink: 0;
    }

    .item-icon--accent {
        background: rgba(34, 195, 166, 0.15);
        color: #1a9c82;
    }

    .item-icon :is(i, svg) {
        width: 18px;
        height: 18px;
        font-size: 1rem;
    }

    .item-text {
        flex: 1;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .list-item i {
        color: #8c90a6;
        font-size: 0.9rem;
    }

    @media (max-width: 480px) {
        .account-card {
            padding: 12px;
        }

        .hero-score {
            font-size: 0.78rem;
        }
    }
</style>

<div class="account-screen">
    <header class="account-hero">
        <div class="hero-top">
            <a href="{{ route('public.index') }}" class="hero-back" aria-label="{{ trans('core/base::layouts.back') }}">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div class="hero-title">{{ trans('core/base::layouts.account') }}</div>
            @if ($editAccountItem)
                <a href="{{ $editAccountItem['url'] }}" class="hero-action" aria-label="{{ $editAccountItem['name'] ?? trans('core/base::layouts.edit') }}">
                    <i class="bi bi-gear"></i>
                </a>
            @else
                <span class="hero-action" aria-hidden="true">
                    <i class="bi bi-gear"></i>
                </span>
            @endif
        </div>

        <div class="hero-profile">
            <div class="hero-avatar">
                <img src="{{ $customer->avatar_url }}" alt="{{ $customer->name }}" loading="lazy">
            </div>
            <div class="hero-info">
                <div class="hero-name">{{ $customer->name }}</div>
                @if ($customerMeta)
                    <div class="hero-meta">{{ $customerMeta }}</div>
                @endif
            </div>
            @if ($rankIconUrl)
                <div class="hero-rank" title="{{ $rankName }}">
                    <img src="{{ $rankIconUrl }}" alt="{{ $rankName }}" loading="lazy">
                    @if ($rankName)
                        <span class="hero-rank-name">{{ $rankName }}</span>
                    @endif
                </div>
            @endif
        </div>
    </header>

    <main class="account-content">
        @if ($quickAction)
            <a href="{{ $quickAction['url'] }}" class="account-card account-card--quick">
                <span class="item-icon item-icon--accent">
                    <x-core::icon :name="$quickAction['icon']" />
                </span>
                <span class="item-text">{{ $quickAction['name'] }}</span>
                <i class="bi bi-chevron-right card-arrow"></i>
            </a>
        @endif

        @if ($ordersItem)
            <section class="account-card account-card--orders">
                <div class="card-header">
                    <div class="card-title">{{ $ordersItem['name'] }}</div>
                    <a href="{{ $ordersItem['url'] }}" class="card-link">{{ trans('plugins/ecommerce::order.view_all') }}</a>
                </div>
                <div class="status-grid">
                    <a href="{{ $ordersItem['url'] }}" class="status-item">
                        <i class="bi bi-credit-card"></i>
                        <span>{{ trans('plugins/ecommerce::order.pending_payment') }}</span>
                    </a>
                    <a href="{{ $addressUrl }}" class="status-item">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ $addressItem['name'] ?? trans('plugins/ecommerce::addresses.addresses') }}</span>
                    </a>
                    <a href="{{ $downloadsUrl }}" class="status-item">
                        <i class="bi bi-download"></i>
                        <span>{{ $downloadsItem['name'] ?? __('Downloads') }}</span>
                    </a>
                    <a href="{{ $reviewsUrl }}" class="status-item">
                        <i class="bi bi-star"></i>
                        <span>{{ $reviewsItem['name'] ?? trans('plugins/ecommerce::review.name') }}</span>
                    </a>
                </div>
            </section>
        @endif

        @if ($combinedItems->isNotEmpty())
            <section class="account-card">
                <div class="card-header">
                    <div class="card-title">{{ trans('plugins/marketplace::marketplace.my_account') }}</div>
                </div>
                <div class="card-list">
                    @foreach ($combinedItems as $item)
                        <a href="{{ $item['url'] }}"
                            class="list-item {{ ($item['id'] ?? '') === 'cms-customer-logout' ? 'list-item--logout' : '' }}">
                            <span class="item-icon">
                                <x-core::icon :name="$item['icon']" />
                            </span>
                            <span class="item-text">{{ $item['name'] }}</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>
</div>
