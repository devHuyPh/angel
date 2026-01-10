@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<div class="table-wrapper">
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-light border-bottom">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <!-- Tìm kiếm -->
                <div class="search-wrapper">
                    <form method="GET" action="{{ route('kyc.log') }}" class="d-flex align-items-center">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </span>
                            <input type="search" name="search" class="form-control border-start-0" placeholder="{{ trans('core/base::layouts.search')}}" value="{{ request('search') }}">
                        </div>
                    </form>
                </div>
                <!-- Nút Reload -->
                <div>
                    <a href="{{ route('kyc.log') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                        <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                        </svg>
                        {{ trans('core/base::layouts.reload')}}
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-vcenter">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">ID</th>
                            <th class="text-start">{{ trans('core/base::layouts.kyc_pending')}}</th>
                            <th class="text-start">{{ trans('core/base::layouts.performed_by')}}</th>
                            <th class="text-start">{{ trans('core/base::layouts.action')}}</th>
                            <th class="text-start">{{ trans('core/base::layouts.kyc_status')}}</th>
                            <th class="text-start">{{ trans('core/base::layouts.details')}}</th>
                            <th class="text-start">{{ trans('core/base::layouts.created_at')}}</th>
                            <th class="text-center" style="width: 80px;">{{ trans('core/base::layouts.view')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="text-center">{{ $log->id }}</td>
                                <td class="text-start">
                                    @if($log->kyc_pending_id)
                                    {{--  --}}
                                        <a href="{{ route('kyc.pending.view', $log->kyc_pending_id) }}" class="text-primary fw-medium">
                                            {{ $log->kyc_pending_name }} ({{ $log->kyc_verification_type }})
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-start">
                                    @if($log->admin_id)
                                        <span class="fw-medium">{{ $log->admin_name ?? 'N/A' }}</span> ({{ trans('core/base::layouts.admin')}})<br>
                                        <small class="text-muted">Email: {{ $log->admin_email ?? 'N/A' }}</small>
                                    @elseif($log->customer_id)
                                        <span class="fw-medium">{{ $log->customer_name ?? 'N/A' }}</span> ({{ trans('core/base::layouts.customer')}})<br>
                                        <small class="text-muted">{{ trans('core/base::layouts.email')}}: {{ $log->customer_email ?? 'N/A' }}</small><br>
                                        <small class="text-muted">{{ trans('core/base::layouts.phone')}}: {{ $log->customer_phone ?? 'N/A' }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-start">
                                    <span class="badge {{ $log->action == 'submitted' ? 'bg-info' : ($log->action == 'approved' ? 'bg-success' : 'bg-danger') }} text-white">
											{{ trans('core/base::layouts.'.$log->action)}}
                                    </span>
                                </td>
                                <td class="text-start">
                                    @if($log->kyc_status)
                                        <span class="badge {{ $log->kyc_status == 'pending' ? 'bg-warning' : ($log->kyc_status == 'approved' ? 'bg-success' : 'bg-danger') }} text-white">
											{{ trans('core/base::layouts.'.$log->kyc_status)}}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-start">
                                    @if($log->action == 'rejected')
                                        <strong class="text-danger">{{ trans('core/base::layouts.reason')}}:</strong> {{ $log->reason ?? $log->note ?? 'N/A' }}<br>
                                    @else
                                        <strong class="text-muted">{{ trans('core/base::layouts.note')}}:</strong> {{ $log->note ?? 'N/A' }}
                                    @endif
                                </td>
                                <td class="text-start">{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td class="text-center">
                                    {{--  --}}
                                    <a href="{{ route('kyc.log.view', $log->id) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ trans('core/base::layouts.no_kyc_logs_found')}}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Phân trang -->
         <div class="card-footer bg-light border-top">
            {{ $logs->links() }}
        </div> 
    </div>
</div>

<style>
    .table-wrapper {
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

    .search-wrapper .input-group {
        max-width: 300px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .search-wrapper .form-control {
        border: 1px solid #e2e8f0;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .search-wrapper .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .search-wrapper .input-group-text {
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-right: none;
        color: #6b7280;
    }

    .btn-outline-primary {
        border-color: #007bff;
        color: #007bff;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }

    .btn-outline-primary svg {
        margin-right: 5px;
    }

    .btn-outline-info {
        border-color: #17a2b8;
        color: #17a2b8;
        padding: 4px 8px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-outline-info:hover {
        background-color: #17a2b8;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(23, 162, 184, 0.2);
    }

    .table {
        margin-bottom: 0;
        font-size: 14px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background-color: #f1f5f9;
        color: #1f2937;
        font-weight: 600;
        text-transform: uppercase;
        padding: 15px 20px;
        border-bottom: 2px solid #e2e8f0;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
    }

    .table tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
        color: #374151;
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

    .fw-medium {
        font-weight: 500;
    }

    .card-footer {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .pagination .page-link {
        border-radius: 6px;
        margin: 0 3px;
        color: #007bff;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
        transform: translateY(-1px);
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    @media (max-width: 768px) {
        .table-wrapper {
            padding: 10px;
        }

        .card-header {
            padding: 15px;
        }

        .search-wrapper .input-group {
            max-width: 100%;
        }

        .table thead th,
        .table tbody td {
            padding: 10px;
            font-size: 12px;
        }

        .btn-outline-primary {
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-outline-info {
            padding: 4px 6px;
            font-size: 10px;
        }

        .badge {
            font-size: 10px;
            padding: 4px 8px;
        }

        .pagination .page-link {
            padding: 6px 10px;
            font-size: 12px;
        }
    }
</style>
@endsection