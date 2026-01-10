@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="container-fluid p-3">
    <!-- Header -->
    <div class="section-header d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">{{ trans('core/base::layouts.daily-bonus-percentage') }}</h1>
      <div class="section-header-button">
        <a href="{{ route('dailybonusorder.edit') }}" class="btn btn-primary btn-sm">
          <i class="fa fa-edit"></i> {{ trans('core/base::layouts.edit') }}
        </a>
      </div>
    </div>

    <!-- Bonus Percentage Card -->
    <div class="card custom-card-style mb-4">
      <div class="card-body p-3">
        <div class="table-responsive">
          <table class="table table-hover table-striped table-bordered mb-0">
            <thead>
              <tr>
                <th>{{ trans('core/base::layouts.key') }}</th>
                <th class="text-end">{{ trans('core/base::layouts.value') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ trans('core/base::layouts.dailyBonus') }}</td>
                <td class="text-end text-primary fw-bold">{{ $dailyBonus }}%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Bonus Logs -->
    <div class="section-header mb-3">
      <h1 class="h4">{{ trans('core/base::layouts.daily_bonus_log') }}</h1>
    </div>

    <div class="card custom-card-style">
      <div class="card-body p-3">
        @if (empty($dailyBonusLogs) || $dailyBonusLogs->isEmpty())
          <div class="alert alert-info text-center p-3">
            {{ trans('core/base::layouts.no_bonus_logs_found') }}
          </div>
        @else
          <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
              <thead class="text-nowrap">
                <tr>
                  <th class="text-center">{{ trans('core/base::layouts.customers_name') }}</th>
                  <th class="text-center">{{ trans('core/base::layouts.bonus_amount') }}</th>
                  <th class="text-center">{{ trans('core/base::layouts.order_total') }}</th>
                  <th class="text-center">{{ trans('core/base::layouts.walet') }}</th>
                  <th class="text-center">{{ trans('core/base::layouts.distribution_date') }}</th>
                  <th class="text-center">{{ trans('core/base::layouts.daily_created_at') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($dailyBonusLogs as $log)
                  <tr>
                    <td class="text-center">
                      <a href="{{ route('dailybonusorder.customerview', $log->customer->id) }}" class="text-primary fw-medium">
                        {{ $log->customer->name }}
                      </a>
                    </td>
                    <td class="text-center">{{ number_format($log->bonus_amount, 2) }}</td>
                    <td class="text-center">{{ number_format($log->order_total, 2) }}</td>
                    <td class="text-center">{{ number_format($log->customer->walet_1, 2) }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($log->distribution_date)->format('Y-m-d') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-3 d-flex justify-content-center">
            {{ $dailyBonusLogs->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('style')
    <style>
        /* Mobile-first approach */
        body {
            font-size: 14px;
        }

        .container-fluid {
            padding: 15px;
        }

        .custom-card-style {
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .custom-card-style:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
        }

        .card-body {
            padding: 15px;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start !important;
            margin-bottom: 15px;
        }

        .section-header h1 {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        .section-header-button {
            margin-top: 10px;
        }

        .btn-primary {
            font-size: 0.875rem;
            padding: 8px 12px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            font-size: 0.85rem;
            min-width: 600px; /* Ensures table is scrollable on mobile */
        }

        .table th, .table td {
            padding: 8px;
            vertical-align: middle;
        }

        .table th {
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .table td {
            word-break: break-word;
        }

        .alert-info {
            background: #e6f4ff;
            color: #2b6cb0;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.9rem;
            text-align: center;
        }

        .fw-bold {
            font-weight: 700;
        }

        .fw-medium {
            font-weight: 500;
        }

        /* Pagination */
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
            font-size: 0.85rem;
        }

        .pagination .page-link {
            padding: 6px 12px;
            margin: 0 2px;
        }

        /* Media Queries for Larger Screens */
        @media (min-width: 768px) {
            body {
                font-size: 16px;
            }

            .container-fluid {
                padding: 20px;
            }

            .section-header {
                flex-direction: row;
                align-items: center !important;
            }

            .section-header h1 {
                font-size: 1.5rem;
            }

            .section-header-button {
                margin-top: 0;
            }

            .card-body {
                padding: 20px;
            }

            .table {
                font-size: 0.9rem;
            }

            .table th, .table td {
                padding: 12px;
            }

            .btn-primary {
                font-size: 1rem;
                padding: 10px 16px;
            }

            .pagination .page-link {
                padding: 8px 16px;
            }
        }
    </style>
@endpush