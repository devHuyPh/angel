@php
    $hasLogoLight ??= false;
    $defaultIsDark ??= true;

    $logo = theme_option('logo');
    $logoLight = theme_option('logo_light');

    $height = theme_option('logo_height', 35);
    $attributes = [
        'style' => sprintf('height: %s', is_numeric($height) ? "{$height}px" : $height),
        'loading' => 'eager',
    ];
@endphp

@if ($logo || $logoLight)
    <div class="logo">
        <a href="{{ BaseHelper::getHomepageUrl() }}">
            @if ($hasLogoLight)
                {{ RvMedia::image($logoLight ?: $logo, theme_option('site_title'), attributes: ['class' => 'logo-light', ...$attributes], lazy: false) }}
                {{ RvMedia::image($logo ?: $logoLight, theme_option('site_title'), attributes: ['class' => 'logo-dark', ...$attributes], lazy: false) }}
            @else
                {{ RvMedia::image($defaultIsDark ? $logo : $logoLight, theme_option('site_title'), attributes: $attributes, lazy: false) }}
            @endif
        </a>
    </div>
@endif
