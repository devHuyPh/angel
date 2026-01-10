@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Cập nhật Sepay'))

@section('content')
<style>
  @media (max-width: 767.98px) {
    .desktop {
      display: none !important;
    }

    .mobile {
      display: block !important;
    }
  }

  .card-title {
    margin-bottom: 0;
  }
</style>

<div class="header d-flex d-md-none align-items-center mb-3 bg-white px-3 py-2"
  style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
  <a href="{{ route('bank_accounts.index') }}" class="back-btn text-success">
    <i class="bi bi-chevron-left"></i>
  </a>
  <h1 class="header-title text-success ms-2 mb-0">{{ __('Cập nhật Sepay') }}</h1>
</div>

<div class="container">
  <form action="{{ route('withdrawals.put-setup-sepay') }}" method="POST" class="row g-4">
    @csrf
    @method('PUT')



    <div class="col-lg-12">
      <div class="card shadow-sm">
        <div class="card-header bg-white border-0">
          <h4 class="card-title mb-1">{{ __('Thông tin ngân hàng') }}</h4>
          <p class="text-muted mb-0">
            {{ trans('core/base::layouts.please_provide_information') }}
            <a href="https://sepay.vn" target="_blank">SePay</a>
          </p>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <input type="hidden" name="id" value="{{ $bankAccount->id }}">

            <div class="col-12">
              <label class="form-label" for="payment_sepay_bank">{{ trans('core/base::layouts.bank') }}</label>
              <select class="form-control" id="payment_sepay_bank" name="payment_sepay_bank">
                <option value="">{{ trans('core/base::layouts.choose_a_bank') }}</option>
                @foreach ($banks as $bank)
                  <option value="{{ $bank['bank_code'] }}" @selected($bankAccount->bank_code == $bank['bank_code'])>
                    {{ $bank['name'] }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="payment_sepay_account_number">
                {{ trans('core/base::layouts.account_number') }}
              </label>
              <input class="form-control" data-counter="250" name="payment_sepay_account_number" type="text"
                value="{{ old('payment_sepay_account_number') ?? $bankAccount->account_number }}"
                id="payment_sepay_account_number" placeholder="123456789">
            </div>

            <div class="col-md-6">
              <label class="form-label" for="payment_sepay_account_holder">
                {{ trans('core/base::layouts.account_owner') }}
              </label>
              <input class="form-control" data-counter="250" name="payment_sepay_account_holder" type="text"
                value="{{ old('payment_sepay_account_holder') ?? $bankAccount->account_holder }}"
                id="payment_sepay_account_holder" placeholder="NGUYEN VAN A">
            </div>


            <div class="col-12">
              <label class="form-check">
                <input type="hidden" name="confirm_bank_account" value="0">
                <input type="checkbox" id="confirm-bank-account" name="confirm_bank_account" class="form-check-input"
                  value="1">
                <span class="form-check-label">
                  {{ trans('core/base::layouts.i_certify_that_i_have_completed_all_steps_and_filled_in_all_information_correctly') }}.
                </span>
              </label>
            </div>

            <div class="col-12">
              <button class="btn btn-primary w-100">{{ trans('core/base::layouts.update') }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </form>
</div>

<script>
  $(document).ready(function() {
    $('#payment_sepay_bank').select2({
      placeholder: '{{ trans('core/base::layouts.choose_a_bank') }}',
      width: '100%'
    });
  });

  function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      const r = Math.random() * 16 | 0;
      const v = c === 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }

  document.getElementById('create-sepay-webhook-secret').addEventListener('click', function() {
    const input = document.getElementById('sepay-webhook-secret');
    const input2 = document.getElementById('sepay-webhook-secret-send');

    const newKey = generateUUID();
    input.value = input2.value = newKey;
  });
</script>
@endsection

@push('style-lib')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link media="all" type="text/css" rel="stylesheet"
  href="{{ url('/') }}/vendor/core/core/base/libraries/select2/css/select2.min.css?v=...">
@endpush
