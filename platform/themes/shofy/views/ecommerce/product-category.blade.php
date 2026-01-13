@php
    Theme::set('pageTitle', $category->name);
@endphp

<section class="tp-shop-area @if (! theme_option('theme_breadcrumb_enabled', true)) pt-50 @endif">
    <div class="container position-relative">
        @php
            $storeOptions = $storeOptions ?? collect();
            $selectedStoreId = $selectedStoreId ?? null;
            $shouldShowStoreModal = $shouldShowStoreModal ?? false;
        @endphp

        @if ($storeOptions->count())
            <div class="store-pill d-flex align-items-center gap-2 mb-3">
                <span class="fw-semibold">{{ __('Cửa hàng đang xem:') }}</span>
                @if ($selectedStoreId)
                    @php
                        $activeStore = $storeOptions->firstWhere('id', $selectedStoreId);
                    @endphp
                    <span class="badge bg-primary">
                        {{ $activeStore->name ?? __('Cửa hàng') }}
                    </span>
                @else
                    <span class="text-muted">{{ __('Vui lòng chọn cửa hàng') }}</span>
                @endif
                <button type="button" class="btn btn-sm btn-outline-primary ms-auto" data-store-modal-open>
                    {{ $selectedStoreId ? __('Đổi cửa hàng') : __('Chọn cửa hàng') }}
                </button>
            </div>

            <div class="store-select-modal" data-store-modal style="display: {{ $shouldShowStoreModal ? 'flex' : 'none' }}">
                <div class="store-select-card">
                    <h5 class="mb-3">{{ __('Chọn cửa hàng để xem sản phẩm trong danh mục này') }}</h5>
                    <div class="list-group mb-3">
                        @foreach ($storeOptions as $store)
                            <button type="button"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-start"
                                data-store-id="{{ $store->id }}">
                                <div>
                                    <div class="fw-semibold">{{ $store->name }}</div>
                                    @if (!empty($store->address))
                                        <small class="text-muted">{{ $store->address }}</small>
                                    @endif
                                </div>
                                <span class="text-primary">&rarr;</span>
                            </button>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-secondary w-100" data-store-modal-close>
                        {{ __('Đóng') }}
                    </button>
                </div>
            </div>

            <style>
                .store-select-modal {
                    position: fixed;
                    inset: 0;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 1050;
                    display: none;
                    align-items: center;
                    justify-content: center;
                    padding: 1rem;
                }

                .store-select-card {
                    background: #fff;
                    border-radius: 12px;
                    max-width: 520px;
                    width: 100%;
                    padding: 24px;
                    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
                }

                .store-pill {
                    background: #f6f8ff;
                    border: 1px solid #e1e5ff;
                    border-radius: 12px;
                    padding: 10px 14px;
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const modal = document.querySelector('[data-store-modal]');
                    const openBtn = document.querySelector('[data-store-modal-open]');
                    const closeBtn = document.querySelector('[data-store-modal-close]');

                    const openModal = () => {
                        if (modal) {
                            modal.style.display = 'flex';
                        }
                    };

                    const closeModal = () => {
                        if (modal) {
                            modal.style.display = 'none';
                        }
                    };

                    if (openBtn) openBtn.addEventListener('click', openModal);
                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    modal?.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            closeModal();
                        }
                    });

                    modal?.querySelectorAll('[data-store-id]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const storeId = button.getAttribute('data-store-id');
                            const params = new URLSearchParams(window.location.search);
                            params.set('store', storeId);
                            window.location.search = params.toString();
                        });
                    });
                });
            </script>
        @endif

        {!! dynamic_sidebar('products_by_category_top_sidebar') !!}

        @include(Theme::getThemeNamespace('views.ecommerce.includes.products-listing'), ['pageName' => $category->name, 'pageDescription' => $category->description])

        {!! dynamic_sidebar('products_by_category_bottom_sidebar') !!}
    </div>
</section>
