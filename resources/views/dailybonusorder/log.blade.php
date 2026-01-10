@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('core/base::layouts.daily_bonus_log')}}</h3>
                    </div>
                    <div class="card-body">
                        @if (empty($dailyBonusLogs) || $dailyBonusLogs->isEmpty())
                            <div class="alert alert-info" role="alert">
                                {{ trans('core/base::layouts.no_bonus_logs_found')}}
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>

                                            <th>{{ trans('core/base::layouts.customers_name')}}</th>
                                            <th>{{ trans('core/base::layouts.bonus_amount')}}</th>
                                            <th>{{ trans('core/base::layouts.order_total')}}</th>
                                            <th>{{ trans('core/base::layouts.walet')}}</th>
                                            <th>{{ trans('core/base::layouts.distribution_date')}}</th>
                                            <th>{{ trans('core/base::layouts.created_at')}}</th>
                                            <th>{{ trans('core/base::layouts.updated_at')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dailyBonusLogs as $log)
                                            <tr>
                                                <td><a href="{{ route('dailybonusorder.customerview',$log->customer->id) }}">{{ $log->customer->name }}</a></td>
                                                <td>{{ number_format($log->bonus_amount, 2) }}</td>
                                                <td>{{ number_format($log->order_total, 2) }}</td>
                                                <td>{{ number_format($log->customer->walet_1, 2) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($log->distribution_date)->format('Y-m-d') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($log->updated_at)->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $dailyBonusLogs->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <!-- Load Bootstrap CSS (already included in your code) -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
    <!-- Additional custom styles -->
    <style>
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .card-header {
            background-color: #f8f9fa;
        }
        .table-responsive {
            min-height: 300px;
        }
    </style>
@endpush

@push('script-lib')
    <!-- Optional: Add any JavaScript if needed (e.g., for table sorting/filtering) -->
    <script src="{{ asset('assets/admin/js/jquery-ui.min.js') }}"></script>
@endpush