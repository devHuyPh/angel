@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
      <div>
        <h3 class="mb-1">
          <i class="fas fa-warehouse me-2"></i>{{ $store->name }}
        </h3>
        <div class="text-muted">
          {{ $store->customer?->name ?? trans('core/base::layouts.n_a') }}
          @if ($store->customer?->phone)
            &middot; {{ $store->customer->phone }}
          @endif
        </div>
      </div>

      <a href="{{ route('store-levels.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>{{ trans('core/base::layouts.back') }}
      </a>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-light border-0 py-2">
            <h5 class="mb-0">{{ __('Thông tin kho') }}</h5>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">{{ trans('core/base::layouts.store_name') }}</span>
              <span class="fw-semibold text-dark">{{ $store->name }}</span>
            </div>
            
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">{{ __('Tổng đơn hoàn tất') }}</span>
              <span class="fw-semibold text-dark">{{ number_format($orders->total()) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">{{ __('Sản phẩm trong kho') }}</span>
              <span class="fw-semibold text-dark">{{ number_format($products->count()) }}</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">{{ __('Giá trị kho hàng') }}</span>
              <span class="fw-semibold text-primary">{{ format_price($inventoryValue) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-light border-0 py-2 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Tài khoản ngân hàng') }}</h5>
            <span class="badge bg-info text-dark text-uppercase">{{ str_replace('_', ' ', $paymentChannel) }}</span>
          </div>

          @if (! empty($bankInfo))
            <ul class="list-group list-group-flush">
              @foreach ($bankInfo as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span class="text-muted">{{ $item['label'] }}</span>
                  <span class="fw-semibold text-dark text-end">{{ $item['value'] }}</span>
                </li>
              @endforeach
            </ul>
          @else
            <div class="card-body">
              <div class="text-muted">{{ trans('core/base::layouts.no_data') }}</div>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="card shadow-sm mb-3">
      <div class="card-header bg-light border-0 py-2">
        <h5 class="mb-0">{{ __('Danh sách đơn') }}</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>{{ __('Mã đơn') }}</th>
                <th class="text-end">{{ __('Tổng tiền') }}</th>
                <th>{{ __('Trạng thái') }}</th>
                <th>{{ __('Ngày tạo') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($orders as $order)
                <tr>
                  <td class="fw-semibold">{{ $order->code }}</td>
                  <td class="text-end">{{ format_price($order->amount) }}</td>
                  <td>
    {!! $order->status ? $order->status->toHtml() : trans('core/base::layouts.n_a') !!}
</td>

                  <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-3">
                    {{ trans('core/base::layouts.no_data') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if ($orders->hasPages())
        <div class="card-footer">
          {{ $orders->links() }}
        </div>
      @endif
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-light border-0 py-2">
        <h5 class="mb-0">{{ __('Sản phẩm trong kho') }}</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>{{ __('Tên sản phẩm') }}</th>
                <th>{{ __('SKU') }}</th>
                <th class="text-end">{{ __('Số lượng') }}</th>
                <th class="text-end">{{ __('Giá') }}</th>
                <th class="text-end">{{ __('Thành tiền') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($products as $product)
                @php
                  $productPrice = $product->front_sale_price ?? $product->price ?? 0;
                  $productValue = (float) ($product->quantity ?? 0) * (float) $productPrice;
                @endphp
                <tr>
                  <td class="fw-semibold">{{ $product->name }}</td>
                  <td>{{ $product->sku ?: trans('core/base::layouts.n_a') }}</td>
                  <td class="text-end">{{ number_format($product->quantity ?? 0) }}</td>
                  <td class="text-end">{{ format_price($productPrice) }}</td>
                  <td class="text-end">{{ format_price($productValue) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-3">
                    {{ trans('core/base::layouts.no_data') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
