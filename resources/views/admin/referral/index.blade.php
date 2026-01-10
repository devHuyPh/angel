{{-- resources/views/admin/referral-commission/index.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<div class="container-fluid p-0">

    {{-- HEADER GIỐNG ẢNH: TIÊU ĐỀ + NÚT SỬA BÊN PHẢI --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                {{ trans('core/base::layouts.commission_percentage_list') }}
            </h4>

            <a href="{{ route('referralcommission.edit') }}"
               class="btn btn-primary btn-sm d-inline-flex align-items-center">
                <i class="fas fa-edit me-1"></i>
                <span>{{ trans('core/base::layouts.edit') }}</span>
            </a>
        </div>
    </div>

    {{-- TABLE FULL WIDTH --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th class="px-4 py-3 text-uppercase small fw-semibold">
                                    <i class="fas fa-key me-2"></i> Thông số
                                </th>
                                {{-- thêm class commission-value-col --}}
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-end commission-value-col">
                                    <i class="fas fa-coins me-2"></i> Giá trị hiện tại
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                        {{-- Dòng 1: Hoa hồng trực tiếp --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success bg-gradient text-white me-3"
                                         style="width: 48px; height: 48px;">
                                        <i class="fas fa-user-plus fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ trans('core/base::layouts.direct_referral_commission') }}
                                        </div>
                                        <div class="text-muted small">
                                            Hoa hồng F1 - Giới thiệu trực tiếp
                                        </div>
                                    </div>
                                </div>
                            </td>
                            {{-- thêm class commission-value-col --}}
                            <td class="px-4 py-3 text-end commission-value-col">
                                <span class="h2 fw-bold text-success mb-0">
                                    {{ $direct }}<span class="h5">%</span>
                                </span>
                            </td>
                        </tr>

                        {{-- Dòng 2: Hoa hồng gián tiếp --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white me-3"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg,#6f42c1,#d63384);">
                                        <i class="fas fa-users fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ trans('core/base::layouts.indirect_referral_commission') }}
                                        </div>
                                        <div class="text-muted small">
                                            Hoa hồng F2, F3... - Giới thiệu gián tiếp
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end commission-value-col">
                                <span class="h2 fw-bold mb-0" style="color:#6f42c1;">
                                    {{ $indirect }}<span class="h5">%</span>
                                </span>
                            </td>
                        </tr>

                        {{-- Phí ví --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white me-3"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg,#fbbf24,#d97706);">
                                        <i class="fas fa-wallet fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ trans('core/base::layouts.wallet_fee') }}
                                        </div>
                                        <div class="text-muted small">
                                            Phí nạp/rút tiền ví điện tử
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end commission-value-col">
                                <span class="h2 fw-bold mb-0" style="color:#d97706;">
                                    {{ $wallet_fee }}<span class="h5">%</span>
                                </span>
                            </td>
                        </tr>

                        {{-- Phí rút cố định --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white me-3"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg,#0ea5e9,#0284c7);">
                                        <i class="fas fa-receipt fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ trans('core/base::layouts.fixed_withdrawal_fee') }}
                                        </div>
                                        <div class="text-muted small">
                                            Phí rút cố định áp dụng cho mỗi giao dịch
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end commission-value-col">
                                <span class="h3 fw-bold mb-0" style="color:#0284c7;">
                                    {{ format_price($fixed_fees) }}
                                </span>
                            </td>
                        </tr>

                        {{-- Phí giao hàng --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white me-3"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg,#fb7185,#ef4444);">
                                        <i class="fas fa-truck fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ trans('core/base::layouts.fee_dis_ware_to_customer') }}
                                        </div>
                                        <div class="text-muted small">
                                            Phí vận chuyển từ kho phân phối → khách hàng
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end commission-value-col">
                                <span class="h2 fw-bold mb-0" style="color:#e11d48;">
                                    {{ $fee_dis_ware_to_customer }}<span class="h5">%</span>
                                </span>
                            </td>
                        </tr>

                        {{-- Mua lại hàng tháng --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white me-3"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg,#22d3ee,#2563eb);">
                                        <i class="fas fa-shopping-cart fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ trans('core/base::layouts.monthly_repurchase') }}
                                        </div>
                                        <div class="text-muted small">
                                            Doanh số duy trì hàng tháng bắt buộc
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end commission-value-col">
                                <span class="h3 fw-bold mb-0" style="color:#0ea5e9;">
                                    {{ format_price($monthly_repurchase) }}
                                </span>
                            </td>
                        </tr>

{{-- MỚI: Thời gian tự động xác nhận đơn --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white me-3"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg,#0ea5e9,#2563eb);">
                                        <i class="fas fa-clock fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{-- thêm key lang nếu có --}}
                                            {{ trans('core/base::layouts.auto_confirmation_time') ?? 'Thời gian tự động xác nhận' }}
                                        </div>
                                        <div class="text-muted small">
                                            Sau khoảng thời gian này, hệ thống tự động xác nhận đơn hàng
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end commission-value-col">
                                {{-- tuỳ bạn đang lưu theo giờ/phút mà đổi text phía sau --}}
                                <span class="h4 fw-bold mb-0" style="color:#2563eb;">
                                    {{ $auto_confirmation_time }}
                                </span>
                            </td>
                        </tr>

                        {{-- MỚI: Số dư ví tối thiểu để dùng ví --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white me-3"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg,#0ea5e9,#2563eb);">
                                        <i class="fas fa-wallet fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            Số dư ví tối thiểu để sử dụng ví
                                        </div>
                                        <div class="text-muted small">
                                            Nếu số dư ví < ngưỡng này, chỉ cho phép chuyển khoản.
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end commission-value-col">
                                <span class="h3 fw-bold mb-0" style="color:#0ea5e9;">
                                    {{ format_price($wallet_min_amount) }}
                                </span>
                            </td>
                        </tr>

                        {{-- MỚI: Số tiền rút tối thiểu / khách hàng --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white me-3"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg,#10b981,#047857);">
                                        <i class="fas fa-hand-holding-usd fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ trans('core/base::layouts.minimum_withdrawal_amount_per_customer') ?? 'Số tiền rút tối thiểu / khách hàng' }}
                                        </div>
                                        <div class="text-muted small">
                                            Ngưỡng số dư tối thiểu để khách có thể tạo yêu cầu rút
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end commission-value-col">
                                <span class="h3 fw-bold mb-0" style="color:#047857;">
                                    {{ format_price($minimum_withdrawal_amount_per_customer) }}
                                </span>
                            </td>
                        </tr>

                        {{-- Hoa hồng kho --}}
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white me-3"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg,#4f46e5,#1d4ed8);">
                                        <i class="fas fa-warehouse fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ trans('core/base::layouts.warehouse-referral-commission') }}
                                        </div>
                                        <div class="text-muted small">
                                            Hoa hồng dành cho chủ kho
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end commission-value-col">
                                <span class="h2 fw-bold mb-0" style="color:#4338ca;">
                                    {{ $warehouse }}<span class="h5">%</span>
                                </span>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>

                {{-- FOOTER NOTE --}}
                <div class="card-footer bg-light border-top">
                    <div class="d-flex align-items-center justify-content-center text-muted small">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        <span>
                            Tất cả giá trị được áp dụng tự động cho toàn hệ thống • Cập nhật tức thì sau khi chỉnh sửa
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('footer')
<style>
    .table-hover tbody tr:hover td {
        background-color: #f5f7fb !important;
    }

    /* Đẩy giá trị sang trái 1 chút bằng cách tăng padding-right */
    .commission-value-col {
        padding-right: 2.5rem !important; /* chỉnh số này nếu muốn xa/gần hơn */
    }
</style>
@endpush
