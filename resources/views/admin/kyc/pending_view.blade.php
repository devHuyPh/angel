@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<div class="container-wrapper">
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ trans('core/base::layouts.kyc_request_details')}}</h4>
            <a href="{{ route('kyc.pending') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
                {{ trans('core/base::layouts.back_to_list')}}
            </a>
        </div>

        <div class="card-body">
            <!-- Thông tin cơ bản -->
            <div class="mb-5">
                <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.basic_information')}}</h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-striped info-table">
                            <tr>
                                <th>{{ trans('core/base::layouts.kyc_form_id')}}</th>
                                <td>{{ $pending->kyc_form_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.name')}}</th>
                                <td>{{ $pending->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.verification_type')}}</th>
                                <td>{{ $pending->verification_type }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.status')}}</th>
                                <td>
                                    <span class="badge {{ $pending->status == 'approved' ? 'bg-success' : ($pending->status == 'pending' ? 'bg-warning' : 'bg-danger') }} text-white">
                                        {{ ucfirst($pending->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.created_at')}}</th>
                                <td>{{ $pending->created_at ? $pending->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('core/base::layouts.updated_at')}}</th>
                                <td>{{ $pending->updated_at ? $pending->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 text-center">
                        <h4 class="mb-3">{{ trans('core/base::layouts.avatar')}}</h4>
                        <div class="avatar-wrapper">
                            @if($pending->customer && $pending->customer->avatar)
                                <img src="{{ asset('storage/' . $pending->customer->avatar) }}" alt="{{ $pending->name }}" class="avatar-img">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($pending->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dữ liệu động -->
            <div class="mb-5">
                <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.kyc_data')}}</h5>
                @php
                    $data = is_string($pending->data) ? json_decode($pending->data, true) : $pending->data;
                    // Map key không dấu với tên có dấu
                    $labelMap = array_combine($fieldNames, $fieldLabels);
                @endphp
                @if(is_array($data) && !empty($data))
                    <table class="table table-bordered table-striped info-table">
                        @foreach($data as $key => $value)
                            <tr>
                                <th class="data-key">{{ $labelMap[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</th>
                                <td class="data-value">
                                    @php
                                        // Danh sách các đuôi file ảnh hợp lệ
                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                        // Lấy đuôi file từ $value (chuyển về chữ thường để so sánh)
                                        $extension = $value ? strtolower(pathinfo($value, PATHINFO_EXTENSION)) : '';
                                        // Kiểm tra xem $value có phải là file ảnh không
                                        $isImage = $value && in_array($extension, $imageExtensions);
                                        // Kiểm tra xem $key có nằm trong mảng đặc biệt không
                                        $isSpecialField = in_array($key, ['front_page', 'rear_page']);
                                    @endphp
                                    @if($isImage)
                                        <div class="image-wrapper {{ $isSpecialField ? 'special-image' : '' }}">
                                            <img src="{{ asset($value) }}" alt="{{ $labelMap[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}" class="data-image" onerror="this.onerror=null; this.src='{{ asset('default-image.jpg') }}';">
                                            <a href="{{ asset($value) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                {{ trans('core/base::layouts.view_file') }}
                                            </a>
                                        </div>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <p class="text-muted">{{ trans('core/base::layouts.no_data_available')}}.</p>
                @endif
            </div>

            <!-- Lịch sử hành động -->
            <div class="mb-5">
                <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.action_history')}}</h5>
                @if($pending->logs->isEmpty())
                    <p class="text-muted">{{ trans('core/base::layouts.no_action_history_available')}}.</p>
                @else
                    <table class="table table-bordered table-striped info-table">
                        <thead>
                            <tr>
                                <th>{{ trans('core/base::layouts.action')}}</th>
                                <th>{{ trans('core/base::layouts.note')}}</th>
                                <th>{{ trans('core/base::layouts.admin_id')}}</th>
                                <th>{{ trans('core/base::layouts.action_at')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pending->logs as $log)
                                <tr>
                                    <td>{{ ucfirst($log->action) }}</td>
                                    <td>{{ $log->note }}</td>
                                    <td>{{ $log->admin_id ?? 'N/A' }}</td>
                                    <td>{{ $log->action_at ? $log->action_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Nút hành động -->
            @if($pending->status == 'pending')
                <div class="d-flex gap-2 justify-content-end">
                    <form action="{{ route('kyc.pending.approve', $pending->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-2" onclick="return confirm('{{ trans('core/base::layouts.confirm_approve_kyc') }}')">
                                <svg class="icon svg-icon-ti-ti-check" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            {{ trans('core/base::layouts.approve')}}
                        </button>
                    </form>
                    <form action="{{ route('kyc.pending.reject', $pending->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm d-flex align-items-center gap-2" onclick="return confirm('{{ trans('core/base::layouts.confirm_reject_kyc_with_name', ['name' => $pending->name]) }}')">
                            <svg class="icon svg-icon-ti-ti-x" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                            {{ trans('core/base::layouts.reject')}}
                        </button>
                    </form>
                </div>
            @endif
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

    .info-table thead th {
        background-color: #f1f5f9;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    /* Tùy chỉnh ảnh trong KYC Data */
    .data-image {
        width: 200px; /* Chiều rộng cố định */
        height: 150px; /* Chiều cao cố định */
        object-fit: cover; /* Giữ tỷ lệ ảnh, cắt phần thừa */
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .data-image:hover {
        transform: scale(1.05);
        border-color: #007bff;
    }

    .image-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .special-image .data-image {
        border: 2px solid #007bff; /* Viền đặc biệt cho front_page và rear_page */
    }

    /* Tùy chỉnh avatar */
    .avatar-wrapper {
        width: 200px; /* Tăng từ 150px lên 200px */
        height: 200px; /* Tăng từ 150px lên 200px */
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
        font-size: 80px; /* Tăng từ 60px lên 80px để phù hợp với kích thước mới */
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

    /* Tùy chỉnh nút hành động */
    .btn-success, .btn-danger {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-success {
        background-color: #10b981;
        border-color: #10b981;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }

    .btn-success:hover {
        background-color: #0d8f6b;
        border-color: #0d8f6b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-danger {
        background-color: #ef4444;
        border-color: #ef4444;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
    }

    .btn-danger:hover {
        background-color: #dc2626;
        border-color: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }

    .btn svg {
        margin-right: 5px;
    }

    /* Tùy chỉnh tiêu đề phần */
    h5 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
    }

    .text-muted {
        color: #6b7280 !important;
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

        .btn-primary,
        .btn-success,
        .btn-danger {
            padding: 6px 12px;
            font-size: 12px;
        }

        .avatar-wrapper {
            width: 120px; /* Giảm từ 200px xuống 120px trên màn hình nhỏ */
            height: 120px; /* Giảm từ 200px xuống 120px trên màn hình nhỏ */
        }

        .avatar-placeholder {
            font-size: 48px; /* Giảm từ 80px xuống 48px trên màn hình nhỏ */
        }

        .data-image {
            width: 150px; /* Giảm kích thước ảnh trên di động */
            height: 100px;
        }
    }
</style>
@endsection