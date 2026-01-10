{!! SeoHelper::render() !!}

@include(MarketplaceHelper::viewPath('vendor-dashboard.layouts.header-meta'))

<link
    href="{{ asset('vendor/core/plugins/marketplace/fonts/linearicons/linearicons.css') }}?v={{ MarketplaceHelper::getAssetVersion() }}"
    rel="stylesheet"
>
<link
    href="{{ asset('vendor/core/plugins/marketplace/css/marketplace.css') }}?v={{ MarketplaceHelper::getAssetVersion() }}"
    rel="stylesheet"
>


@if (session('locale_direction', 'ltr') == 'rtl')
    <link href="{{ asset('vendor/core/core/base/css/core.rtl.css') }}" rel="stylesheet">

    <link
        href="{{ asset('vendor/core/plugins/marketplace/css/marketplace-rtl.css') }}?v={{ MarketplaceHelper::getAssetVersion() }}"
        rel="stylesheet"
    >
@endif


@if (File::exists($styleIntegration = Theme::getStyleIntegrationPath()))
    {!! Html::style(Theme::asset()->url('css/style.integration.css?v=' . filectime($styleIntegration))) !!}
@endif
<style>
.jungle {
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.jungle:hover {
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}
.card.bg-primary {
    background: linear-gradient(252deg, #4ea317, #62d717);
}
.card-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 1rem;
}

.card-text {
    font-family: 'Poppins', sans-serif;
    font-size: 1.2rem;
    font-weight: bold;
}
.card-body i {
    font-size: 2rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 12px;
    border-radius: 50%;
}
.jungle:hover {
    border: 2px solid rgba(255, 255, 255, 0.5);
}
@keyframes sparkle {
    0% { filter: brightness(1); transform: scale(1.2); }
    50% { filter: brightness(1.5); transform: scale(1.4); }
    100% { filter: brightness(1); transform: scale(1.6); }
}

@keyframes glowing {
    0% { box-shadow: 0 0 5px #6bdf13, 0 0 10px #6bdf13; }
    50% { box-shadow: 0 0 10px #6bdf13, 0 0 20px #6bdf13; }
    100% { box-shadow: 0 0 5px #6bdf13, 0 0 10px #6bdf13; }
}

.rank-icon {
    animation: sparkle 6s infinite alternate ease-in-out;
}

.badge-glow {
    animation: glowing 1.5s infinite alternate ease-in-out;
    font-weight: bold;
    letter-spacing: 0.5px;
    padding: 8px 14px;
    min-width: 90px; /* Để đảm bảo badge không quá nhỏ */
    text-align: center;
}
@media screen and (min-width: 768px) {
  .ps-drawer--mobile {
    width: 50%;
  }
}

   .ps-drawer__content::-webkit-scrollbar {
        width: 8px;
    }
    .ps-drawer__content::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .ps-drawer__content::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    .ps-drawer__content::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

</style>
