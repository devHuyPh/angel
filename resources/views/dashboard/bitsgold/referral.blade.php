@extends(EcommerceHelper::viewPath('customers.master'))

@php
    $url = request()->getSchemeAndHttpHost() . '/dang-ki?ref_code=' . $customer['uuid_code'];
    $encodedUrl = urlencode($url);
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$encodedUrl}&download=true";
    $siteTitle = theme_option('site_title') ?: setting('site_title') ?: 'Referral';
    $logo = theme_option('logo_vendor_dashboard', theme_option('logo'));
    $logoUrl = $logo ? RvMedia::getImageUrl($logo) : null;
@endphp

@once
    <style>
        .marketing-referral {
            --mr-primary: #4BA213;
            --mr-primary-rgb: 75, 162, 19;
            --mr-muted: #6b7280;
            --mr-border: rgba(0, 0, 0, 0.08);
            --mr-soft: rgba(var(--mr-primary-rgb), 0.08);
            --mr-header-h: 56px;

            color: #1c1b23;
        }

        .marketing-referral__header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            background: #fff;
            border-bottom: 1px solid var(--mr-border);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .marketing-referral__back {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid rgba(75, 162, 19, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--mr-primary);
            text-decoration: none;
        }

        .marketing-referral .icon-tabler {
            width: 1rem;
            height: 1rem;
        }

        .marketing-referral__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--mr-primary);
        }

        


        .marketing-referral__section {
            margin-bottom: 18px;
        }

        .marketing-card {
            background: #fff;
            border: 1px solid var(--mr-border);
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
        }

        .marketing-card__header {
            padding: 16px 16px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .marketing-card__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--mr-primary);
        }

        .marketing-card__badge {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--mr-primary);
            background: var(--mr-soft);
            border-radius: 999px;
            padding: 4px 10px;
        }

        .marketing-card__body {
            padding: 16px;
        }

        .btn-referral {
            background: var(--mr-primary);
            border-color: var(--mr-primary);
            color: #fff;
        }

        .btn-referral:hover,
        .btn-referral:focus {
            background: #3f9111;
            border-color: #3f9111;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(var(--mr-primary-rgb), 0.2);
        }

        .marketing-share {
            text-align: center;
        }

        .marketing-share__body {
            display: grid;
            gap: 12px;
            justify-items: center;
        }

        .marketing-share__hint {
            margin: 0 0 12px;
            color: var(--mr-muted);
            font-size: 0.9rem;
        }

        .marketing-share__brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            justify-content: center;
            width: 100%;
        }

        .marketing-share__logo {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            overflow: hidden;
            border: 1px solid var(--mr-border);
            background: #fff;
            display: grid;
            place-items: center;
        }

        .marketing-share__logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .marketing-share__name {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .marketing-share__qr {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            margin-bottom: 16px;
        }

        .marketing-share__qr img {
            width: 100%;
            max-width: 220px;
            height: auto;
        }

        .marketing-share__qr-logo {
            position: absolute;
            inset: 50% auto auto 50%;
            transform: translate(-50%, -50%);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            overflow: hidden;
            background: #fff;
            border: 2px solid #fff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .marketing-share__qr-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .marketing-share__actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .marketing-share__btn {
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .marketing-share__btn--ghost {
            background: #fff;
            border: 1px solid var(--mr-border);
            color: #1c1b23;
        }

        .marketing-share__btn--ghost:hover,
        .marketing-share__btn--ghost:focus {
            background: var(--mr-soft);
            color: #1c1b23;
        }

        .referral-alert {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--mr-soft);
            border: 1px solid rgba(var(--mr-primary-rgb), 0.25);
            color: #1f4d14;
            margin-top: 12px;
            border-radius: 10px;
            padding: 8px 12px;
        }

        .referral-alert .btn-close {
            margin-left: auto;
        }

        .referral-tree {
            display: grid;
            gap: 12px;
        }

        .referral-node {
            border: 1px solid var(--mr-border);
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
        }

        .referral-node--nested {
            background: #f9fbf7;
        }

        .referral-node__summary {
            list-style: none;
            cursor: pointer;
            padding: 14px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .referral-node__summary::-webkit-details-marker {
            display: none;
        }

        .referral-node__summary::marker {
            content: "";
        }

        .referral-node__summary--static {
            cursor: default;
        }

        .referral-node__content {
            flex: 1;
            min-width: 0;
        }

        .referral-node__chevron {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid rgba(var(--mr-primary-rgb), 0.2);
            color: var(--mr-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }

        .referral-node[open] .referral-node__chevron {
            transform: rotate(90deg);
        }

        .referral-card__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .referral-card__name {
            font-weight: 700;
            font-size: 1rem;
        }

        .referral-card__meta {
            color: var(--mr-muted);
            font-size: 0.85rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .referral-card__meta span + span::before {
            content: "|";
            margin: 0 6px;
            color: #c1c4cf;
        }

        .referral-card__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--mr-soft);
            color: var(--mr-primary);
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .referral-card__badge img {
            width: 22px;
            height: 22px;
            object-fit: contain;
        }

        .referral-card__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 8px 12px;
            margin-top: 12px;
        }

        .referral-card__item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .referral-card__label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: var(--mr-muted);
            font-weight: 600;
        }

        .referral-card__value {
            font-weight: 600;
        }

        .referral-node__count {
            margin-top: 10px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--mr-primary);
            background: var(--mr-soft);
            border-radius: 999px;
            padding: 4px 10px;
            display: inline-flex;
            align-items: center;
        }

        .referral-node__children {
            margin: 0 14px 14px;
            padding-left: 12px;
            border-left: 2px dashed rgba(var(--mr-primary-rgb), 0.3);
            display: grid;
            gap: 12px;
        }

        .referral-node.is-loading .referral-node__chevron {
            opacity: 0.6;
        }

        .referral-empty {
            padding: 14px;
            text-align: center;
            color: var(--mr-muted);
            background: var(--mr-soft);
            border-radius: 12px;
            border: 1px dashed rgba(var(--mr-primary-rgb), 0.25);
        }

        @media (min-width: 768px) {
            .marketing-referral__header {
                display: none;
            }

            .marketing-referral__body {
                padding-top: 0;
            }
        }

        @media (min-width: 992px) {
            .marketing-share {
                max-width: 560px;
                margin: 0 auto;
                text-align: center;
            }

            .marketing-share__hint {
                max-width: 360px;
                margin-left: auto;
                margin-right: auto;
            }

            .marketing-share__brand {
                margin-bottom: 8px;
            }

            .marketing-share__qr {
                margin-bottom: 18px;
            }

            .marketing-share__qr img {
                max-width: 260px;
            }
        }

        @media (max-width: 767.98px) {
            .marketing-card {
                border-radius: 12px;
            }

            .marketing-card__header {
                padding: 12px 12px 0;
            }

            .marketing-card__body {
                padding: 12px;
            }

            .marketing-share__qr img {
                max-width: 170px;
            }

            .marketing-share__qr-logo {
                width: 42px;
                height: 42px;
            }
        }
    </style>
@endonce

@section('content')
    <div class="marketing-referral">
        <div class="marketing-referral__header d-md-none">
            <a href="{{ route('setting') }}" class="marketing-referral__back" aria-label="{{ __('Back') }}">
                <x-core::icon name="ti ti-chevron-left" />
            </a>
            <h1 class="marketing-referral__title">@lang('plugins/marketplace::marketplace.referral')</h1>
        </div>

        <div class="marketing-referral__body">
            @include('notification_alert.active_account')

            <section class="marketing-referral__section">
                <div class="container px-0 px-md-3">
                    <div class="marketing-card marketing-share">
                        <div class="marketing-card__body marketing-share__body">
                            <p class="marketing-share__hint">
                                Hãy chia sẻ mã QR này để kết nối nhanh chóng và an toàn.
                            </p>

                            <div class="marketing-share__brand">
                                @if ($logoUrl)
                                    <span class="marketing-share__logo">
                                        <img src="{{ $logoUrl }}" alt="{{ $siteTitle }}" loading="lazy">
                                    </span>
                                @endif
                                <div class="marketing-share__name">{{ $siteTitle }}</div>
                            </div>

                            <div class="marketing-share__qr">
                                <img src="{{ $qrCodeUrl }}" alt="QR Code" loading="lazy">
                                @if ($logoUrl)
                                    <span class="marketing-share__qr-logo">
                                        <img src="{{ $logoUrl }}" alt="{{ $siteTitle }}" loading="lazy">
                                    </span>
                                @endif
                            </div>

                            <div class="marketing-share__actions">
                                <a href="{{ $qrCodeUrl }}" download class="btn btn-referral marketing-share__btn">
                                    <x-core::icon name="ti ti-download" />
                                    <span>Tải xuống</span>
                                </a>
                                <button type="button" class="btn marketing-share__btn marketing-share__btn--ghost"
                                    id="shareReferral"
                                    data-share-title="{{ $siteTitle }}"
                                    data-share-text="Hãy chia sẻ mã QR này để kết nối nhanh chóng và an toàn."
                                    data-share-url="{{ $url }}">
                                    <x-core::icon name="ti ti-share" />
                                    <span>Chia sẻ</span>
                                </button>
                            </div>

                            <div class="alert referral-alert d-none" id="shareAlert" role="status">
                                <x-core::icon name="ti ti-check" />
                                <span>{{ trans('core/base::base.copied') }}</span>
                                <button type="button" class="btn-close" aria-label="{{ __('Close') }}"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="marketing-referral__section">
                <div class="container px-0 px-md-3">
                    <div class="marketing-card">
                        <div class="marketing-card__header">
                            <h3 class="marketing-card__title">@lang('plugins/marketplace::marketplace.your-referral')</h3>
                            @if ($referrals->total() > 0)
                                <span class="marketing-card__badge">
                                    @lang('plugins/marketplace::marketplace.total_referrals'):
                                    {{ number_format($referrals->total()) }}
                                </span>
                            @endif
                        </div>
                        <div class="marketing-card__body">
                            @if ($referrals->count() > 0)
                                <div class="referral-tree">
                                    @foreach ($referrals as $referral)
                                        @php
                                            $children = $referral->referrers;
                                            $childCount = $children ? $children->count() : 0;
                                        @endphp

                                        <div class="referral-node referral-node--leaf">
                                            <div class="referral-node__summary referral-node__summary--static">
                                                <div class="referral-node__content">
                                                    <div class="referral-card__header">
                                                        <div>
                                                            <div class="referral-card__name">{{ $referral->name }}</div>
                                                            <div class="referral-card__meta">
                                                                @if ($referral->email)
                                                                    <span>{{ $referral->email }}</span>
                                                                @endif
                                                                @if ($referral->phone)
                                                                    <span>{{ $referral->phone }}</span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if (! empty($referral->rank))
                                                            <div class="referral-card__badge">
                                                                <img src="{{ asset($referral->rank->rank_icon) }}"
                                                                    alt="{{ $referral->rank->rank_name }}" loading="lazy">
                                                                <span>{{ $referral->rank->rank_name }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="referral-card__grid">
                                                        <div class="referral-card__item">
                                                            <span class="referral-card__label">@lang('plugins/marketplace::marketplace.reference')</span>
                                                            <span class="referral-card__value">{{ optional($referral->referrer)->name }}</span>
                                                        </div>
                                                        <div class="referral-card__item">
                                                            <span class="referral-card__label">@lang('core/base::layouts.total_dowline')</span>
                                                            <span class="referral-card__value">{{ format_price($referral->total_dowline) }}</span>
                                                        </div>
                                                        <div class="referral-card__item">
                                                            <span class="referral-card__label">@lang('plugins/marketplace::marketplace.datejoined')</span>
                                                            <span class="referral-card__value">{{ $referral->created_at }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="referral-node__count">
                                                        {{ number_format($childCount) }} @lang('core/base::layouts.referrals')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    {{ $referrals->links() }}
                                </div>
                            @else
                                <div class="referral-empty">
                                    @lang('plugins/marketplace::marketplace.nodata')
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('footer')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const shareButton = document.getElementById('shareReferral');
            const shareAlert = document.getElementById('shareAlert');
            const closeAlert = shareAlert ? shareAlert.querySelector('.btn-close') : null;

            function showShareAlert() {
                if (!shareAlert) return;
                shareAlert.classList.remove('d-none');
                shareAlert.classList.add('show');

                setTimeout(function() {
                    shareAlert.classList.remove('show');
                    setTimeout(function() {
                        shareAlert.classList.add('d-none');
                    }, 400);
                }, 2000);
            }

            function copyText(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(text);
                }

                const temp = document.createElement('textarea');
                temp.value = text;
                temp.style.position = 'fixed';
                temp.style.left = '-9999px';
                document.body.appendChild(temp);
                temp.select();
                temp.setSelectionRange(0, 99999);
                document.execCommand('copy');
                document.body.removeChild(temp);
                return Promise.resolve();
            }

            if (shareButton) {
                shareButton.addEventListener('click', function() {
                    const title = this.dataset.shareTitle || '';
                    const text = this.dataset.shareText || '';
                    const url = this.dataset.shareUrl || '';

                    if (navigator.share) {
                        navigator.share({
                            title,
                            text,
                            url
                        }).catch(function() {});
                        return;
                    }

                    copyText(url).then(showShareAlert).catch(showShareAlert);
                });
            }

            if (closeAlert) {
                closeAlert.addEventListener('click', function() {
                    shareAlert.classList.add('d-none');
                    shareAlert.classList.remove('show');
                });
            }

        });
    </script>
@endpush
