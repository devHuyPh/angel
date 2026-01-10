@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', SeoHelper::getTitle())

@section('content')
    <style>
        @media (max-width: 767.98px) {
            .profile__tab-content {
                padding: 0 !important;

            }

            .form-control {
                font-size: 16px !important;
            }
        }
    </style>
    <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <a href="{{ route('setting') }}" class="back-btn text-success">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="header-title text-success">{{ __('Đánh giá sản phẩm') }}</h1>
    </div>
    <style>
        .mobile {
            display: none !important;
        }

        @media (max-width: 767.98px) {

            .bg-custom-moblie {
                padding: 0 !important;
            }


            .head-mobile-order {
                background: #f8f8f8;
                padding: 0.5rem 0 0.5rem 10px !important;
                border-radius: 0.375rem !important;
            }

            h3 {
                font-size: 16px !important;
                margin-bottom: 0 !important;
                overflow: hidden !important;
                white-space: nowrap !important;
                text-overflow: ellipsis !important;
            }

            .desktop {
                display: none !important;
            }

            .mobile {
                display: block !important;
            }

            .card-body {
                padding: 0 !important;
            }

            .shipment-status {
                color: #fff;
                border-radius: 5px;
                display: flex;
                justify-content: space-around;
                align-items: center;
            }

            .head-shipment {
                display: flex;
                gap: 10px;
            }

            .products {
                /* display: flex;
                                                                                                                                            gap: 10px;
                                                                                                                                            border-radius: 0.375rem; */
                /* justify-content: space-between; */
            }

            .img-pro {
                padding-left: 0 !important;

                img {
                    max-width: 90px;
                    border-radius: 0.375rem;
                }
            }

            .content-pro {
                /* width: 235px; */
                /* width: 14.6875rem; */
                /* width: 100% */
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .footer-ct-pro {
                display: flex;
                justify-content: space-between;

            }

            .head-ct-pro {
                p {
                    font-weight: bold;
                    overflow: hidden !important;
                    white-space: nowrap !important;
                    text-overflow: ellipsis !important;
                    margin-bottom: 0 !important;
                }

            }

            .discount-price {
                text-decoration: line-through;

            }

            .bill,
            .order-info {
                p {
                    margin-bottom: 0 !important;
                }
            }

            .btn {
                border-radius: 0.375rem !important;
            }

            .paginate {
                background: #f8f8f8;

                p {
                    margin-bottom: 0 !important;
                }
            }
        }
    </style>

    <div class="container pb-3">
        @if ($products->isNotEmpty() || $reviews->isNotEmpty())
            @include(EcommerceHelper::viewPath('customers.product-reviews.icons'))

            <div class="product-reviews-page">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if (!request()->has('page')) active @endif" id="waiting-tab"
                            data-toggle="tab" data-target="#waiting-tab-pane" data-bs-toggle="tab"
                            data-bs-target="#waiting-tab-pane" type="button" role="tab"
                            aria-controls="waiting-tab-pane" aria-selected="true">
                            {{ __('Waiting for your review') }} ({{ $products->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if (request()->has('page')) active @endif" id="reviewed-tab"
                            data-toggle="tab" data-target="#reviewed-tab-pane" data-bs-toggle="tab"
                            data-bs-target="#reviewed-tab-pane" type="button" role="tab"
                            aria-controls="reviewed-tab-pane" aria-selected="false">
                            {{ __('Reviewed') }} ({{ $reviews->total() }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-3">
                    <div class="tab-pane fade @if (!request()->has('page')) show active @endif" id="waiting-tab-pane"
                        role="tabpanel" aria-labelledby="waiting-tab" tabindex="0">
                        @if ($products->isNotEmpty())
                            {{-- @dd($products) --}}
                            <div class="row row-cols-md-2 row-cols-1 g-3 desktop">
                                @foreach ($products as $product)
                                    <div class="col ">
                                        <div class="ecommerce-product-item border p-3" data-id="{{ $product->id }}">
                                            <div class="d-flex gap-2">
                                                {{ RvMedia::image($product->order_product_image ?: $product->image, $product->name, 'thumb', true, ['class' => 'img-fluid rounded-start ecommerce-product-image']) }}

                                                <div>
                                                    <a href="{{ $product->url }}">
                                                        <h6 class="card-title ecommerce-product-name">
                                                            {!! BaseHelper::clean($product->order_product_name ?: $product->name) !!}
                                                        </h6>
                                                    </a>

                                                    @if ($product->order_completed_at)
                                                        <div class="text-muted mt-1">
                                                            {{ __('Order completed at') }}:
                                                            <time>{{ Carbon\Carbon::parse($product->order_completed_at)->translatedFormat('M d, Y h:m') }}</time>
                                                        </div>
                                                    @endif

                                                    <div class="d-flex ecommerce-product-star mt-1 w-50">
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            <label class="order-{{ $i }}">
                                                                <x-core::icon name="ti ti-star-filled"
                                                                    class="ecommerce-icon"
                                                                    data-star="{{ $i }}" />
                                                            </label>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @foreach ($products as $product)
                                <div class="card mobile mb-2 mx-2">
                                    <div class="card-body">
                                        <div
                                            class="card-title d-flex head-mobile-order justify-content-between align-items-center">
                                            <div class="col-7">
                                                <h3 class="text-success">
                                                    Unigreen - Linh Chi
                                                </h3>
                                            </div>
                                            @if ($product->order_completed_at)
                                                <div class="ecommerce-product-item" data-id="{{ $product->id }}">
                                                    <div class="head-ct-pro ecommerce-product-name d-none">
                                                        <p>{{ $product->order_product_name ?: $product->name }}</p>
                                                    </div>
                                                    <div class="ecommerce-product-star px-2">
                                                        <label class="order-5">
                                                            <span class="btn btn-success text-success-fg ecommerce-icon"
                                                                data-star="5">Viết đánh giá</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                {{-- <div class="col-5 order-5">
                                                    <span class="btn btn-success text-success-fg ecommerce-icon"
                                                        data-star="5">
                                                        {{ trans('core/base::layouts.completed') }}
                                                        adfasd
                                                    </span>
                                                </div> --}}
                                            @endif

                                        </div>

                                        <div class="ecommerce-product-item" data-id="{{ $product->id }}">
                                            <div class="products p-2 bg-light text-white mx-2 mb-1 row align-items-center">
                                                <div class="img-pro col-4">
                                                    <img src="{{ url('/storage') . '/' . $product->order_product_image ?: $product->image, $product->name }}"
                                                        alt="{{ $product->order_product_name ?: $product->name }}">
                                                </div>
                                                <div class="content-pro col-8">
                                                    <div class="head-ct-pro ecommerce-product-name">
                                                        <p>{{ $product->order_product_name ?: $product->name }}</p>
                                                    </div>
                                                    <div class="text-muted mt-1">
                                                        {{ __('Order completed at') }}:
                                                        <time>{{ Carbon\Carbon::parse($product->order_completed_at)->translatedFormat('M d, Y h:m') }}</time>
                                                    </div>
                                                    <div class="d-flex ecommerce-product-star mt-1 w-50 col-6">
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            <label class="order-{{ $i }}">
                                                                <x-core::icon name="ti ti-star-filled"
                                                                    class="ecommerce-icon"
                                                                    data-star="{{ $i }}" />
                                                            </label>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div role="alert" class="alert alert-info">
                                {{ __('You do not have any products to review yet. Just shopping!') }}</div>
                        @endif
                    </div>

                    <div class="tab-pane fade @if (request()->has('page')) show active @endif" id="reviewed-tab-pane"
                        role="tabpanel" aria-labelledby="reviewed-tab" tabindex="0">
                        @include(EcommerceHelper::viewPath('customers.product-reviews.reviewed'))
                    </div>
                </div>

                @include(EcommerceHelper::viewPath('customers.product-reviews.modal'))
            </div>
        @else
            @include(EcommerceHelper::viewPath('customers.partials.empty-state'), [
                'title' => __('No reviews yet!'),
                'subtitle' => __('You have not reviewed any products yet.'),
                'actionUrl' => route('public.products'),
                'actionLabel' => __('Start shopping now'),
            ])
        @endif
    </div>
@endsection
