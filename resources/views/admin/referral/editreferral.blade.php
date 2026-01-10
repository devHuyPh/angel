{{-- resources/views/admin/referral-commission/edit.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<div class="container-fluid p-0">

    {{-- HEADER giống list: tiêu đề + back --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold">
                {{ trans('core/base::layouts.edit_commission_percentage') }}
            </h4>

            <a href="{{ route('referralcommission.index') }}"
               class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-1"></i>
                <span>{{ trans('core/base::layouts.back') ?? __('Quay lại') }}</span>
            </a>
        </div>
    </div>

    {{-- FORM --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <form method="POST" action="{{ route('referralcommission.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Hoa hồng trực tiếp --}}
                            <div class="col-md-6 mb-3">
                                <label for="direct" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.direct_referral_commission') }} (%)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('direct') is-invalid @enderror"
                                    id="direct"
                                    name="direct"
                                    value="{{ old('direct', $direct) }}"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    required
                                >
                                @error('direct')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hoa hồng gián tiếp --}}
                            <div class="col-md-6 mb-3">
                                <label for="indirect" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.indirect_referral_commission') }} (%)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('indirect') is-invalid @enderror"
                                    id="indirect"
                                    name="indirect"
                                    value="{{ old('indirect', $indirect) }}"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    required
                                >
                                @error('indirect')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phí ví --}}
                            <div class="col-md-6 mb-3">
                                <label for="wallet_fee" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.wallet_fee_referral_commission') }} (%)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('wallet_fee') is-invalid @enderror"
                                    id="wallet_fee"
                                    name="wallet_fee"
                                    value="{{ old('wallet_fee', $wallet_fee) }}"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    required
                                >
                                @error('wallet_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phí rút cố định --}}
                            <div class="col-md-6 mb-3">
                                <label for="fixed_fees" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.fixed_withdrawal_fee') }} (đ)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('fixed_fees') is-invalid @enderror"
                                    id="fixed_fees"
                                    name="fixed_fees"
                                    value="{{ old('fixed_fees', $fixed_fees) }}"
                                    min="0"
                                    step="1"
                                    required
                                >
                                @error('fixed_fees')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phí kho phân phối -> khách --}}
                            <div class="col-md-6 mb-3">
                                <label for="fee_dis_ware_to_customer" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.fee_dis_ware_to_customer') }} (%)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('fee_dis_ware_to_customer') is-invalid @enderror"
                                    id="fee_dis_ware_to_customer"
                                    name="fee_dis_ware_to_customer"
                                    value="{{ old('fee_dis_ware_to_customer', $fee_dis_ware_to_customer) }}"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    required
                                >
                                @error('fee_dis_ware_to_customer')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hoa hồng kho --}}
                            <div class="col-md-6 mb-3">
                                <label for="warehouse" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.warehouse-referral-commission') }} (%)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('warehouse') is-invalid @enderror"
                                    id="warehouse"
                                    name="warehouse"
                                    value="{{ old('warehouse', $warehouse) }}"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    required
                                >
                                @error('warehouse')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Doanh số mua lại hàng tháng --}}
                            <div class="col-md-6 mb-3">
                                <label for="monthly_repurchase" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.monthly_repurchase') }} (đ)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('monthly_repurchase') is-invalid @enderror"
                                    id="monthly_repurchase"
                                    name="monthly_repurchase"
                                    value="{{ old('monthly_repurchase', $monthly_repurchase) }}"
                                    min="0"
                                    required
                                >
                                @error('monthly_repurchase')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Thời gian tự động xác nhận đơn --}}
                            <div class="col-md-6 mb-3">
                                <label for="auto_confirmation_time" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.auto_confirmation_time') }} (giờ)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('auto_confirmation_time') is-invalid @enderror"
                                    id="auto_confirmation_time"
                                    name="auto_confirmation_time"
                                    value="{{ old('auto_confirmation_time', $auto_confirmation_time) }}"
                                    min="0"
                                    step="1"
                                    required
                                >
                                @error('auto_confirmation_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Số tiền rút tối thiểu / khách hàng --}}
                            <div class="col-md-6 mb-3">
                                <label for="minimum_withdrawal_amount_per_customer" class="form-label fw-semibold">
                                    {{ trans('core/base::layouts.minimum_withdrawal_amount_per_customer') }} (đ)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('minimum_withdrawal_amount_per_customer') is-invalid @enderror"
                                    id="minimum_withdrawal_amount_per_customer"
                                    name="minimum_withdrawal_amount_per_customer"
                                    value="{{ old('minimum_withdrawal_amount_per_customer', $minimum_withdrawal_amount_per_customer) }}"
                                    min="0"
                                    required
                                >
                                @error('minimum_withdrawal_amount_per_customer')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Số dư ví tối thiểu để được phép dùng ví --}}
                            <div class="col-md-6 mb-3">
                                <label for="wallet_min_amount" class="form-label fw-semibold">
                                    Số dư ví tối thiểu để sử dụng ví (đ)
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('wallet_min_amount') is-invalid @enderror"
                                    id="wallet_min_amount"
                                    name="wallet_min_amount"
                                    value="{{ old('wallet_min_amount', $wallet_min_amount) }}"
                                    min="0"
                                    step="1"
                                    placeholder="Ví dụ: 100000"
                                >
                                @error('wallet_min_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <a href="{{ route('referralcommission.index') }}" class="btn btn-outline-secondary">
                                {{ trans('core/base::layouts.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ trans('core/base::layouts.save') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('style-lib')
<link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
@endpush

@push('style')
<style>
    .card {
        border-radius: 0.5rem;
        background-color: #ffffff;
    }

    .form-control {
        border-radius: 5px;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .btn {
        border-radius: 5px;
    }

    .commission-note {
        display: block;
        margin-top: -0.5rem;
        margin-bottom: 1.5rem;
        font-size: 0.9em;
        color: #6c757d;
        font-style: italic;
        padding-left: 0.5rem;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
@endpush
