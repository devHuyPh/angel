@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
  @php
    $is_vendor = auth('customer')->check();
    $vendor = auth('customer')->user();
  @endphp

  @if ($is_vendor && $vendor)
    @php
      $allNotifications = $vendor->vendorNotifications;
      $perPage = 10;
      $currentPage = request()->get('page', 1);
      $offset = ($currentPage - 1) * $perPage;
      $pagedNotifications = $allNotifications->slice($offset, $perPage);
      $totalPages = ceil($allNotifications->count() / $perPage);
    @endphp

    @if ($pagedNotifications->isNotEmpty())
      <div class="list-group shadow-sm">
        @foreach ($pagedNotifications as $notification)
          @php
            $vars = json_decode($notification->variables, true) ?? [];
            $formattedVars = [];

            foreach ($vars as $key => $value) {
                if ($key === 'text_status_order') {
                    $formattedVars[$key] = match ($value) {
                        'pending' => trans('core/base::kho.status_pending'),
                        'completed' => trans('core/base::kho.status_completed'),
                        'processing' => trans('core/base::kho.status_processing'),
                        'shipping' => trans('core/base::kho.status_shipping'),
                        'delivered' => trans('core/base::kho.status_delivered'),
                        'cancelled' => trans('core/base::kho.status_cancelled'),
                        'unknown' => trans('core/base::kho.status_unknown'),
                        default => $value,
                    };
                } elseif (Str::startsWith($key, 'text_')) {
                    $formattedVars[$key] = trans($value);
                } elseif (
                    is_numeric($value)
                    && (
                        $key === 'amount'
                        || $key === 'price'
                        || $key === 'total'
                        || $key === 'fee'
                        || Str::endsWith($key, ['_amount', '_price', '_fee', '_total'])
                    )
                ) {
                    $formattedVars[$key] = format_price((float) $value);
                } elseif (Str::contains($key, 'date') || Str::endsWith($key, ['_at', '_date', '_time'])) {
                    $formattedVars[$key] = $value instanceof \Carbon\Carbon
                        ? $value->format('d/m/Y H:i')
                        : (string) $value;
                } else {
                    $formattedVars[$key] = $value;
                }
            }

            $translatedDescription = trans('core/base::layouts.' . $notification->description, $formattedVars);
          @endphp

          <a href="{{ route('notifications.redirect', ['id' => $notification->id]) }}" class="list-group-item list-group-item-action rounded-3 mb-3 border-0 shadow-sm p-3 position-relative
              @if ($notification->viewed == 0) bg-light border-start border-4 border-primary @else bg-white @endif">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <h5 class="mb-0 text-primary fw-semibold">
                <i class="bi bi-bell-fill me-2"></i> {{ trans($notification->title) }}
              </h5>
              <small class="text-muted">
                <i class="bi bi-clock me-1"></i> {{ $notification->created_at->format('d-m-Y H:i') }}
              </small>
            </div>
            <p class="mb-0 text-dark">{{ $translatedDescription }}</p>
          </a>
        @endforeach
      </div>

      {{-- Phân trang --}}
      @if ($totalPages > 1)
        <nav>
          <ul class="pagination justify-content-center mt-4">
            @for ($i = 1; $i <= $totalPages; $i++)
              <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
              </li>
            @endfor
          </ul>
        </nav>
      @endif
    @else
      <div class="alert alert-info text-center mt-4">
        <i class="bi bi-info-circle me-2"></i> Không có thông báo mới.
      </div>
    @endif
  @endif
@endsection

@push('footer')
  {{-- Hover effect --}}
  <style>
    .list-group-item:hover {
      background-color: #f0f8ff !important;
      transition: background-color 0.3s ease;
    }
  </style>
@endpush
