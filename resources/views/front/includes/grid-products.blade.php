<div class="mobile">
    <div class="container-fluid p-0">
        <h2 class="section-title">{{ $shortcode->title }}</h2>

        <!-- First Row - Scrollable -->
        @php
            $productCount = $products->count();

            if ($productCount <= 2) {
                $chunks = collect([$products]);
            } else {
                $chunks = $products->chunk(ceil($productCount / 2));
            }
        @endphp
        <div class="horizontal-scroll-container">
            @foreach ($chunks as $chunk)
                <div class="product-row">
                    @foreach ($chunk as $product)
                        {{-- @dd($product) --}}
                        @php
                            $rawStateName = $product->store->states->name ?? 'Hà Nội';
                            $stateName = Str::replace(['TP. ', 'Tỉnh ', 'Thành phố '], '', $rawStateName);
                        @endphp
                        <div class="product-card">
                            {{-- <div class="discount-badge">40%</div> --}}
                            @include(Theme::getThemeNamespace('views.ecommerce.includes.product.badges'))

                            @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-5.actions'))
                            {{-- <img src="/placeholder.svg?height=120&width=160" alt="Combo sản phẩm sức khỏe"
                            class="product-image"> --}}
                            <img src="{{ RvMedia::getImageUrl($product->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                alt="{{ $product->name }}" class="product-image" loading="lazy">
                            <div class="product-info">
                                <a href="{{ $product->url }}">
                                    <h3 class="product-title">{{ $product->name }}</h3>
                                    {{-- <div class="product-price">{{ format_price($product->final_price) }}</div> --}}
                                    @include(Theme::getThemeNamespace(
                                            'views.ecommerce.includes.product.style-5.rating'))
                                    @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-5.price'))
                                    <div class="product-meta">
                                        <div class="sold-count">
                                            {{-- <i class="fa fa-shopping-cart"></i> --}}
                                            <i class="ti ti-shopping-cart-dollar"></i>
                                            <x-core::icon name="ti ti-shopping-cart-dollar" class="icon-ft-item" />
                                            <span>Đã bán {{ $product->sold_count }}</span>
                                        </div>
                                        <div class="location">
                                            {{-- <i class="fa fa-map-marker-alt"></i> --}}
                                            {{-- <i class="ti ti-map-pin"></i> --}}
                                            <x-core::icon name="ti ti-map-pin" class="icon-ft-item" />
                                            <span>{{ $stateName }}</span>
                                        </div>
                                    </div>
                                </a>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
