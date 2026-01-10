<section class="tp-category-area pt-3" style="background-color: #F3F5F7 !important;">
    <div class="container">
        {!! Theme::partial('section-title', compact('shortcode')) !!}

        <div class="row g-3">
            @foreach ($categories as $category)
                <div class="col-3">
                    <div class="tp-category-main mb-3 p-relative fix text-center"
                        @if ($shortcode->background_color) style="background-color: {{ $shortcode->background_color }} !important;"
                        @else
                            style="background-color: #F3F5F7 !important;" @endif>
                        <a href="{{ $category->url }}" title="{{ $category->name }}" class="d-block">
                            {{-- Ảnh vuông nhỏ dùng ratio 1x1 --}}
                            <div class="ratio ratio-1x1">
                                @if ($category->image)
                                    <img src="{{ RvMedia::getImageUrl($category->image) }}" alt="{{ $category->name }}"
                                        class="img-fluid w-100 h-100 object-fit-cover">
                                @endif
                            </div>
                        </a>

                        <div class="text-center pt-2">
                            <p class="mb-1 fw-semibold small">
                                <a href="{{ $category->url }}" title="{{ $category->name }}"
                                    class="text-dark text-decoration-none">
                                    {{ $category->name }}
                                </a>
                            </p>

                            @if ($shortcode->show_products_count)
                                <span class="d-block small text-muted">
                                    @if ($category->count_all_products === 1)
                                        {{ __('1 product') }}
                                    @else
                                        {{ __(':count products', ['count' => number_format($category->count_all_products)]) }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
