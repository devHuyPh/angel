@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Orders'))


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
    <h1 class="header-title text-success">{{ __('Đơn hàng') }}</h1>
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
@if ($orders->isNotEmpty())
    @foreach ($orders as $order)
        <div class="card mobile mb-2 mx-2">
            <div class="card-body">
                <div class="card-title d-flex head-mobile-order justify-content-between align-items-center">
                    <div class="col-7">
                        <h3 class="text-success">
                            @if ($order->store == '')
                                {{ $order->store->name }}
                            @else
                                Unigreen - Linh Chi
                            @endif
                        </h3>
                    </div>
                    <div class="col-5">
                        @php
                            $statusKey = $order->status->getValue(); // hoặc $order->status->value nếu là backed enum

                            $statusClasses = [
                                'not_approved' => 'secondary text-secondary-fg',
                                'approved' => 'primary text-primary-fg',
                                'pending' => 'warning text-warning-fg',
                                'completed' => 'success text-success-fg',
                                'processing' => 'info text-info-fg',
                                'arrange_shipment' => 'info text-info-fg',
                                'ready_to_be_shipped_out' => 'info text-info-fg',
                                'picking' => 'primary text-primary-fg',
                                'delay_picking' => 'warning text-warning-fg',
                                'picked' => 'success text-success-fg',
                                'not_picked' => 'danger text-danger-fg',
                                'delivering' => 'primary text-primary-fg',
                                'delivered' => 'success text-success-fg',
                                'not_delivered' => 'danger text-danger-fg',
                                'audited' => 'success text-success-fg',
                                'canceled' => 'danger text-danger-fg',
                            ];
                        @endphp

                        <span class="btn btn-{{ $statusClasses[$statusKey] ?? 'secondary text-secondary-fg' }}">
                            {{ trans('core/base::layouts.' . $statusKey) }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('customer.orders.view', $order->id) }}" class="shipment">
                    <div class="shipment-status p-2 bg-success text-white m-2">
                        <i class="fa fa-truck-fast"></i>
                        @php
                            $shipment = $order->shipment;
                            $shipmentNew = $shipment?->histories?->sortByDesc('created_at')?->first();
                        @endphp

                        @if ($shipmentNew)
                            @php
                                $rawDescription = $shipmentNew->description ?? '';
                                $cleanDescription = Str::before($rawDescription, 'Cập nhật bởi:');

                                $customDescription = Str::replaceFirst(
                                    'Đã thay đổi trạng thái vận chuyển thành:',
                                    'Đơn hàng của bạn:',
                                    trim($cleanDescription),
                                );
                            @endphp
                            <div class="content-shipment">
                                <div class="head-shipment">
                                    <strong>{{ date_format($shipmentNew->updated_at, 'H:i - d/m/Y') }}</strong>
                                    <strong>{{ trans('core/base::layouts.' . $shipmentNew->shipment->status) }}</strong>
                                </div>
                                <p class="text-white mb-0">{{ $customDescription }}</p>
                            </div>
                            <i class="fa fa-arrow-right"></i>
                        @else
                            <div class="content-shipment">
                                <div class="head-shipment">
                                    <strong>{{ date_format($order->updated_at, 'H:i - d/m/Y') }}</strong>
                                    <strong>{{ trans('core/base::layouts.' . $order->status) }}</strong>
                                </div>
                                <p class="text-white mb-0">Đơn hàng của bạn đã được gửi lên</p>
                            </div>
                            <i class="fa fa-arrow-right"></i>
                        @endif
                    </div>
                </a>
                @php
                    $products = $order->products;
                @endphp
                @foreach ($products as $product)
                    <div class="products p-2 bg-light text-white mx-2 mb-1 row align-items-center">
                        <div class="img-pro col-4">
                            <img src="{{ url('/storage') . '/' . $product->product_image }}" alt="{{ $product->product_name }}">
                        </div>
                        <div class="content-pro col-8">
                            <div class="head-ct-pro">
                                <p>{{ $product->product_name }}</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p>x {{ $product->qty }}</p>
                                <p>{{ format_price($product->price) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
                @php
                    $payment = $order->payment;
                    $shippingAddress = $order->shippingAddress;
                    $aa = $order->address->full_address;
                @endphp
                <div class="bill bg-white text-dark p-2 mx-2 text-end ">
                    <p>{{ __('Subtotal') }}: {{ format_price($order->sub_total) }}</p>
                    <p>{{ __('Tax') }}: {{ format_price($order->tax_amount) }}</p>
                    <p>{{ __('Shipping fee') }}: {{ format_price($order->shipping_amount) }}</p>
                    <p>{{ __('Chiết khấu') }}:
                        <span class="discount-price">{{ format_price($order->discount_amount) }}</span>
                    </p>
                    <strong>{{ __('Total') }}: {{ format_price($order->amount) }}</strong>
                </div>
                {{-- @if ($order->status == 'completed')
                <div class="review p-2 bg-white text-dark mx-2 mb-1">
                    <div class="button-review text-end">
                        <button class="btn btn-success ">Viết đánh giá</button>
                    </div>
                    <div class="quick-review p-2 bg-white text-dark mx-2 mb-1">
                        <a href="" class="d-flex justify-content-between align-items-center">
                            <div>
                                Đánh giá nhanh
                            </div>
                            <div class="star">
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                        </a>
                    </div>
                </div>
                @endif --}}

            </div>
        </div>
    @endforeach
    <div class="my-3 py-3 paginate container mobile d-flex align-items-center justify-content-between">
        <p class="text-success">{{ trans('core/base::layouts.show_from') }}
            {{ $orders->firstItem() }}
            {{ trans('core/base::layouts.to') }}
            {{ $orders->lastItem() }}
            {{ trans('core/base::layouts.in') }}
            {{ $orders->total() }}
            {{ trans('core/base::layouts.records') }}
        </p>
        {{ $orders->links() }}
    </div>
    {{-- duog --}}
    <div class="table-responsive customer-list-order desktop">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{{ __('Order number') }}</th>
                    <th>{{ __('Created at') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Payment method') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->code }}</td>
                        <td>{{ $order->created_at->format('d M Y H:i:s') }}</td>
                        <td>{{ __(':price for :total item(s)', ['price' => $order->amount_format, 'total' => $order->products_count]) }}
                        </td>
                        <td>
                            @if (is_plugin_active('payment') && $order->payment->id && $order->payment->payment_channel->label())
                                {{ $order->payment->payment_channel->label() }}
                            @else
                                &mdash;
                            @endif
                        </td>

                        <td>{!! BaseHelper::clean($order->status->toHtml()) !!}</td>

                        <td>
                            <a class="btn btn-primary btn-sm"
                                href="{{ route('customer.orders.view', $order->id) }}">{{ __('View') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {!! $orders->links() !!}
    </div>
@else
    @include(EcommerceHelper::viewPath('customers.partials.empty-state'), [
        'title' => __('No orders yet!'),
        'subtitle' => __('You have not placed any orders yet.'),
        'actionUrl' => route('public.products'),
        'actionLabel' => __('Start shopping now'),
    ])
@endif
@stop
