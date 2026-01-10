
<div @class([
    'tp-product-item-5 p-relative white-bg mb-40 product-card',
    $class ?? null,
]) style="
    width: 100% !important;
">
    {{-- haiiii --}}
    <div class="tp-product-thumb-5 w-img fix mb-15">
        <a href="{{ $product->url }}">
            {{-- {{ RvMedia::image($product->image, $product->name, $style === 2 ? 'thumb' : 'medium', true) }} --}}
            {{ RvMedia::image($product->image, $product->name, $style === 2 ? 'thumb' : 'medium', true, ['class' => 'product-image']) }}
        </a>

        @include(Theme::getThemeNamespace('views.ecommerce.includes.product.badges'))

        @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-5.actions'))
    </div>
    <div class="tp-product-content-5">
        {!! apply_filters('ecommerce_before_product_item_content_renderer', null, $product) !!}

        <div class="product-info">
            <!-- @if (is_plugin_active('marketplace') && $product->store->getKey())
                <div class="tp-product-tag-5">
                    <span><a href="{{ $product->store->url }}">{{ $product->store->name }}</a></span>
                </div>
            @endif -->
            <h3 class="tp-product-title-2">
                <a href="{{ $product->url }}" class="product-title" title="{{ $product->name }}">{{ $product->name }}</a>
            </h3>

            <div @class([
                'mt-2 tp-product-price-review' =>
                    theme_option('product_listing_review_style', 'default') !== 'default' &&
                    EcommerceHelper::isReviewEnabled() &&
                    ($product->reviews_avg ||
                        theme_option('ecommerce_hide_rating_star_when_is_zero', 'no') ===
                            'no'),
            ])>
                @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-5.rating'))

                @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-5.price'))
            </div>
            @php
                $rawStateName = $product->store->states->name ?? 'Hà Nội';
                $stateName = Str::replace(['TP. ', 'Tỉnh ', 'Thành phố '], '', $rawStateName);
            @endphp
            <div class="product-meta">
                <div class="sold-count">
                    <x-core::icon name="ti ti-shopping-cart-dollar" class="icon-ft-item" />
                    {{-- <span>Đã bán {{ $product->sold_count }}</span> --}}
                    <span>Đã bán 
                        {{ rand(900, 1000) }}
                    </span>
                </div>
                <div class="location">
                    <x-core::icon name="ti ti-map-pin" class="icon-ft-item" />
                    <span>
                        {{-- {{ $stateName }} --}}
                        Toàn quốc
                    </span>
                </div>
            </div>

        </div>
        {!! apply_filters('ecommerce_after_product_item_content_renderer', null, $product) !!}
    </div>
</div>
