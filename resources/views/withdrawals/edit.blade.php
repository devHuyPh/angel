@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Chi tiết rút tiền'))

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
  <a href="{{ route('withdrawals.index') }}" class="back-btn text-success">
    <i class="bi bi-chevron-left"></i>
  </a>
  <h1 class="header-title text-success ms-2 mb-0">{{ __('Chi tiết rút tiền') }}</h1>
</div>

<div class="container my-3 my-md-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-white border-0">
          <h4 class="card-title mb-1">{{ __('Thông tin rút tiền') }}</h4>
          <p class="text-muted mb-0">{{ trans('core/base::layouts.balance') }}: {{ format_price($customer->walet_1) }}</p>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ trans('core/base::layouts.amount') }}</label>
              <input disabled readonly class="form-control" value="{{ format_price($CustomerWithdrawal->amount) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ trans('core/base::layouts.currency') }}</label>
              <input disabled readonly class="form-control" value="{{ $CustomerWithdrawal->currency }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ trans('core/base::layouts.transaction_id') }}</label>
              <input disabled readonly class="form-control" value="{{ $CustomerWithdrawal->transaction_id }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ trans('core/base::layouts.withdrawal_method') }}</label>
              <input disabled readonly class="form-control" value="{{ $CustomerWithdrawal->withdrawal_method }}">
            </div>
            <div class="col-12">
              <label class="form-label">{{ trans('core/base::layouts.note') }}</label>
              <textarea disabled readonly class="form-control" rows="3">{{ $CustomerWithdrawal->notes }}</textarea>
            </div>
          </div>

          <fieldset class="form-fieldset mt-4">
            <h5 class="mb-3">{{ trans('core/base::layouts.bank_account') }}</h5>
            <div class="datagrid">
              <div class="datagrid-item">
                <div class="datagrid-title">{{ trans('core/base::layouts.bank_name') }}</div>
                <div class="datagrid-content">{{ $CustomerWithdrawal->bank_name }}</div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">{{ trans('core/base::layouts.bank_code') }}</div>
                <div class="datagrid-content">{{ $CustomerWithdrawal->bank_code }}</div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">{{ trans('core/base::layouts.account_holder') }}</div>
                <div class="datagrid-content">{{ $CustomerWithdrawal->account_name }}</div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">{{ trans('core/base::layouts.account_number') }}</div>
                <div class="datagrid-content">{{ $CustomerWithdrawal->account_number }}</div>
              </div>
            </div>
          </fieldset>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header bg-white border-0">
          <h5 class="card-title mb-0">{{ trans('core/base::layouts.status') }}</h5>
        </div>
        <div class="card-body">
          @php
            $statusClasses = [
                'pending' => 'badge bg-warning',
                'completed' => 'badge bg-success',
                'rejected' => 'badge bg-danger',
                'cancelled' => 'badge bg-danger'
            ];
          @endphp
          <span class="{{ $statusClasses[$CustomerWithdrawal->status] ?? 'badge bg-secondary' }}">
            {{ trans('core/base::layouts.' . $CustomerWithdrawal->status) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
