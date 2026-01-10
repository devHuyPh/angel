@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<div class="container-wrapper">
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ trans('core/base::layouts.kyv_log_details')}} (ID: {{ $log->id }})</h4>
            <a href="{{ route('kyc.log') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
                {{ trans('core/base::layouts.back_to_list')}}
            </a>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Thông tin chính -->
                <div class="col-md-6 mb-4">
                    <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.main_information')}}</h5>
                    <div class="info-item">
                        <strong>ID:</strong> {{ $log->id }}
                    </div>
                    <div class="info-item">
                        <strong>{{ trans('core/base::layouts.kyc_pending')}}:</strong>
                        @if($log->kyc_pending_id)
                            <a href="{{ route('kyc.pending.view', $log->kyc_pending_id) }}" class="text-primary">
                                {{ $log->kyc_pending_name }} ({{ $log->kyc_verification_type }})
                            </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="info-item">
                        <strong>{{ trans('core/base::layouts.action')}}:</strong>
                        <span class="badge {{ $log->action == 'submitted' ? 'bg-info' : ($log->action == 'approved' ? 'bg-success' : 'bg-danger') }} text-white">
							{{ trans('core/base::layouts.'.$log->action)}}
                        </span>
                    </div>
                    <div class="info-item">
                        <strong>{{ trans('core/base::layouts.kyc_status')}}:</strong>
                        @if($log->kyc_status)
                            <span class="badge {{ $log->kyc_status == 'pending' ? 'bg-warning' : ($log->kyc_status == 'approved' ? 'bg-success' : 'bg-danger') }} text-white">
								{{ trans('core/base::layouts.'.$log->kyc_status)}}
                        	</span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="info-item">
                        <strong>{{ trans('core/base::layouts.customer_status')}}:</strong>
                        @if($log->customer_status)
                            <span class="badge {{ $log->customer_status == 'active' ? 'bg-success' : ($log->customer_status == 'inactive' ? 'bg-warning' : 'bg-danger') }} text-white">
                                {{ ucfirst($log->customer_status) }}
                            </span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>

                <!-- Thông tin người thực hiện -->
                <div class="col-md-6 mb-4">
                    <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.performed_by')}}</h5>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-wrapper">
                            @if($log->admin_id && $log->admin && $log->admin->avatar)
                                <img src="{{ asset('storage/' . $log->admin->avatar) }}" alt="Avatar" class="avatar-img">
                            @elseif($log->customer_id && $log->customer && $log->customer->avatar)
                                <img src="{{ asset('storage/' . $log->customer->avatar) }}" alt="Avatar" class="avatar-img">
                            @else
                                <div class="avatar-placeholder">
                                    {{ $log->admin_name ? strtoupper(substr($log->admin_name, 0, 1)) : ($log->customer_name ? strtoupper(substr($log->customer_name, 0, 1)) : 'N/A') }}
                                </div>
                            @endif
                        </div>
                        <div>
                            @if($log->admin_id)
                                <div class="info-item">
                                    <strong>{{ trans('core/base::layouts.name')}}:</strong> {{ $log->admin_name ?? 'N/A' }} (Admin)
                                </div>
                                <div class="info-item">
                                    <strong>{{ trans('core/base::layouts.email')}}:</strong> {{ $log->admin_email ?? 'N/A' }}
                                </div>
                            @elseif($log->customer_id)
                                <div class="info-item">
                                    <strong>{{ trans('core/base::layouts.name')}}:</strong> {{ $log->customer_name ?? 'N/A' }} (Customer)
                                </div>
                                <div class="info-item">
                                    <strong>{{ trans('core/base::layouts.email')}}:</strong> {{ $log->customer_email ?? 'N/A' }}
                                </div>
                                <div class="info-item">
                                    <strong>{{ trans('core/base::layouts.phone')}}:</strong> {{ $log->customer_phone ?? 'N/A' }}
                                </div>
                            @else
                                <div class="info-item">
                                    <strong>{{ trans('core/base::layouts.performed_by')}}:</strong> <span class="text-muted">N/A</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Thông tin bổ sung -->
                <div class="col-md-6 mb-4">
                    <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.addditional_infomation')}}</h5>
                    <div class="info-item">
                        <strong>{{ trans('core/base::layouts.affected_entity')}}:</strong>
                        @if($log->affected_entity && $log->affected_entity_id)
                            {{ ucfirst($log->affected_entity) }} (ID: {{ $log->affected_entity_id }})
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="info-item">
                        <strong>{{ trans('core/base::layouts.system_notification')}}:</strong>
                        <span class="badge {{ $log->system_notification ? 'bg-success' : 'bg-secondary' }} text-white">
                            {{ $log->system_notification ? trans('core/base::layouts.yes') : trans('core/base::layouts.no') }}
                        </span>
                    </div>
                    <div class="info-item">
                        <strong>{{ trans('core/base::layouts.details')}}:</strong>
                        @if($log->action == 'rejected')
                            <span class="text-danger">{{ trans('core/base::layouts.reason')}}:</span> {{ $log->reason ?? $log->note ?? 'N/A' }}
                        @else
                            <span class="text-muted">{{ trans('core/base::layouts.note')}}:</span> {{ $log->note ?? 'N/A' }}
                        @endif
                    </div>
                </div>

                <!-- Thời gian -->
                <div class="col-md-6 mb-4">
                    <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.timestamps')}}</h5>
                    <div class="info-item">
                        <strong>{{ trans('core/base::layouts.action_at')}}:</strong> {{ $log->action_at ? $log->action_at->format('Y-m-d H:i:s') : 'N/A' }}
                    </div>
                    <div class="info-item">
                        <strong>{{ trans('core/base::layouts.created_at')}}:</strong> {{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A' }}
                    </div>
                </div>

                <!-- Data Changes -->
                <div class="col-12">
                    <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.data_changes')}}</h5>
                    @if($log->data_before || $log->data_after)
                        <div class="data-changes d-flex gap-3 flex-wrap">
                            <!-- Hiển thị Data Before -->
                            <div class="data-section">
                                <strong class="text-muted">{{ trans('core/base::layouts.before')}}:</strong>
                                @php
                                    $dataBefore = is_string($log->data_before) ? json_decode($log->data_before, true) : $log->data_before;
                                @endphp
                                @if(is_array($dataBefore) && !empty($dataBefore))
                                    <table class="table table-sm table-bordered nested-table">
                                        @foreach($dataBefore as $key => $value)
                                            <tr>
                                                <td class="data-key">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                                <td class="data-value">
                                                    @php
                                                        // Danh sách các đuôi file ảnh hợp lệ
                                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                                        // Lấy đuôi file từ $value (chuyển về chữ thường để so sánh)
                                                        $extension = $value ? strtolower(pathinfo($value, PATHINFO_EXTENSION)) : '';
                                                        // Kiểm tra xem $value có phải là file ảnh không
                                                        $isImage = $value && in_array($extension, $imageExtensions);
                                                        // Kiểm tra xem $key có nằm trong mảng đặc biệt không
                                                        $isSpecialField = in_array($key, ['front_side_of_id_card', 'back_side_of_id_card']);
                                                    @endphp
                                                    @if($isImage)
                                                        <div class="image-wrapper {{ $isSpecialField ? 'special-image' : '' }}">
                                                            <img src="{{ asset($value) }}" alt="{{ ucwords(str_replace('_', ' ', $key)) }}" class="data-image" onerror="this.onerror=null; this.src='{{ asset('default-image.jpg') }}';">
                                                            @if($isSpecialField)
                                                                <a href="{{ asset($value) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                                    {{ trans('core/base::layouts.view_file') }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>

                            <!-- Hiển thị Data After -->
                            <div class="data-section">
                                <strong class="text-muted">{{ trans('core/base::layouts.after')}}:</strong>
                                @php
                                    $dataAfter = is_string($log->data_after) ? json_decode($log->data_after, true) : $log->data_after;
                                @endphp
                                @if(is_array($dataAfter) && !empty($dataAfter))
                                    <table class="table table-sm table-bordered nested-table">
                                        @foreach($dataAfter as $key => $value)
                                            <tr>
                                                <td class="data-key">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                                <td class="data-value">
                                                    @php
                                                        // Danh sách các đuôi file ảnh hợp lệ
                                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                                        // Lấy đuôi file từ $value (chuyển về chữ thường để so sánh)
                                                        $extension = $value ? strtolower(pathinfo($value, PATHINFO_EXTENSION)) : '';
                                                        // Kiểm tra xem $value có phải là file ảnh không
                                                        $isImage = $value && in_array($extension, $imageExtensions);
                                                        // Kiểm tra xem $key có nằm trong mảng đặc biệt không
                                                        $isSpecialField = in_array($key, ['front_side_of_id_card', 'back_side_of_id_card']);
                                                    @endphp
                                                    @if($isImage)
                                                        <div class="image-wrapper {{ $isSpecialField ? 'special-image' : '' }}">
                                                            <img src="{{ asset($value) }}" alt="{{ ucwords(str_replace('_', ' ', $key)) }}" class="data-image" onerror="this.onerror=null; this.src='{{ asset('default-image.jpg') }}';">
                                                            @if($isSpecialField)
                                                                <a href="{{ asset($value) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                                    {{ trans('core/base::layouts.view_file') }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <span class="text-muted">{{ trans('core/base::layouts.no_changes')}}</span>
                    @endif
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
    }

    .card-header {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .card-body {
        padding: 30px;
    }

    .info-item {
        margin-bottom: 15px;
        font-size: 14px;
        color: #374151;
    }

    .info-item strong {
        display: inline-block;
        width: 150px;
        font-weight: 600;
        color: #1f2937;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
        transition: all 0.3s ease;
    }

    .badge.bg-info {
        background-color: #0ea5e9 !important;
    }

    .badge.bg-success {
        background-color: #10b981 !important;
    }

    .badge.bg-danger {
        background-color: #ef4444 !important;
    }

    .badge.bg-warning {
        background-color: #f59e0b !important;
        color: #fff !important;
    }

    .badge.bg-secondary {
        background-color: #6b7280 !important;
    }

    .text-muted {
        color: #6b7280 !important;
    }

    .text-primary {
        color: #007bff !important;
    }

    .text-danger {
        color: #ef4444 !important;
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

    /* Style cho avatar */
    .avatar-wrapper {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #e2e8f0;
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
        font-size: 20px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .avatar-placeholder:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }

    /* Style cho Data Changes */
    .data-changes {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .data-section {
        flex: 1;
        min-width: 300px;
    }

    .data-section strong {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1f2937;
    }

    /* Style cho bảng lồng */
    .nested-table {
        margin: 0;
        font-size: 12px;
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background-color: #f9fafb;
    }

    .nested-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #e2e8f0;
    }

    .nested-table .data-key {
        font-weight: 500;
        background-color: #f1f5f9;
        width: 40%;
        color: #4b5563;
    }

    .nested-table .data-value {
        background-color: #fff;
        color: #374151;
    }

    .nested-table .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 6px;
    }

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
        border: 2px solid #007bff; /* Viền đặc biệt cho front_side_of_id_card và back_side_of_id_card */
    }

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

        .info-item {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .info-item strong {
            width: 120px;
        }

        .btn-primary {
            padding: 6px 12px;
            font-size: 12px;
        }

        .avatar-wrapper {
            width: 40px;
            height: 40px;
        }

        .avatar-placeholder {
            font-size: 16px;
        }

        .nested-table {
            font-size: 10px;
        }

        .nested-table td {
            padding: 6px 8px;
        }

        .data-changes {
            flex-direction: column;
            gap: 10px;
        }

        .data-section {
            min-width: 100%;
        }

        .data-image {
            width: 150px; /* Giảm kích thước ảnh trên di động */
            height: 100px;
        }
    }
</style>
@endsection