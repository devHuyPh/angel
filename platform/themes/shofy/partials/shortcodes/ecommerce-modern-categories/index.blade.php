<style>
/* Modern Category Slider Styles */
.ecommerce-modern-categories {
    overflow: hidden;
}

.ecommerce-modern-categories__wrapper {
    padding: 0 50px;
    position: relative;
}

@media (max-width: 991px) {
    .ecommerce-modern-categories__wrapper {
        padding: 0;
    }
}

.ecommerce-modern-categories__slider {
    overflow: hidden;
}

.ecommerce-modern-categories__slider .swiper-wrapper {
    padding: 10px 0 20px;
}

.ecommerce-modern-categories__item {
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.ecommerce-modern-categories__item:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.ecommerce-modern-categories__item:hover .ecommerce-modern-categories__thumb img {
    transform: scale(1.1);
}

.ecommerce-modern-categories__item:hover .ecommerce-modern-categories__overlay {
    opacity: 0.7;
}

.ecommerce-modern-categories__item:hover .ecommerce-modern-categories__content {
    background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.5) 60%, transparent 100%);
}

.ecommerce-modern-categories__item:hover .ecommerce-modern-categories__btn {
    opacity: 1;
    transform: translateY(0);
}

.ecommerce-modern-categories__link {
    display: block;
    position: relative;
    height: 280px;
}

@media (max-width: 1199px) {
    .ecommerce-modern-categories__link {
        height: 250px;
    }
}

@media (max-width: 991px) {
    .ecommerce-modern-categories__link {
        height: 220px;
    }
}

@media (max-width: 767px) {
    .ecommerce-modern-categories__link {
        height: 200px;
    }
}

.ecommerce-modern-categories__thumb {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.ecommerce-modern-categories__thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.ecommerce-modern-categories__thumb-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    color: #a0aec0;
}

.ecommerce-modern-categories__overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.4) 100%);
    opacity: 0.5;
    transition: opacity 0.4s ease;
}

.ecommerce-modern-categories__content {
    position: absolute;
    bottom: -25px;
    left: 18px;
    right: 0;
    padding: 20px;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 60%, transparent 100%);
    transition: background 0.4s ease;
    z-index: 2;
}

.ecommerce-modern-categories__title {
    font-size: 12px!important;
    font-weight: 600;
    color: #fff;
    margin-bottom: 4px;
    line-height: 1.3;
}

@media (max-width: 767px) {
    .ecommerce-modern-categories__title {
        font-size: 12px!important;
    }
}

.ecommerce-modern-categories__count {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.8);
    display: block;
    margin-bottom: 8px;
}

.ecommerce-modern-categories__btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #fff;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.ecommerce-modern-categories__btn svg {
    transition: transform 0.3s ease;
}

.ecommerce-modern-categories__btn:hover svg {
    transform: translateX(4px);
}

.ecommerce-modern-categories__nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #fff;
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    color: #000;
}

.ecommerce-modern-categories__nav:hover {
    background: var(--tp-theme-primary, #0989ff);
    color: #fff;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.ecommerce-modern-categories__nav:disabled,
.ecommerce-modern-categories__nav.swiper-button-disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.ecommerce-modern-categories__nav:disabled:hover,
.ecommerce-modern-categories__nav.swiper-button-disabled:hover {
    background: #fff;
    color: #000;
}

@media (max-width: 991px) {
    .ecommerce-modern-categories__nav {
        display: none;
    }
}

.ecommerce-modern-categories__prev {
    left: 0;
}

.ecommerce-modern-categories__next {
    right: 0;
}

.ecommerce-modern-categories__pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 20px;
}

.ecommerce-modern-categories__pagination .swiper-pagination-bullet {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d1d5db;
    opacity: 1;
    transition: all 0.3s ease;
}

.ecommerce-modern-categories__pagination .swiper-pagination-bullet-active {
    width: 24px;
    border-radius: 4px;
    background: var(--tp-theme-primary, #0989ff);
}
</style>

<section class="ecommerce-modern-categories pt-60 pb-60"
    @if ($shortcode->background_color && $shortcode->background_color !== 'transparent')
        style="background-color: {{ $shortcode->background_color }};"
    @endif
>
    <div class="container">
        @if ($shortcode->title || $shortcode->subtitle)
            <div class="tp-section-title-wrapper mb-40 text-center">
                @if ($shortcode->subtitle)
                    <span class="tp-section-title-pre">{{ $shortcode->subtitle }}</span>
                @endif
                @if ($shortcode->title)
                    <h3 class="tp-section-title">{{ $shortcode->title }}</h3>
                @endif
            </div>
        @endif

        <div class="ecommerce-modern-categories__wrapper position-relative">
            <div class="ecommerce-modern-categories__slider swiper" data-items="{{ $shortcode->items_per_view }}">
                <div class="swiper-wrapper">
                    @foreach ($categories as $category)
                        <div class="swiper-slide">
                            <div class="ecommerce-modern-categories__item">
                                <a href="{{ $category->url }}" title="{{ $category->name }}" class="ecommerce-modern-categories__link">
                                    <div class="ecommerce-modern-categories__thumb">
                                        @if($category->image)
                                            <img 
                                                src="{{ RvMedia::getImageUrl($category->image, 'medium') }}" 
                                                alt="{{ $category->name }}"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="ecommerce-modern-categories__thumb-placeholder">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                    <polyline points="21 15 16 10 5 21"></polyline>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ecommerce-modern-categories__overlay"></div>
                                    </div>
                                    <div class="ecommerce-modern-categories__content">
                                        <h4 class="ecommerce-modern-categories__title">{{ $category->name }}</h4>
                                        @if ($shortcode->show_products_count)
                                            <span class="ecommerce-modern-categories__count">
                                                @if ($category->count_all_products === 1)
                                                    {{ __('1 product') }}
                                                @else
                                                    {{ __(':count products', ['count' => number_format($category->count_all_products)]) }}
                                                @endif
                                            </span>
                                        @endif
                                        <span class="ecommerce-modern-categories__btn">
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
            <button class="ecommerce-modern-categories__nav ecommerce-modern-categories__prev" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button class="ecommerce-modern-categories__nav ecommerce-modern-categories__next" aria-label="Next">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>

            <!-- Pagination -->
            <div class="ecommerce-modern-categories__pagination"></div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sliders = document.querySelectorAll('.ecommerce-modern-categories__slider');
    
    sliders.forEach(function(slider) {
        const wrapper = slider.closest('.ecommerce-modern-categories__wrapper');
        const itemsPerView = parseInt(slider.dataset.items) || 4;
        
        new Swiper(slider, {
            slidesPerView: itemsPerView,
            spaceBetween: 24,
            loop: false,
            observer: true,
            observeParents: true,
            navigation: {
                nextEl: wrapper.querySelector('.ecommerce-modern-categories__next'),
                prevEl: wrapper.querySelector('.ecommerce-modern-categories__prev'),
            },
            pagination: {
                el: wrapper.querySelector('.ecommerce-modern-categories__pagination'),
                clickable: true,
            },
            breakpoints: {
                1200: {
                    slidesPerView: itemsPerView,
                    spaceBetween: 24,
                },
                992: {
                    slidesPerView: Math.max(itemsPerView - 1, 3),
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 16,
                },
                576: {
                    slidesPerView: 2,
                    spaceBetween: 12,
                },
                0: {
                    slidesPerView: 1.5,
                    spaceBetween: 12,
                },
            },
        });
    });
});

// Also init on shortcode loaded event
document.addEventListener('shortcode.loaded', function() {
    const sliders = document.querySelectorAll('.ecommerce-modern-categories__slider:not(.swiper-initialized)');
    
    sliders.forEach(function(slider) {
        const wrapper = slider.closest('.ecommerce-modern-categories__wrapper');
        const itemsPerView = parseInt(slider.dataset.items) || 4;
        
        new Swiper(slider, {
            slidesPerView: itemsPerView,
            spaceBetween: 24,
            loop: false,
            observer: true,
            observeParents: true,
            navigation: {
                nextEl: wrapper.querySelector('.ecommerce-modern-categories__next'),
                prevEl: wrapper.querySelector('.ecommerce-modern-categories__prev'),
            },
            pagination: {
                el: wrapper.querySelector('.ecommerce-modern-categories__pagination'),
                clickable: true,
            },
            breakpoints: {
                1200: {
                    slidesPerView: itemsPerView,
                    spaceBetween: 24,
                },
                992: {
                    slidesPerView: Math.max(itemsPerView - 1, 3),
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 16,
                },
                576: {
                    slidesPerView: 2,
                    spaceBetween: 12,
                },
                0: {
                    slidesPerView: 1.5,
                    spaceBetween: 12,
                },
            },
        });
    });
});
</script>
