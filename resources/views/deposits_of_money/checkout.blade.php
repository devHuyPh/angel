@extends('plugins/marketplace::themes.bitsgold-dashboard.layouts.master')

@section('content')
  <div class="container my-0 my-md-3 my-lg-5 checkout-content-wrap">
      <link href="https://mocthienan.hisotechgroup.com/vendor/core/plugins/payment/css/payment.css?v=1.1.0"
          rel="stylesheet">
      <script src="https://mocthienan.hisotechgroup.com/vendor/core/plugins/payment/js/payment.js?v=1.1.0"></script>
      <form method="POST"
          action="{{route('deposit.store')}}">
          @csrf
          <div class="row" id="main-checkout-product-info">
              <div class="col-lg-5 col-md-6 order-1 order-md-2">
                  <div class="d-block d-sm-none">
                      <div class="checkout-logo">
                          <a href="https://mocthienan.hisotechgroup.com" title="Unigreen - Linh Chi">
                            <img src="https://mocthienan.hisotechgroup.com/storage/logo/4722277-2383582-4.png" alt="Unigreen - Linh Chi">
                          </a>
                          </div>
                          <hr class="border-dark-subtle">
                          </div>
                          <div class="my-3 bg-light">
                            <div class="position-relative p-3" id="cart-item">
                              <div class="bg-light py-2">
                                <p class="font-weight-bold mb-0">{{trans('core/base::layouts.your-info')}}:</p>
                              </div>
                              <div class="checkout-products-marketplace shipping-method-wrapper">
                                <div class="mt-3 bg-light mb-3">
                                  <div class="p-2" style="background: antiquewhite;">
                                    <img class="img-fluid rounded" src="https://mocthienan.hisotechgroup.com/storage/logo/favicon.png"
                                      alt="Unigreen - Linh Chi" style="max-width: 30px; margin-inline-end: 3px;">
                                    <span class="font-weight-bold">Unigreen - Linh Chi</span>
                                  </div>
                                  <div class="py-3">
                                      <div class="row cart-item">
                                          <div class="col-3">
                                              <div class="checkout-product-img-wrapper">
                                                  <img class="item-thumb img-thumbnail img-rounded"
                                                      src="https://mocthienan.hisotechgroup.com/storage/san-pham/sinh-khi-pqa-77-150x150.jpg"
                                                      alt="Nạp tiền vào ví">
                                              </div>
                                          </div>
                                          <div class="col ">
                                              <p class="mb-0">
                                                  {{trans('core/base::layouts.deposit-description')}}
                                              </p>
                                          </div>
                                          <div class="col-auto text-end">
                                              <p id="deposit">{{format_price(0)}}</p>
                                          </div>
                                      </div>
                                  </div>
                                  <hr class="border-dark-subtle">
                              </div>
                          </div>
                          <div class="mt-2 p-2">
                              <div class="row">
                                  <div class="col-6">
                                      <p>{{trans('core/base::layouts.subtotal')}}:</p>
                                  </div>
                                  <div class="col-6">
                                      <p id="deposit-notional" class="price-text sub-total-text text-end" data-deposit-notional="">{{format_price(0)}}</p>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-6">
                                      <p>{{trans('core/base::layouts.fee')}}:</p>
                                  </div>
                                  <div class="col-6">
                                      <p id="free" class="price-text sub-total-text text-end" data-free="0">{{format_price(0)}}</p>
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="col-6">
                                      <p><strong>{{trans('core/base::layouts.total')}}</strong>:</p>
                                  </div>
                                  <div class="col-6">
                                      <p id="total_amount" class="total-text raw-total-text text-end">{{format_price(0)}}</p>

                                  </div>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>
              <div class="form-checkout col-lg-7 col-md-6">
                  <div class="mb-4">
                      <h5 class="checkout-shipping-information-title">{{trans('core/base::layouts.your-info')}}</h5>
                      <div class="customer-address-payment-form">
                          <div class="mb-3 form-group">
                              <p>{{trans('core/base::layouts.account')}}: <strong>{{$customer->name}}</strong> - {{$customer->email}} (<a
                                      href="https://mocthienan.hisotechgroup.com/logout">{{trans('core/base::layouts.log-out')}})</a></p>
                          </div>
                      </div>
                  </div>
                  @foreach ($currencies as $currency_item)
                      <textarea hidden id="{{$currency_item->title}}" cols="30" rows="10">{{$currency_item}}</textarea>
                      {{-- <input type="text" id="{{$currency_item->title}}" value="{{$currency_item->}}"> --}}
                  @endforeach

                  <h5 class="checkout-shipping-information-title">Số tiền nạp</h5>
                  <input class="form-control mb-4" name="amount" id="amount" type="number" value="{{old('amount') ?? 0 }}">
                  <div data-bb-toggle="checkout-payment-methods-area">
                      <input id="currency-selected" name="currency" hidden type=""
                          value="{{$currency}}">
                      <div class="position-relative mb-4">
                          <div class="payment-info-loading loading-spinner" style="display: none"></div>
                          <h5 class="checkout-payment-title">{{trans('core/base::layouts.payment-method')}}</h5>
                          <ul class="list-group list_payment_method">
                              <li class="list-group-item payment-method-item">
                                  <input class="magic-radio js_payment_method" id="payment-cod" name="payment_method"
                                      type="radio" value="bank" checked="">
                                  <label for="payment-cod" class="form-label fw-medium">
                                      Chuyển khoản ngân hàng
                                  </label>

                                  <div class="payment-method-logo">
                                      <img src="https://mocthienan.hisotechgroup.com/storage/payments/cod.png"
                                          data-bb-lazy="true" loading="lazy">
                                  </div>
                              </li>

                          </ul>
                      </div>

                      <input hidden="" class="" type="" id="use_wallet_2" value="0">
                  </div>
                  <div class="form-group mb-3">
                      <label class="form-label" for="description">
                          {{trans('core/base::layouts.note')}}
                      </label>
                      <textarea class="form-control" rows="3"
                          placeholder="{{trans('core/base::layouts.note')}}"
                          id="description" name="description" cols="50">{{old('description')}}</textarea>
                  </div>
                  <label class="form-check">
                      <input type="checkbox" id="agree_terms_and_policy" name="agree_terms_and_policy"
                          class="form-check-input" value="1">

                      <span class="form-check-label">
                          {{trans('core/base::layouts.im-do-with-procie')}} <a href="https://mocthienan.hisotechgroup.com/privacy-policy"
                              class="text-decoration-underline" target="_blank" rel="noreferrer noopener">{{trans('core/base::layouts.privacy-policy')}}</a>
                      </span>
                  </label>
                  <div class="w-100 row align-items-center g-3 mb-5">
                      <div class="order-2 order-md-1 col-md-6 text-center text-md-start mb-4 mb-md-0">
                          <a class="d-flex align-items-center gap-1" href="https://mocthienan.hisotechgroup.com/cart">
                              <svg class="icon  svg-icon-ti-ti-arrow-narrow-left" xmlns="http://www.w3.org/2000/svg"
                                  width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                  <path d="M5 12l14 0"></path>
                                  <path d="M5 12l4 4"></path>
                                  <path d="M5 12l4 -4"></path>
                              </svg> <span class="d-inline-block back-to-cart">{{trans('core/base::layouts.back')}}</span>
                          </a>
                      </div>
                      <div class="order-1 order-md-2 col-md-6">
                          <button id="btn_checkout"
                              class="btn payment-checkout-btn payment-checkout-btn-step float-end"
                              data-processing-text="Xử lý. Vui lòng chờ..." data-error-header="Lỗi" type="button">
                              {{trans('core/base::layouts.checkout')}}
                          </button>
                      </div>
                  </div>
              </div>
          </div>
      </form>
  </div>
@endsection
@push('footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const currencySelected = document.getElementById('currency-selected');
        const deposit = document.getElementById('deposit');
        const depositNotional = document.getElementById('deposit-notional');
        const free = document.getElementById('free');
        const totalAmount = document.getElementById('total_amount');

        function formatMoney(amount, currencyConfig) {
            amount = parseFloat(amount);
            if (isNaN(amount)) return '';

            let options = {
                minimumFractionDigits: currencyConfig.decimals,
                maximumFractionDigits: currencyConfig.decimals,
            };

            let formatted = amount.toLocaleString('en-US', options);

            if (currencyConfig.is_prefix_symbol) {
                return currencyConfig.symbol + ' ' + formatted;
            } else {
                return formatted + ' ' + currencyConfig.symbol;
            }
        }

        function parseNumber(str) {
            if (!str) return 0;
            // Remove all commas or spaces
            return parseFloat(str.replace(/,/g, '').replace(/\s/g, '')) || 0;
        }

        function updateDisplay() {
            const amountValue = amountInput.value.trim();
            const currencyCode = currencySelected.value;

            if (!amountValue) {
                deposit.textContent = '';
                depositNotional.textContent = '';
                totalAmount.textContent = '';
                return;
            }

            const currencyTextarea = document.getElementById(currencyCode);
            if (!currencyTextarea) {
                console.error('Currency config not found for:', currencyCode);
                return;
            }

            const currencyConfig = JSON.parse(currencyTextarea.value);

            const formattedMoney = formatMoney(amountValue, currencyConfig);

            // Update deposit and deposit-notional
            deposit.textContent = formattedMoney;
            depositNotional.textContent = formattedMoney;

            // Gán data-deposit-notional để dễ tính toán
            depositNotional.setAttribute('data-deposit-notional', amountValue);

            // Lấy giá trị hiện tại của free
            const freeValue = parseFloat(free.getAttribute('data-free')) || 0;

            // Tổng cộng
            const total = parseFloat(amountValue) + freeValue;

            totalAmount.textContent = formatMoney(total, currencyConfig);
        }

        amountInput.addEventListener('input', updateDisplay);

        updateDisplay();
    });

</script>
@endpush
