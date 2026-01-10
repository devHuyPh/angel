@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<div class="table-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                <!-- Tìm kiếm -->
                <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-1">
                    <div class="table-search-input">
                        <form method="GET" action="{{ route('kyc.pending') }}">
                            <label>
                                <input type="search" name="search" class="form-control input-sm" placeholder="{{ trans('core/base::layouts.search')}}" style="min-width: 120px" value="{{ request('search') }}">
                            </label>
                        </form>
                    </div>
                </div>
                <!-- Nút Reload -->
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('kyc.pending') }}" class="btn btn-outline-primary" type="button" data-bb-toggle="dt-buttons" data-bb-target=".buttons-reload" tabindex="0" aria-controls="botble-marketplace-tables-unverified-vendor-table">
                        <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                        </svg>
                        {{ trans('core/base::layouts.reload')}}
                    </a>
                </div>
            </div>
        </div>

        <div class="card-table">
            <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped table-hover" id="botble-marketplace-tables-unverified-vendor-table">
                    <thead>
                        <tr>
                            <th class="text-start column-key-3 text-center">{{ trans('core/base::layouts.name')}}</th>
                            <th class="column-key-4">{{ trans('core/base::layouts.avatar')}}</th>
                            <th class="text-start column-key-5">{{ trans('core/base::layouts.verification_type')}}</th>
                            <th class="text-start column-key-6">{{ trans('core/base::layouts.data')}}</th>
                            <th class="text-start column-key-7">{{ trans('core/base::layouts.status')}}</th>
                            <th class="text-center no-column-visibility text-nowrap">{{ trans('core/base::layouts.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendings as $pending)
    <tr>
        <td class="text-start column-key-3">
            <a href="{{ route('kyc.pending.view', $pending->id) }}" title="{{ $pending->name }}">{{ $pending->name }}</a>
        </td>
        <td class="column-key-4">
            <div class="avatar-wrapper">
                <img src="{{ $pending->customer ? asset('storage/' . $pending->customer->avatar) : asset('default-avatar.jpg') }}" alt="{{ $pending->name }}" class="avatar-img rounded-circle">
            </div>
        </td>
        <td class="text-start column-key-5">{{ $pending->verification_type }}</td>
        <td class="text-start column-key-6">
            @php
                // Kiểm tra và chuyển data thành mảng
                $data = is_string($pending->data) ? json_decode($pending->data, true) : $pending->data;
                // Lấy field_name và field_labels từ $kycForms dựa trên kyc_form_id
                $formData = $kycForms[$pending->kyc_form_id] ?? [];
                $fieldNames = $formData['field_name'] ?? [];
                $fieldLabels = $formData['field_labels'] ?? $fieldNames;
                // Map key không dấu với tên có dấu
                $labelMap = array_combine($fieldNames, $fieldLabels);
            @endphp
            @if(is_array($data) && !empty($data))
                <!-- Hiển thị dữ liệu dưới dạng danh sách key-value -->
                <div class="data-list">
                    @foreach($data as $key => $value)
                        <div class="data-item">
                            <span class="data-label">{{ $labelMap[$key] ?? ucwords(str_replace('_', ' ', $key)) }}:</span>
                            <span class="data-content">
                                @php
                                    // Danh sách các đuôi file ảnh hợp lệ
                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                    // Lấy đuôi file từ $value (chuyển về chữ thường để so sánh)
                                    $extension = $value ? strtolower(pathinfo($value, PATHINFO_EXTENSION)) : '';
                                    // Kiểm tra xem $value có phải là file ảnh không
                                    $isImage = $value && in_array($extension, $imageExtensions);
                                @endphp
                                @if($isImage)
                                    <a href="{{ asset($value) }}" target="_blank" class="btn btn-sm btn-outline-primary">View File</a>
                                @else
                                    {{ str_replace('-', ' ', $value) }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                N/A
            @endif
        </td>
        <td class="text-start column-key-7">
            <span class="badge {{ $pending->status == 'approved' ? 'bg-success' : ($pending->status == 'pending' ? 'bg-warning' : 'bg-danger') }} text-{{ $pending->status == 'approved' ? 'success' : ($pending->status == 'pending' ? 'warning' : 'danger') }}-fg">
                {{ ucfirst($pending->status) }}
            </span>
        </td>
        <td class="text-center no-column-visibility text-nowrap">
            <div class="table-actions d-flex gap-1 justify-content-center">
                <!-- Nút View -->
                <a href="{{ route('kyc.pending.view', $pending->id) }}" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-title="View">
                    <svg class="icon svg-icon-ti-ti-eye" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M12 18c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6c-2.4 4 -5.4 6 -9 6z" />
                    </svg>
                    <span class="sr-only">{{ trans('core/base::layouts.view')}}</span>
                </a>
                <!-- Nút Approve -->
                @if ($pending->status == 'pending')
                    <button type="button" class="btn btn-sm btn-icon btn-success confirm-action" 
                            data-action="approve" 
                            data-id="{{ $pending->id }}" 
                            data-url="{{ route('kyc.pending.approve', $pending->id) }}" 
                            data-bs-toggle="modal" 
                            data-bs-target="#confirmModal" 
                            data-bs-title="{{ trans('core/base::layouts.confirm_approve_title') }}" 
        data-bs-message="{{ trans('core/base::layouts.confirm_approve_kyc_with_name', ['name' => $pending->name]) }}">
                        <svg class="icon svg-icon-ti-ti-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        <span class="sr-only">{{ trans('core/base::layouts.approve')}}</span>
                    </button>
                    <!-- Nút Reject -->
                    <button type="button" class="btn btn-sm btn-icon btn-danger confirm-action" 
                            data-action="reject" 
                            data-id="{{ $pending->id }}" 
                            data-url="{{ route('kyc.pending.reject', $pending->id) }}" 
                            data-bs-toggle="modal" 
                            data-bs-target="#confirmModal" 
                            data-bs-title="{{ trans('core/base::layouts.confirm_reject_title') }}" 
        data-bs-message="{{ trans('core/base::layouts.confirm_reject_kyc_with_name', ['name' => $pending->name]) }}">
                        <svg class="icon svg-icon-ti-ti-x" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                        <span class="sr-only">{{ trans('core/base::layouts.reject')}}</span>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">{{ trans('core/base::layouts.no_pending_kyc_requests_found')}}.</td>
    </tr>
@endforelse
                    </tbody>
                </table>
            </div>

            <!-- Danh sách cho giao diện di động -->
            <div class="mobile-view">
                @forelse ($pendings as $pending)
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <div class="mobile-avatar">
                                <img src="{{ $pending->customer ? asset('storage/' . $pending->customer->avatar) : asset('default-avatar.jpg') }}" alt="{{ $pending->name }}" class="avatar-img">
                            </div>
                            <div class="mobile-info">
                                <h6 class="mobile-name">
                                    <a href="{{ route('kyc.pending.view', $pending->id) }}" title="{{ $pending->name }}">{{ $pending->name }}</a>
                                </h6>
                                <span class="badge {{ $pending->status == 'approved' ? 'bg-success' : ($pending->status == 'pending' ? 'bg-warning' : 'bg-danger') }} text-{{ $pending->status == 'approved' ? 'success' : ($pending->status == 'pending' ? 'warning' : 'danger') }}-fg">
                                    {{ ucfirst($pending->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="mobile-card-body">
                            <div class="mobile-details">
                                <p><strong>{{ trans('core/base::layouts.kyc_form_id') }}:</strong> {{ $pending->kyc_form_id ?? 'N/A' }}</p>
                                <p><strong>{{ trans('core/base::layouts.verification_type') }}:</strong> {{ $pending->verification_type }}</p>
                                <div class="mobile-data">
                                    <strong>{{ trans('core/base::layouts.data') }}:</strong>
                                    @php
                                        $data = is_string($pending->data) ? json_decode($pending->data, true) : $pending->data;
                                        // Lấy field_name và field_labels từ $kycForms dựa trên kyc_form_id
                                        $formData = $kycForms[$pending->kyc_form_id] ?? [];
                                        $fieldNames = $formData['field_name'] ?? [];
                                        $fieldLabels = $formData['field_labels'] ?? $fieldNames;
                                        // Map key không dấu với tên có dấu
                                        $labelMap = array_combine($fieldNames, $fieldLabels);
                                    @endphp
                                    @if(is_array($data) && !empty($data))
                                        <div class="data-list">
                                            @foreach($data as $key => $value)
                                                <div class="data-item">
                                                    <span class="data-label">{{ $labelMap[$key] ?? ucwords(str_replace('_', ' ', $key)) }}:</span>
                                                    <span class="data-content">
                                                        @php
                                                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                                            $extension = $value ? strtolower(pathinfo($value, PATHINFO_EXTENSION)) : '';
                                                            $isImage = $value && in_array($extension, $imageExtensions);
                                                        @endphp
                                                        @if($isImage)
                                                            <a href="{{ asset($value) }}" target="_blank" class="btn btn-sm btn-outline-primary">View File</a>
                                                        @else
                                                            {{ str_replace('-', ' ', $value) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mobile-card-footer">
                            <div class="table-actions d-flex gap-2 justify-content-center">
                                <!-- Nút View -->
                                <a href="{{ route('kyc.pending.view', $pending->id) }}" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-title="View">
                                    <svg class="icon svg-icon-ti-ti-eye" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M12 18c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6c-2.4 4 -5.4 6 -9 6z" />
                                    </svg>
                                    <span class="sr-only">{{ trans('core/base::layouts.view')}}</span>
                                </a>
                                <!-- Nút Approve -->
                                @if ($pending->status == 'pending')
                                    <button type="button" class="btn btn-sm btn-icon btn-success confirm-action" 
                                            data-action="approve" 
                                            data-id="{{ $pending->id }}" 
                                            data-url="{{ route('kyc.pending.approve', $pending->id) }}" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmModal" 
                                            data-bs-title="{{ trans('core/base::layouts.confirm_approve_title') }}" 
                                            data-bs-message="{{ trans('core/base::layouts.confirm_approve_kyc_with_name', ['name' => $pending->name]) }}">
                                        <svg class="icon svg-icon-ti-ti-check" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                        <span class="sr-only">{{ trans('core/base::layouts.approve')}}</span>
                                    </button>
                                    <!-- Nút Reject -->
                                    <button type="button" class="btn btn-sm btn-icon btn-danger confirm-action" 
                                            data-action="reject" 
                                            data-id="{{ $pending->id }}" 
                                            data-url="{{ route('kyc.pending.reject', $pending->id) }}" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmModal" 
                                            data-bs-title="{{ trans('core/base::layouts.confirm_reject_title') }}" 
                                            data-bs-message="{{ trans('core/base::layouts.confirm_reject_kyc_with_name', ['name' => $pending->name]) }}">
                                        <svg class="icon svg-icon-ti-ti-x" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M18 6l-12 12" />
                                            <path d="M6 6l12 12" />
                                        </svg>
                                        <span class="sr-only">{{ trans('core/base::layouts.reject')}}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="mobile-card text-center">
                        <p>{{ trans('core/base::layouts.no_pending_kyc_requests_found')}}.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Phân trang -->
        <div class="card-footer">
            {{ $pendings->links() }}
        </div>
    </div>
</div>

<!-- Modal xác nhận -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">{{ trans('core/base::layouts.confirm_action')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">{{ trans('core/base::layouts.are_you_sure_you_want_to_perform_this_action')}}?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('core/base::layouts.no')}}</button>
                <form id="confirmForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary" id="confirmButton">{{ trans('core/base::layouts.yes')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Lắng nghe sự kiện khi modal được hiển thị
    const confirmModal = document.getElementById('confirmModal');
    confirmModal.addEventListener('show.bs.modal', function (event) {
        // Lấy nút đã kích hoạt modal
        const button = event.relatedTarget;

        // Lấy dữ liệu từ các thuộc tính data-*
        const action = button.getAttribute('data-action');
        const url = button.getAttribute('data-url');
        const title = button.getAttribute('data-bs-title');
        const message = button.getAttribute('data-bs-message');

        // Cập nhật tiêu đề và nội dung của modal
        const modalTitle = confirmModal.querySelector('.modal-title');
        const modalMessage = confirmModal.querySelector('#confirmMessage');
        modalTitle.textContent = title;
        modalMessage.textContent = message;

        // Cập nhật form trong modal
        const form = confirmModal.querySelector('#confirmForm');
        form.action = url;

        // Cập nhật màu sắc và văn bản của nút "Yes" dựa trên hành động
        const confirmButton = confirmModal.querySelector('#confirmButton');
        if (action === 'approve') {
            confirmButton.classList.remove('btn-danger');
            confirmButton.classList.add('btn-success');
            confirmButton.textContent = 'Approve';
        } else if (action === 'reject') {
            confirmButton.classList.remove('btn-success');
            confirmButton.classList.add('btn-danger');
            confirmButton.textContent = 'Reject';
        }
    });
});
</script>

<style>
    /* Tùy chỉnh bảng chính */
    .table-wrapper .table {
        border-collapse: collapse;
        width: 100%;
        font-size: 14px;
    }

    .table-wrapper .table thead th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: 600;
        text-transform: uppercase;
        padding: 12px 15px;
        border-bottom: 2px solid #dee2e6;
    }

    .table-wrapper .table tbody tr {
        transition: background-color 0.3s ease;
    }

    .table-wrapper .table tbody tr:hover {
        background-color: #f1f3f5;
    }

    .table-wrapper .table tbody td {
        padding: 10px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #dee2e6;
    }

    /* Tùy chỉnh danh sách key-value trong cột Data */
    .data-list {
        display: flex;
        flex-direction: column;
        gap: 8px; /* Khoảng cách giữa các mục */
    }

    .data-item {
        display: flex;
        align-items: center;
        gap: 10px; /* Khoảng cách giữa label và content */
        font-size: 13px;
        padding: 5px 0;
        border-bottom: 1px solid #e9ecef; /* Đường viền dưới mỗi mục */
    }

    .data-label {
        font-weight: 500;
        color: #1f2937; /* Màu đậm cho nhãn */
        text-transform: capitalize;
        min-width: 120px; /* Đảm bảo nhãn có độ rộng tối thiểu */
    }

    .data-content {
        color: #374151; /* Màu nhạt hơn cho nội dung */
    }

    .data-content .btn {
        padding: 3px 8px;
        font-size: 12px;
        border-radius: 4px;
    }

    /* Tùy chỉnh avatar */
    .column-key-4 {
        width: 80px; /* Tăng từ 70px lên 80px để phù hợp với kích thước avatar mới */
        text-align: center; /* Căn giữa avatar */
    }

    .avatar-wrapper {
        width: 60px; /* Tăng từ 50px lên 60px */
        height: 60px; /* Tăng từ 50px lên 60px */
        margin: 0 auto; /* Căn giữa trong cột */
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Đảm bảo hình ảnh không bị méo */
        border-radius: 50%; /* Làm tròn hoàn toàn (thay thế rounded-circle) */
        border: 2px solid #dee2e6; /* Viền nhẹ */
        transition: all 0.3s ease; /* Hiệu ứng mượt mà */
    }

    .avatar-img:hover {
        border-color: #007bff; /* Đổi màu viền khi hover */
        transform: scale(1.05); /* Phóng to nhẹ khi hover */
    }

    /* Tùy chỉnh badge trạng thái */
    .badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    /* Tùy chỉnh nút hành động */
    .table-actions .btn {
        padding: 5px 10px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .table-actions .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    /* Tùy chỉnh modal */
    .modal-content {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
    }

    .modal-body {
        font-size: 14px;
        color: #374151;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
    }

    .modal-footer .btn {
        padding: 8px 16px;
        border-radius: 5px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .modal-footer .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }

    .modal-footer .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #5a6268;
    }

    .modal-footer .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .modal-footer .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .modal-footer .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .modal-footer .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    /* Tùy chỉnh phân trang */
    .pagination {
        justify-content: center;
        margin-top: 20px;
    }

    .pagination .page-link {
        border-radius: 5px;
        margin: 0 3px;
        color: #007bff;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    /* Ẩn bảng trên di động và hiển thị danh sách card */
    @media (max-width: 768px) {
        .table-wrapper .table {
            display: none; /* Ẩn bảng trên di động */
        }

        .mobile-view {
            display: block; /* Hiển thị danh sách card trên di động */
        }

        .data-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            font-size: 12px;
        }

        .data-label {
            min-width: auto;
        }
    }

    /* Ẩn danh sách card trên desktop */
    @media (min-width: 769px) {
        .mobile-view {
            display: none; /* Ẩn danh sách card trên desktop */
        }
    }

    /* Tùy chỉnh giao diện card trên di động */
    .mobile-view {
        padding: 10px;
    }

    .mobile-card {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .mobile-card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .mobile-card-header {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #dee2e6;
    }

    .mobile-avatar {
        width: 40px;
        height: 40px;
        margin-right: 10px;
    }

    .mobile-avatar .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 1px solid #dee2e6;
    }

    .mobile-info {
        flex: 1;
    }

    .mobile-name {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }

    .mobile-name a {
        color: #007bff;
        text-decoration: none;
    }

    .mobile-name a:hover {
        text-decoration: underline;
    }

    .mobile-card-body {
        padding: 10px;
    }

    .mobile-details p {
        margin: 5px 0;
        font-size: 12px;
        color: #374151;
    }

    .mobile-details strong {
        color: #1f2937;
    }

    .mobile-data .data-list {
        margin-top: 5px;
    }

    .mobile-data .data-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
        font-size: 12px;
        padding: 5px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .mobile-data .data-label {
        font-weight: 500;
        color: #1f2937;
        text-transform: capitalize;
    }

    .mobile-data .data-content {
        color: #374151;
    }

    .mobile-data .data-content .btn {
        padding: 2px 6px;
        font-size: 11px;
    }

    .mobile-card-footer {
        padding: 10px;
        border-top: 1px solid #dee2e6;
        text-align: center;
    }

    .mobile-card-footer .table-actions .btn {
        padding: 6px 12px; /* Tăng kích thước nút để dễ bấm */
        font-size: 12px;
    }

    /* Tùy chỉnh nút hành động trên di động */
    @media (max-width: 768px) {
        .column-key-4 {
            width: 60px; /* Giảm chiều rộng cột trên màn hình nhỏ */
        }

        .avatar-wrapper {
            width: 40px; /* Giảm kích thước avatar trên màn hình nhỏ */
            height: 40px;
        }

        .avatar-img {
            border-width: 1px; /* Giảm độ dày viền trên màn hình nhỏ */
        }

        .table-actions .btn {
            padding: 6px 12px; /* Tăng kích thước nút để dễ bấm */
        }

        .table-actions .btn svg {
            width: 20px;
            height: 20px;
        }

        .modal-body {
            font-size: 12px;
        }

        .modal-footer .btn {
            padding: 6px 12px;
            font-size: 12px;
        }

        .pagination .page-link {
            padding: 6px 10px;
            font-size: 12px;
        }
    }
</style>
@endsection