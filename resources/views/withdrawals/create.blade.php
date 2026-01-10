@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Overview'))

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

  .fee-list small {
    display: block;
    line-height: 1.5;
  }
</style>

<div class="header d-flex d-md-none align-items-center mb-3 bg-white px-3 py-2"
  style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
  <a href="{{ route('withdrawals.index') }}" class="back-btn text-success">
    <i class="bi bi-chevron-left"></i>
  </a>
  <h1 class="header-title text-success ms-2 mb-0">{{ __('Tạo lệnh rút tiền') }}</h1>
</div>

<div id="app">
  <div class="container">
    <form id="botble-marketplace-forms-vendor-withdrawal-form" method="POST" action="{{ route('withdrawals.store') }}"
      accept-charset="UTF-8" class="js-base-form dirty-check" novalidate="novalidate">
      @csrf @method('POST')

      <div class="row">
        <div class="col-lg-8 mb-4">
          <div class="card shadow-sm">
            <div class="card-header bg-white border-0 pb-0">
              <h4 class="card-title">{{ trans('core/base::layouts.add_new_withdrawal') }}</h4>
              <p class="text-muted mb-0">
                {{ trans('core/base::layouts.balance') }}: {{ format_price($customer->walet_1) }}
              </p>
            </div>
            <div class="card-body">
              <div class="form-body">
                <div class="mb-3">
                  <label class="form-label required" for="amount">
                    {{ trans('core/base::layouts.amount') }}
                  </label>
                  <input id="amount" class="form-control" data-counter="120" max="{{ $customer->walet_1 }}" min="0"
                    name="amount" type="number" aria-required="true" placeholder="Nhập số tiền muốn rút">

                  <input class="form-control" data-counter="120" hidden
                    value="{{ $customer->id ?? old('customer_id') }}" name="customer_id">
                  <input class="form-control" data-counter="120" hidden value="{{ $currency }}" name="currency">
                </div>

                <div class="mb-3">
                  <label class="form-label" for="description">
                    {{ trans('core/base::layouts.note') }}
                  </label>
                  <textarea id="description" class="form-control" data-counter="200" rows="3" name="description"
                    cols="50" placeholder="{{ trans('core/base::layouts.note') }}"></textarea>
                </div>

                <div class="mt-3">
                  <div class="alert alert-light border fee-list">
                    <strong class="d-block mb-2 text-dark">{{ trans('core/base::layouts.withdrawal_fees') }}</strong>
                    <small>
                      {{ trans('core/base::layouts.Phí rút tiền-sẽ dùng đóng VAT') }}
                      (<span id="fee-percent">{{ $walletFeePercent }}</span>%):
                      <strong id="fee-amount" class="text-danger">0₫</strong>
                    </small>
                    <small>
                      {{ trans('core/base::layouts.fixed_withdrawal_fee') }}:
                      <strong id="fixed-fee-amount" class="text-danger">{{ format_price($fixedFee) }}</strong>
                    </small>
                    <small>
                      {{ trans('core/base::layouts.Số tiền thực nhận:') }}
                      <strong id="real-amount" class="text-success">0₫</strong>
                    </small>
                    <small>
                      {{ trans('core/base::layouts.Tổng trừ từ ví:') }}
                      <strong id="total-deduct" class="text-danger">0₫</strong>
                    </small>
                  </div>
                  <input id="wallet_fee_percent" type="hidden" value="{{ $walletFeePercent }}">
                  <input id="fixed_fee_amount" type="hidden" value="{{ $fixedFee }}">
                </div>

                <div class="d-none mb-3">
                  <label class="form-label required" for="bank_account">
                    {{ trans('core/base::layouts.bank_account') }}
                  </label>
                  @php
                    $defaultBankAccount = $bankAccounts->firstWhere('is_default', true) ?? $bankAccounts->first();
                  @endphp
                  <select id="bank_account" name="bank_account" class="form-control">
                    @if ($bankAccounts->isNotEmpty())
                      @foreach ($bankAccounts as $bankAccount)
                        <option value="{{ $bankAccount->id }}" data-bank-name="{{ $bankAccount->bank_name }}"
                          data-bank-code="{{ $bankAccount->bank_code }}"
                          data-account-holder="{{ $bankAccount->account_holder }}"
                          data-account-number="{{ $bankAccount->account_number }}" @selected($bankAccount->is_default)>
                          {{ $bankAccount->bank_name }}, {{ $bankAccount->account_number }},
                          {{ $bankAccount->account_holder }}
                        </option>
                      @endforeach
                    @endif
                    <option value="new" @if ($bankAccounts->isEmpty()) selected @endif>
                      {{ trans('core/base::layouts.add_new') }}</option>
                  </select>
                </div>

                <fieldset id="bank-info" class="form-fieldset mb-3 d-none">
                  <h4>{{ trans('core/base::layouts.you_will_account') }}</h4>
                  <div class="datagrid">
                    <div class="datagrid-item">
                      <div class="datagrid-title">{{ trans('core/base::layouts.bank_name') }}</div>
                      <div class="datagrid-content" id="bank-name"></div>
                    </div>
                    <div class="datagrid-item">
                      <div class="datagrid-title">{{ trans('core/base::layouts.bank_code') }}</div>
                      <div class="datagrid-content" id="bank-code"></div>
                    </div>
                    <div class="datagrid-item">
                      <div class="datagrid-title">{{ trans('core/base::layouts.account_holder') }}</div>
                      <div class="datagrid-content" id="account-holder"></div>
                    </div>
                    <div class="datagrid-item">
                      <div class="datagrid-title">{{ trans('core/base::layouts.account_number') }}</div>
                      <div class="datagrid-content" id="account-number"></div>
                    </div>
                    @if ($defaultBankAccount)
                      <div class="datagrid-item">
                        <input type="text" hidden value="{{ $defaultBankAccount->bank_name }}" name="bank_name">
                        <input type="text" hidden value="{{ $defaultBankAccount->bank_code }}" name="bank_code">
                        <input type="text" hidden value="{{ $defaultBankAccount->account_number }}"
                          name="account_number">
                        <input type="text" hidden value="{{ $defaultBankAccount->account_holder }}"
                          name="account_holder">
                        <input type="text" hidden value="{{ $defaultBankAccount->branch }}" name="branch">
                        <input type="text" hidden value="{{ $defaultBankAccount->swift_code }}" name="swift_code">
                      </div>
                    @endif
                  </div>
                </fieldset>

                <div id="new-account-form" class="mb-3" style="display: none;">
                  <h4>{{ trans('core/base::layouts.inp_add_new_bank_account') }}</h4>

                  <div class="mb-3">
                    <label class="form-label">{{ trans('core/base::layouts.select_bank') }}<span
                        class="text-danger">*</span></label>
                    <select name="bank_code" id="api_bank_vn" class="form-control">
                      <option value="other">Khác</option>
                    </select>
                  </div>
                  <div>
                    <div class="mb-3">
                      <label class="form-label">{{ trans('core/base::layouts.bank_code') }}<span
                          class="text-danger">*</span></label>
                      <input type="text" name="new_bank_code" id="custom_bank_code" class="form-control"
                        value="{{ old('bank_code') }}" placeholder="Ex: ACB">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">{{ trans('core/base::layouts.bank_name') }}<span
                          class="text-danger">*</span></label>
                      <input type="text" name="new_bank_name" id="bank_name" class="form-control"
                        value="{{ old('bank_name') }}" placeholder="Ex: Ngân hàng Á Châu">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">{{ trans('core/base::layouts.account_holder') }}<span
                        class="text-danger">*</span></label>
                    <input type="text" name="new_account_holder" class="form-control mb-2"
                      placeholder="Ex: NGUYEN VAN A">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">{{ trans('core/base::layouts.account_number') }}<span
                        class="text-danger">*</span></label>
                    <input type="text" name="new_account_number" class="form-control mb-2"
                      placeholder="Ex: 909999999999">
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4 d-flex flex-column gap-3 mb-5">
          <div class="card shadow-sm">
            <div class="card-header bg-white border-0">
              <h4 class="card-title mb-0">{{ trans('core/base::layouts.publish') }}</h4>
            </div>
            <div class="card-body">
              <button class="btn btn-primary w-100" type="submit" name="submit" value="save">
                <svg class="icon icon-left svg-icon-ti-ti-coin" xmlns="http://www.w3.org/2000/svg" width="24"
                  height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                  <path
                    d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1">
                  </path>
                  <path d="M12 7v10"></path>
                </svg>
                {{ trans('core/base::layouts.request') }}
              </button>
            </div>
          </div>

          <div class="card shadow-sm">
            <div class="card-header bg-white border-0">
              <h5 class="card-title mb-0">{{ trans('core/base::layouts.note') }}</h5>
            </div>
            <div class="card-body text-muted">
              <ul class="mb-0 ps-3">
                <li>{{ trans('core/base::layouts.withdrawal_fees') }}</li>
                <li>{{ trans('core/base::layouts.balance') }}: {{ format_price($customer->walet_1) }}</li>
                <li>{{ trans('core/base::layouts.auto_confirmation_time') ?? 'Thời gian xử lý tùy thuộc hệ thống' }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const bankSelect = document.getElementById("bank_account");
    const bankInfo = document.getElementById("bank-info");
    const newAccountForm = document.getElementById("new-account-form");

    const bankName = document.getElementById("bank-name");
    const bankCode = document.getElementById("bank-code");
    const accountHolder = document.getElementById("account-holder");
    const accountNumber = document.getElementById("account-number");

    function updateBankInfo(selectedOption) {
      if (selectedOption.value === "new") {
        bankInfo.style.display = "none";
        newAccountForm.style.display = "block";
      } else {
        bankInfo.style.display = "block";
        newAccountForm.style.display = "none";

        bankName.textContent = selectedOption.getAttribute("data-bank-name");
        bankCode.textContent = selectedOption.getAttribute("data-bank-code");
        accountHolder.textContent = selectedOption.getAttribute("data-account-holder");
        accountNumber.textContent = selectedOption.getAttribute("data-account-number");
      }
    }

    if (bankSelect) {
      let selectedOption = bankSelect.options[bankSelect.selectedIndex];

      if (bankSelect.options.length === 1 || selectedOption.value === "new") {
        newAccountForm.style.display = "block";
      } else {
        updateBankInfo(selectedOption);
      }

      bankSelect.addEventListener("change", function() {
        updateBankInfo(this.options[this.selectedIndex]);
      });
    }
  });

  $(document).ready(function() {
    const bankSelect = $("#api_bank_vn");
    const bankNameInput = $("#bank_name");
    const bankCodeInput = $("#custom_bank_code");

    $.ajax({
      url: "https://api.vietqr.io/v2/banks",
      method: "GET",
      dataType: "json",
      success: function(response) {
        if (response.code === "00" && response.data.length > 0) {
          response.data.forEach(bank => {
            bankSelect.append(new Option(`${bank.name} - ${bank.shortName}`,
              bank.code));
          });
        }
      },
      error: function(error) {
        console.error("Lỗi khi tải danh sách ngân hàng:", error);
      }
    });

    bankSelect.on("change", function() {
      let selectedOption = $(this).val();

      if (selectedOption === "other") {
        bankNameInput.val("").focus();
        bankCodeInput.val("");
      } else {
        let selectedText = $(this).find("option:selected").text().split(" - ");
        bankNameInput.val(selectedText[0]);
        bankCodeInput.val(selectedOption);
      }
    });
  });

  document.getElementById('amount').addEventListener('input', function() {
    const amount = parseFloat(this.value) || 0;
    const walletFeePercent = parseFloat(document.getElementById('wallet_fee_percent').value) || 10;
    const fixedFee = parseFloat(document.getElementById('fixed_fee_amount').value) || 0;

    const percentFee = (walletFeePercent / 100) * amount;
    const fee = percentFee + fixedFee;
    const realAmount = Math.max(amount - fee, 0);
    const total = amount;

    document.getElementById('fee-amount').innerText = percentFee.toLocaleString('vi-VN', {
      style: 'currency',
      currency: 'VND'
    });

    document.getElementById('fixed-fee-amount').innerText = fixedFee.toLocaleString('vi-VN', {
      style: 'currency',
      currency: 'VND'
    });

    document.getElementById('real-amount').innerText = realAmount.toLocaleString('vi-VN', {
      style: 'currency',
      currency: 'VND'
    });

    document.getElementById('total-deduct').innerText = total.toLocaleString('vi-VN', {
      style: 'currency',
      currency: 'VND'
    });
  });
</script>
@endsection
