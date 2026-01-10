@extends('plugins/marketplace::themes.bitsgold-dashboard.layouts.master')

@section('content')
<div class="container my-0 my-md-3 my-lg-5 checkout-content-wrap">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-12">
            <div class="thank-you">
                <div class="d-inline-block">
                    <h3 class="thank-you-sentence">
                        {{trans('core/base::layouts.deposit-order-created')}}
                    </h3>
                    <p>{{trans('core/base::layouts.scan-qr-to-complete')}}</p>
                </div>
            </div>

            <div class="order-customer-info">
                <h3>{{trans('core/base::layouts.your-info')}}</h3>
                <p>
                    <span class="d-inline-block">{{trans('core/base::layouts.full-name')}}:</span>
                    <span class="order-customer-info-meta">{{$customer->name}}</span>
                </p>
                <p>
                    <span class="d-inline-block">{{trans('core/base::layouts.phone')}}:</span>
                    <span class="order-customer-info-meta">{{$customer->phone}}</span>
                </p>
                <p>
                    <span class="d-inline-block">{{trans('core/base::layouts.email')}}:</span>
                    <span class="order-customer-info-meta">{{$customer->email}}</span>
                </p>
                <p>
                    <span class="d-inline-block">{{trans('core/base::layouts.payment-method')}}:</span>
                    <span class="order-customer-info-meta">{{trans($deposit->method)}}</span>
                </p>
                @php
                    $statuses = [
                        0 => ['label' => 'pending', 'class' => 'bg-warning text-warning-fg'],
                        1 => ['label' => 'completed', 'class' => 'bg-success text-success-fg'],
                        2 => ['label' => 'failed', 'class' => 'bg-danger text-danger-fg'],
                    ];
                    $status = $statuses[$deposit->status] ?? ['label' => 'Không xác định', 'class' => 'bg-secondary text-secondary-fg'];
                @endphp
                
                <p>
                    <span class="d-inline-block">{{trans('core/base::layouts.payment-status')}}:</span>
                    <span class="order-customer-info-meta" style="text-transform: uppercase" data-bb-target="ecommerce-order-payment-status">
                        <span class="badge {{ $status['class'] }}">{{ trans('core/base::layouts.'.$status['label']) }}</span>
                    </span>
                </p>
            
                <style>
                    .sepay.fob-container {
                        margin-top: 2rem;
                    }

                    .sepay .fob-qr-code {
                        text-align: center;
                        margin-bottom: 40px;
                    }

                    .sepay .fob-qr-code img {
                        width: 250px;
                        height: auto;
                        margin: 0;
                        padding: 0;
                    }

                    .sepay .fob-qr-code figcaption {
                        margin-top: 10px;
                        font-size: 14px;
                        color: #666;
                    }

                    .sepay .fob-qr-intro {
                        margin-bottom: 10px;
                        font-size: 16px;
                    }

                    .sepay .transaction-status-done {
                        background-color: var(--bs-tertiary-bg);
                        border: none;
                        color: var(--primary-color);
                    }

                    .sepay .transaction-status-done .icon {
                        width: 40px;
                        height: 40px;
                    }

                    .sepay .transaction-status-fail {
                        background-color: var(--bs-tertiary-bg);
                        border: none;
                        color: #D63939;
                    }

                    .sepay .transaction-status-fail .icon {
                        width: 40px;
                        height: 40px;
                    }
                </style>
                <div id="fob-sepay-bank" class="sepay fob-container">
                    @if($deposit->status == 0)
                    <div id="sepay-bank-info">
                        <div class="fob-qr-intro">
                            {{trans('core/base::layouts.how-to-pay-1')}}
                        </div>
                        <div class="fob-qr-code">
                            <figure>
                                <img src="https://img.vietqr.io/image/{{ setting('payment_sepay_bank')}}-{{ setting('payment_sepay_account_number')}}-qr_only.png?amount={{ $deposit->amount}}&addInfo={{ $deposit->transaction_code}}" alt="QR Code">
                            </figure>
                        </div>

                        <div class="fob-qr-intro">
                            {{trans('core/base::layouts.how-to-pay-2')}}
                        </div>
                        <div class="fob-qr-information">
                            <table class="table table-hover table-striped">
                                <tbody>
                                    <tr>
                                        <td>{{trans('core/base::layouts.bank-name')}}</td>
                                        <td>
                                            <strong>{{setting('payment_sepay_bank')}}</strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('core/base::layouts.account-holder')}}</td>
                                        <td>
                                            <strong>{{setting('payment_sepay_account_holder')}}</strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('core/base::layouts.account-number')}}</td>
                                        <td>
                                            <strong>{{setting('payment_sepay_account_number')}}</strong>
                                        </td>
                                        <td class="text-end" style="width: 80px;">
                                            <a href="javascript:void(0);" rel="nooper" class="ms-2" type="button"
                                                data-clipboard="{{setting('payment_sepay_account_number')}}" data-bb-toggle="copy">
                                                <svg class="icon  svg-icon-ti-ti-clipboard"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2">
                                                    </path>
                                                    <path
                                                        d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z">
                                                    </path>
                                                </svg> </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('core/base::layouts.transfer-content')}}</td>
                                        <td>
                                            <strong>{{$deposit->transaction_code}}</strong>
                                        </td>
                                        <td class="text-end" style="width: 80px;">
                                            <a href="javascript:void(0);" rel="nooper" class="ms-2" type="button"
                                                data-clipboard="{{$deposit->transaction_code}}" data-bb-toggle="copy">
                                                <svg class="icon  svg-icon-ti-ti-clipboard"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2">
                                                    </path>
                                                    <path
                                                        d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z">
                                                    </path>
                                                </svg> </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('core/base::layouts.transaction-amount')}}</td>
                                        <td>
                                            <strong>{{format_price($deposit->amount)}}</strong>
                                        </td>
                                        <td class="text-end" style="width: 80px;">
                                            <a href="javascript:void(0);" rel="nooper" class="ms-2" type="button"
                                                data-clipboard="{{$deposit->amount}}" data-bb-toggle="copy">
                                                <svg class="icon  svg-icon-ti-ti-clipboard"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2">
                                                    </path>
                                                    <path
                                                        d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z">
                                                    </path>
                                                </svg> </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="alert alert-warning">
                                <p>{{trans('core/base::layouts.keep-transfer-content-1')}} <strong
                                        class="text-danger">{{$deposit->transaction_code}}</strong> {{trans('core/base::layouts.keep-transfer-content-2')}} <strong
                                        class="text-danger">{{format_price($deposit->amount)}}</strong> {{trans('core/base::layouts.keep-transfer-content-3')}}
                                </p>
                            </div>

                            <div class="transaction-status text-center" data-bb-toggle="sepay-transaction-status"
                                data-url="{{route('deposit.checkStatus', $deposit->id)}}"
                                data-charge-id="">
                                {{trans('core/base::layouts.waiting-payment-status')}}
                                <img
                                    src="{{url('/')}}/vendor/core/plugins/fob-sepay/images/loading.gif"
                                    width="20" height="20" alt="Loading">
                            </div>
                        </div>
                    </div>
                    @elseif($deposit->status == 2)
                    <div style="" id="sepay-transaction-status-done">
                        <div class="transaction-status-fail card text-center pb-3 pt-2">
                            <div class="p-4">
                                <div class="mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-xbox-x">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 21a9 9 0 0 0 9 -9a9 9 0 0 0 -9 -9a9 9 0 0 0 -9 9a9 9 0 0 0 9 9z" />
                                        <path d="M9 8l6 8" />
                                        <path d="M15 8l-6 8" />
                                    </svg>
                                </div>
                                <h4>{{trans('core/base::layouts.failed-payment-status')}}</h4>
                            </div>
                        </div>
                    </div>
                    @else
                    <div style="" id="sepay-transaction-status-done">
                        <div class="transaction-status-done card text-center pb-3 pt-2">
                            <div class="p-4">
                                <div class="mb-2">
                                    <svg class="icon  svg-icon-ti-ti-circle-check"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                        <path d="M9 12l2 2l4 -4"></path>
                                    </svg>
                                </div>
                                <h4>{{trans('core/base::layouts.success-payment-status')}}</h4>
                            </div>
                        </div>
                    </div>
                    @endif



                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const copyButtons = document.querySelectorAll('[data-bb-toggle="copy"]');

                        copyButtons.forEach((button) => {
                            button.addEventListener('click', function (event) {
                                event.preventDefault();
                                event.stopPropagation();
                                const textToCopy = this.getAttribute('data-clipboard');
                                fobCopyToClipboard(textToCopy);
                            })
                        })

                    })

                    let interval = null;

                    $(document).ready(function () {
                        const paymentStatus = $('[data-bb-toggle="sepay-transaction-status"]');

                        if (paymentStatus.length) {
                            interval = setInterval(() => fetchPaymentStatus(paymentStatus), 3000);
                        }
                    });

                    function fetchPaymentStatus(elm) {
                        $.ajax({
                            url: elm.data('url'),
                            method: 'POST',
                            data: {
                                // Nếu cần gửi thêm data khác thì thêm ở đây
                            },
                            success: (response) => {
                                if (response.status !== 0) {
                                    clearInterval(interval);
                                    location.reload();
                                }
                            },
                            error: (xhr) => {
                                console.error('Có lỗi xảy ra:', xhr.responseText);
                            }
                        });
                    }

                    async function fobCopyToClipboard(textToCopy) {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(textToCopy);
                        } else {
                            fobUnsecuredCopyToClipboard(textToCopy);
                        }

                        MainCheckout.showSuccess('Sao chép thành công!');
                    }

                    function fobUnsecuredCopyToClipboard(textToCopy) {
                        const textArea = document.createElement('textarea');
                        textArea.value = textToCopy;
                        textArea.style.position = 'absolute';
                        textArea.style.left = '-999999px';
                        document.body.append(textArea);
                        textArea.focus();
                        textArea.select();

                        try {
                            document.execCommand('copy');
                        } catch (error) {
                            console.error('Unable to copy to clipboard', error);
                        }

                        document.body.removeChild(textArea);
                    }
                </script>

            </div>
        </div>

        <div class="col-lg-5 col-md-6 mt-5 mt-md-0 mb-5">
            <div class="bg-light p-3 pt-0">
                <div class="pt-3 mb-5 order-item-info">
                    <div class="align-items-center">
                        <h6 class="d-inline-block">{{trans('core/base::layouts.order-code')}}: {{$deposit->transaction_code}}</h6>
                    </div>

                    <div class="checkout-success-products">
                        <div id="cart-item-53">
                            <div class="row cart-item">
                                <div class="col-lg-3 col-md-3">
                                    <div class="checkout-product-img-wrapper d-inline-block">
                                        <img class="item-thumb img-thumbnail img-rounded mb-2 mb-md-0"
                                            src="{{url('/')}}/storage/san-pham/sinh-khi-pqa-78-150x150.jpg"
                                            alt="NẠP TIỀN VÀO VÍ">
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-5">
                                    <p class="mb-2 mb-md-0">{{trans('core/base::layouts.deposit-description')}}</p>
                                    <p class="mb-2 mb-md-0">
                                        <small></small>
                                    </p>

                                </div>
                                <div class="col-lg-4 col-md-4 col-4 float-md-end text-md-end">
                                    <p>{{format_price($deposit->amount)}}</p>
                                </div>
                            </div>

                            <hr class="border-dark-subtle">

                            <div class="row">
                                <div class="col-6">
                                    <p>{{trans('core/base::layouts.subtotal')}}:</p>
                                </div>
                                <div class="col-6 float-end">
                                    <p class="price-text text-end">{{format_price($deposit->amount)}}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <p>{{trans('core/base::layouts.fee')}}:</p>
                                </div>
                                <div class="col-6 float-end">
                                    <p class="price-text text-end">{{format_price(0)}}</p>
                                </div>
                            </div>
                            <hr class="border-dark-subtle">

                            <div class="row">
                                <div class="col-6">
                                    <p>{{trans('core/base::layouts.total')}}:</p>
                                </div>
                                <div class="col-6 float-end">
                                    <p class="total-text raw-total-text text-end">{{format_price($deposit->amount + 0)}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection