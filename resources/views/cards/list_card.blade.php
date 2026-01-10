@extends(EcommerceHelper::viewPath('customers.master'))


@section('title', __('Quản lý thẻ'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center g-4">
            @foreach ($cards as $card)
                    <div class="col-md-6 col-lg-6">
                        <div class="card-item" style="
                  position: relative;
                  width: 100%;
                  height:100%;
                  border-radius: 16px;
                  color: white;
                  overflow: hidden;
                  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                  background:
                  url('{{ $card->image ? asset('storage/' . $card->image) : '' }}') no-repeat center/cover;
                  background-color: {{ $card->image ? 'transparent' : '#1e3c72' }};
                  padding: 24px;
                  display: flex;
                  flex-direction: column;
                  justify-content: space-between;
                  ">
                            <div class="overlay" style="
                  position: absolute;
                  top: 0; left: 0; right: 0; bottom: 0;
                  background: rgba(0,0,0,0.4);
                  z-index: 0;
                  "></div>



                            <div style="position: relative; z-index: 1;">
                                <div class="card-title" style="font-size: 18px; font-weight: 600; letter-spacing: 0.5px;">
                                    {{ $card->name }}
                                </div>

                                <div class="card-number"
                                    style="font-size: 24px; font-weight: 700; letter-spacing: 3px; margin: 20px 0;">
                                    {{ $card->number }}
                                </div>

                                <div class="cashback" style="
                  background: rgba(255, 255, 255, 0.2);
                  padding: 6px 12px;
                  border-radius: 20px;
                  font-size: 14px;
                  font-weight: 600;
                  backdrop-filter: blur(10px);
                  border: 1px solid rgba(255, 255, 255, 0.3);
                  display: inline-block;
                  margin-bottom: 10px;
                  ">
                                    {{ $card->cashback }}% {{ __('core/base::layouts.cashback') }}
                                </div>

                                <div class="card-footer"
                                    style="display: flex; justify-content: space-between; align-items: flex-end;">
                                    <div class="card-info" style="display: flex; flex-direction: column; gap: 4px;">
                                        <div class="amount" style="font-size: 14px; font-weight: 600;">
                                            {{ __('core/base::layouts.value_card') }}<br>{{ format_price($card->value) }}
                                        </div>
                                        <div class="daily-gift" style="font-size: 12px; opacity: 0.9;">
                                            {{ __('core/base::layouts.gift_description') }}<br>{{ format_price($card->value * ($card->cashback / 100)) }}
                                            /{{ __('core/base::layouts.day') }}
                                        </div>
                                    </div>
                                    <div class="expiry-info" style="text-align: right;">
                                        <div class="cardholder" style="font-size: 12px; opacity: 0.8; margin-bottom: 4px;">
                                            {{ __('core/base::layouts.expiration_date') }}
                                        </div>
                                        <div class="expiry" style="font-size: 16px; font-weight: 600;">
                                            {{ \Carbon\Carbon::parse($card->expiration_date)->format('m/y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            @endforeach
        </div>
    </div>
@endsection
