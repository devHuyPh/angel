@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
  <div class="row">
    <div class="col-lg-12">
    <div class="main-form">
      <div class="card">
      <div class="card-header bg-primary text-white">
        <strong>
        <i class="ti ti-clipboard-check me-1"></i>
        {{ trans('core/base::kho.update_status') }} #{{ $storeOrder->transaction_code }} -
        {{ format_price($storeOrder->amount) }}
        </strong>
      </div>

      <div class="card-body">
        {{-- Danh sách sản phẩm --}}
        <div class="mb-4">
        <h6 class="text-uppercase fw-bold">{{ trans('core/base::kho.products_in_order') }}</h6>
        <div class="table-responsive">
          <table class="table table-bordered table-striped mb-0">
          <thead class="table-light">
            <tr>
            <th>{{ trans('core/base::kho.product_name') }}</th>
            <th class="text-center">{{ trans('core/base::kho.qty') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($storeOrder->products as $item)
        <tr>
        <td>{{ $item->product->name ?? trans('core/base::kho.deleted_product') }}</td>
        <td class="text-center">{{ $item->qty }}</td>
        </tr>
        @empty
        <tr>
        <td colspan="2" class="text-center text-muted">{{ trans('core/base::kho.no_product_in_order') }}
        </td>
        </tr>
        @endforelse
          </tbody>
          </table>
        </div>
        </div>

        {{-- Form cập nhật trạng thái --}}
        <form method="POST"
        action="{{ route('marketplace.vendor.store-orders.update', $storeOrder->transaction_code) }}"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
          <label for="status" class="form-label fw-semibold">{{ trans('core/base::kho.order_status') }}</label>
          <select name="status" id="status" class="form-control" required>
          @foreach($statusOptions as $key => $label)
        <option value="{{ $key }}" {{ $storeOrder->status == $key ? 'selected' : '' }}>
        {{ $label }}
        </option>
      @endforeach
          </select>
        </div>

        {{-- Ảnh minh chứng khi hoàn thành --}}
        <div class="form-group mb-3" id="completed-image-group" style="display: none;">
          <label for="completed_image"
          class="form-label fw-semibold">{{ trans('core/base::kho.completed_image') }}</label>
          <input type="file" name="completed_image" class="form-control">
          <small class="text-muted">{{ trans('core/base::kho.completed_required') }}</small>
        </div>

        @if ($storeOrder->completed_image)
      <div class="form-group mb-3">
        <label class="form-label fw-semibold">{{ trans('core/base::kho.previous_image') }}</label><br>
        <img src="{{ Storage::url($storeOrder->completed_image) }}" alt="Ảnh minh chứng"
        style="max-height: 200px;" class="img-thumbnail">
      </div>
      @endif

        <button type="submit" class="btn btn-primary">
          <i class="ti ti-check me-1"></i> {{ trans('core/base::kho.update_status_btn') }}
        </button>
        <a href="{{ route('marketplace.vendor.store-orders.index') }}" class="btn btn-secondary ms-2">
          <i class="ti ti-arrow-left me-1"></i> {{ trans('core/base::kho.back_list') }}
        </a>
        </form>
      </div>
      </div>
    </div>
    </div>
  </div>

  {{-- Script điều khiển hiển thị ảnh khi chọn trạng thái hoàn thành --}}
  <script>
    document.addEventListener("DOMContentLoaded", function () {
    const statusSelect = document.getElementById('status');
    const imageGroup = document.getElementById('completed-image-group');

    function toggleImageUpload() {
      imageGroup.style.display = statusSelect.value === 'completed' ? 'block' : 'none';
    }

    statusSelect.addEventListener('change', toggleImageUpload);
    toggleImageUpload(); // gọi khi trang vừa load
    });
  </script>
@endsection
