@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0">{{ trans('core/base::kho.change_from_store_for_order') }}: {{ $order->transaction_code }}</h4>
      <a href="{{ route('admin.store-orders.expired-pending') }}"
      class="btn btn-light btn-sm">{{ trans('core/base::kho.back') }}</a>
    </div>

    <div class="card-body p-4">
      {{-- Kho gửi & Kho nhận --}}
      <div class="mb-5">
      <h5 class="fw-bold mb-3">{{ trans('core/base::kho.from_and_to_store') }}</h5>
      <div class="row align-items-center text-center">
        {{-- Nơi gửi --}}
        <div class="col-md-4">
        <div class="p-3 bg-light rounded-3 shadow-sm">
          <i class="fas fa-industry fa-3x mb-2 text-primary"></i>
          <div class="fw-semibold">{{ $order->fromStore->name ?? trans('core/base::kho.factory') }}</div>
          <small
          class="text-muted">{{ $order->fromStore->storeLevel->name ?? trans('core/base::kho.no_level') }}</small>
        </div>
        </div>

        {{-- Mũi tên --}}
        <div class="col-md-1 d-flex justify-content-center">
        <i class="fas fa-arrow-right fa-2x text-muted"></i>
        </div>

        {{-- Nơi nhận --}}
        <div class="col-md-4">
        <div class="p-3 bg-light rounded-3 shadow-sm">
          <i class="fas fa-warehouse fa-3x mb-2 text-success"></i>
          <div class="fw-semibold">{{ $order->toStore->name ?? trans('core/base::kho.unknown') }}</div>
        </div>
        </div>
      </div>
      </div>

      {{-- Thông tin đơn hàng --}}
      <div class="mb-5">
      <h5 class="fw-bold mb-3">{{ trans('core/base::kho.order_info') }}</h5>
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
        <tbody>
          <tr>
          <th class="bg-light" style="width: 200px;">{{ trans('core/base::kho.order_code') }}</th>
          <td>{{ $order->transaction_code }}</td>
          </tr>
          <tr>
          <th class="bg-light">{{ trans('core/base::kho.current_from_store') }}</th>
          <td>{{ $order->fromStore->name ?? '—' }}
            ({{ $order->fromStore->storeLevel->name ?? trans('core/base::kho.no_level') }})</td>
          </tr>
          <tr>
          <th class="bg-light">{{ trans('core/base::kho.to_store') }}</th>
          <td>{{ $order->toStore->name ?? trans('core/base::kho.unknown') }}</td>
          </tr>
          <tr>
          <th class="bg-light">{{ trans('core/base::kho.status') }}</th>
          @php
        $status = $order->status ?? 'unknown';
        $statusMap = [
        'completed' => ['class' => 'bg-success', 'text' => trans('core/base::kho.status_completed')],
        'pending' => ['class' => 'bg-warning', 'text' => trans('core/base::kho.status_pending')],
        'processing' => ['class' => 'bg-info', 'text' => trans('core/base::kho.status_processing')],
        'shipping' => ['class' => 'bg-info', 'text' => trans('core/base::kho.status_shipping')],
        'delivered' => ['class' => 'bg-info', 'text' => trans('core/base::kho.status_delivered')],
        'cancelled' => ['class' => 'bg-danger', 'text' => trans('core/base::kho.status_cancelled')],
        'unknown' => ['class' => 'bg-secondary', 'text' => trans('core/base::kho.status_unknown')],
        ];
        $statusData = $statusMap[$status] ?? $statusMap['unknown'];
      @endphp
          <td>
            <span class="badge {{ $statusData['class'] }} text-white">{{ $statusData['text'] }}</span>
          </td>
          </tr>
          <tr>
          <th class="bg-light">{{ trans('core/base::kho.created_at') }}</th>
          <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
          </tr>
          <tr>
          <th class="bg-light">{{ trans('core/base::kho.total_amount') }}</th>
          <td>{{ number_format($order->amount) }} đ</td>
          </tr>
          <tr>
          <th class="bg-light">{{ trans('core/base::kho.payment_status') }}</th>
          <td>{{ ucfirst($order->payment_status ?? 'unknown') }}</td>
          </tr>
        </tbody>
        </table>
      </div>
      </div>

      {{-- Form đổi kho gửi --}}
      <form action="{{ route('admin.store-orders.update-from-store', $order->id) }}" method="POST"
      class="needs-validation" novalidate>
      @csrf
      <div class="mb-4">
        <label for="new_from_store"
        class="form-label fw-bold">{{ trans('core/base::kho.choose_new_from_store') }}</label>
        <select name="new_from_store" id="new_from_store" class="form-select" required>
        <option value="">{{ trans('core/base::kho.choose_store') }}</option>
        @foreach ($availableStores as $store)
      <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->storeLevel->name }})</option>
      @endforeach
        </select>
        <div class="invalid-feedback">
        {{ trans('core/base::kho.please_choose_from_store') }}
        </div>
      </div>

      <div class="d-flex gap-3">
        <button type="submit" class="btn btn-primary px-4">
        <i class="fas fa-check me-2"></i>{{ trans('core/base::kho.update_from_store') }}
        </button>
        <button type="submit" class="btn btn-outline-secondary px-4"
        onclick="event.preventDefault(); document.getElementById('sendFromFactoryForm').submit();"
        data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ trans('core/base::kho.send_from_factory_tooltip') }}">
        <i class="fas fa-industry me-2"></i>{{ trans('core/base::kho.send_from_factory') }}
        </button>
      </div>
      </form>

      {{-- Form ẩn gửi từ nhà máy --}}
      <form id="sendFromFactoryForm" action="{{ route('admin.store-orders.update-from-store', $order->id) }}"
      method="POST" style="display: none;">
      @csrf
      <input type="hidden" name="new_from_store" value="">
      </form>
    </div>
    </div>
  </div>

  {{-- Bootstrap validation --}}
  <script>
    (function () {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
      form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
      }, false);
    });
    })();
  </script>

  {{-- Tooltip --}}
  <script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  </script>
@endsection
