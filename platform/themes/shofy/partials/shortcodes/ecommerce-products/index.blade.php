@php
    $style = in_array($shortcode->style, ['grid', 'slider', 'simple', 'slider-full-width'])
        ? $shortcode->style
        : 'grid';
@endphp
<div class="desktop">
    {!! Theme::partial(
        "shortcodes.ecommerce-products.$style",
        compact('shortcode', 'products', 'ads', 'categoryIds', 'categories'),
    ) !!}
</div>

@if ($style == 'grid')
    @include('front.includes.grid-products')
@endif
