<style>
/* Modern Category Slider */
.tp-category-modern-area {
    overflow: hidden;
}

.tp-category-modern-slider-wrapper {
    padding: 0 50px;
    position: relative;
}

@media (max-width: 991px) {
    .tp-category-modern-slider-wrapper {
        padding: 0;
    }
}

.tp-category-modern-slider {
    overflow: visible;
}

.tp-category-modern-slider .swiper-wrapper {
    padding: 10px 0 20px;
}

.tp-category-modern-item {
    border-radius: 16px;
    overflow: hidden;
    background: var(--tp-common-white);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.tp-category-modern-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.tp-category-modern-item:hover .tp-category-modern-thumb img {
    transform: scale(1.1);
}

.tp-category-modern-item:hover .tp-category-modern-overlay {
    opacity: 0.7;
}

.tp-category-modern-item:hover .tp-category-modern-content {
    background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.5) 60%, transparent 100%);
}

.tp-category-modern-item:hover .tp-category-modern-btn {
    opacity: 1;
    transform: translateY(0);
}

.tp-category-modern-link {
    display: block;
    position: relative;
    height: 280px;
}

@media (max-width: 1199px) {
    .tp-category-modern-link {
        height: 250px;
    }
}

@media (max-width: 991px) {
    .tp-category-modern-link {
        height: 220px;
    }
}

@media (max-width: 767px) {
    .tp-category-modern-link {
        height: 200px;
    }
}

.tp-category-modern-thumb {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.tp-category-modern-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.tp-category-modern-thumb-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    color: #a0aec0;
}

.tp-category-modern-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.4) 100%);
    opacity: 0.5;
    transition: opacity 0.4s ease;
}

.tp-category-modern-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 60%, transparent 100%);
    transition: background 0.4s ease;
    z-index: 2;
}

.tp-category-modern-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--tp-common-white);
    margin-bottom: 4px;
    line-height: 1.3;
}

@media (max-width: 767px) {
    .tp-category-modern-title {
        font-size: 16px;
    }
}

.tp-category-modern-count {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.8);
    display: block;
    margin-bottom: 8px;
}

.tp-category-modern-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
    color: var(--tp-common-white);
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.tp-category-modern-btn svg {
    transition: transform 0.3s ease;
}

.tp-category-modern-btn:hover svg {
    transform: translateX(4px);
}

.tp-category-modern-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--tp-common-white);
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    color: var(--tp-common-black);
}

.tp-category-modern-nav:hover {
    background: var(--tp-theme-primary);
    color: var(--tp-common-white);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.tp-category-modern-nav:disabled,
.tp-category-modern-nav.swiper-button-disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.tp-category-modern-nav:disabled:hover,
.tp-category-modern-nav.swiper-button-disabled:hover {
    background: var(--tp-common-white);
    color: var(--tp-common-black);
}

@media (max-width: 991px) {
    .tp-category-modern-nav {
        display: none;
    }
}

.tp-category-modern-prev {
    left: 0;
}

.tp-category-modern-next {
    right: 0;
}

.tp-category-modern-pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 20px;
}

.tp-category-modern-pagination .swiper-pagination-bullet {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d1d5db;
    opacity: 1;
    transition: all 0.3s ease;
}

.tp-category-modern-pagination .swiper-pagination-bullet-active {
    width: 24px;
    border-radius: 4px;
    background: var(--tp-theme-primary);
}
</style>

<section class="tp-category-modern-area pt-60 pb-60"
    @if ($shortcode->background_color)
        style="background-color: {{ $shortcode->background_color }};"
    @endif
>
    <div class="container">
        {!! Theme::partial('section-title', compact('shortcode')) !!}

        <div class="tp-category-modern-slider-wrapper position-relative">
            <div class="tp-category-modern-slider swiper-container" data-items="{{ (int) $shortcode->items_per_view ?: 5 }}">
                <div class="swiper-wrapper">
                    @foreach ($categories as $category)
                        <div class="swiper-slide">
                            <div class="tp-category-modern-item">
                                <a href="{{ $category->url }}" title="{{ $category->name }}" class="tp-category-modern-link">
                                    <div class="tp-category-modern-thumb">
                                        @if($category->image)
                                            <img 
                                                src="{{ RvMedia::getImageUrl($category->image, 'medium') }}" 
                                                alt="{{ $category->name }}"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="tp-category-modern-thumb-placeholder">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                    <polyline points="21 15 16 10 5 21"></polyline>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="tp-category-modern-overlay"></div>
                                    </div>
                                    <div class="tp-category-modern-content">
                                        <h4 class="tp-category-modern-title">{{ $category->name }}</h4>
                                        @if ($shortcode->show_products_count)
                                            <span class="tp-category-modern-count">
                                                @if ($category->count_all_products === 1)
                                                    {{ __('1 product') }}
                                                @else
                                                    {{ __(':count products', ['count' => number_format($category->count_all_products)]) }}
                                                @endif
                                            </span>
                                        @endif
                                        <span class="tp-category-modern-btn">
                                            <span>{{ __('Xem ngay') }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                <polyline points="12 5 19 12 12 19"></polyline>
                                            </svg>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Navigation Buttons -->
            <button class="tp-category-modern-nav tp-category-modern-prev" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button class="tp-category-modern-nav tp-category-modern-next" aria-label="Next">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>

            <!-- Pagination -->
            <div class="tp-category-modern-pagination"></div>
        </div>
    </div>
</section>
