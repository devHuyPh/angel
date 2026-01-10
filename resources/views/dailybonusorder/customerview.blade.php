@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<div class="container-wrapper">
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ trans('core/base::layouts.customer_details') }}</h4>
            
        </div>

        <div class="card-body">
            <!-- Thông tin khách hàng -->
            <div class="mb-5">
                <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.customer_information') }}</h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-striped info-table">
                            <tr>
                                <th>{{ trans('core/base::layouts.name') }}</th>
                                <td>{{ $customer->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.email') }}</th>
                                <td>{{ $customer->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.phone') }}</th>
                                <td>{{ $customer->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.status') }}</th>
                                <td>
                                    <span class="badge {{ $customer->status == 'activated' ? 'bg-success' : 'bg-danger' }} text-white">
                                        {{ ucfirst($customer->status ?? 'N/A') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.kyc_status') }}</th>
                                <td>
                                    <span class="badge {{ $customer->kyc_status ? 'bg-success' : 'bg-warning' }} text-white">
                                        {{ $customer->kyc_status ? 'Verified' : 'Not Verified' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.active_account') }}</th>
                                <td>
                                    <span class="badge {{ $customer->is_active_account ? 'bg-success' : 'bg-danger' }} text-white">
                                        {{ $customer->is_active_account ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.walet_1') }}</th>
                                <td>{{ number_format($customer->walet_1, 2) ?? '0.00' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.walet_2') }}</th>
                                <td>{{ number_format($customer->walet_2, 2) ?? '0.00' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.total_dowline') }}</th>
                                <td>{{ number_format($customer->total_dowline, 0) ?? '0' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.referrer') }}</th>
                                <td>
                                @if($customer->referrer)
                                        <a href="{{ route('dailybonusorder.customerview', ['id' => $customer->referrer->id]) }}">
                                            {{ $customer->referrer->name ?? 'N/A' }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.created_at') }}</th>
                                <td>{{ $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.updated_at') }}</th>
                                <td>{{ $customer->updated_at ? $customer->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 text-center">
                        <h4 class="mb-3">{{ trans('core/base::layouts.avatar') }}</h4>
                        <div class="avatar-wrapper">
                            @if($customer->avatar)
                                <img src="{{ asset('storage/' . $customer->avatar) }}" alt="{{ $customer->name }}" class="avatar-img">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .container-wrapper {
        padding: 20px;
        background-color: #f5f7fa;
        min-height: 100vh;
    }

    .card {
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .card-body {
        padding: 30px;
    }

    /* Thiết kế lại nút Back to List */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .btn-primary svg {
        margin-right: 5px;
    }

    /* Tùy chỉnh bảng thông tin */
    .info-table {
        font-size: 14px;
        border-collapse: separate;
        border-spacing: 0;
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }

    .info-table th {
        background-color: #f1f5f9;
        font-weight: 600;
        text-transform: capitalize;
        padding: 12px 15px;
        border-bottom: 1px solid #e2e8f0;
        width: 30%;
        color: #1f2937;
    }

    .info-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #374151;
    }

    .info-table tbody tr:last-child th,
    .info-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Tùy chỉnh avatar */
    .avatar-wrapper {
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .avatar-img:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background-color: #e2e8f0;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 80px;
        font-weight: 600;
        border: 3px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .avatar-placeholder:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }

    /* Tùy chỉnh badge trạng thái */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
        transition: all 0.3s ease;
    }

    .badge.bg-success {
        background-color: #10b981 !important;
    }

    .badge.bg-warning {
        background-color: #f59e0b !important;
        color: #fff !important;
    }

    .badge.bg-danger {
        background-color: #ef4444 !important;
    }

    /* Style cho liên kết Người giới thiệu */
    .info-table a {
        color: #007bff;
        text-decoration: none;
    }

    .info-table a:hover {
        text-decoration: underline;
        color: #0056b3;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container-wrapper {
            padding: 10px;
        }

        .card-header {
            padding: 15px;
        }

        .card-body {
            padding: 20px;
        }

        .info-table th,
        .info-table td {
            font-size: 12px;
            padding: 8px 10px;
        }

        .btn-primary {
            padding: 6px 12px;
            font-size: 12px;
        }

        .avatar-wrapper {
            width: 120px;
            height: 120px;
        }

        .avatar-placeholder {
            font-size: 48px;
        }
    }
</style>
@endsection