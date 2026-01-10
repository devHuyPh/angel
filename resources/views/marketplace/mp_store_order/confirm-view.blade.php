@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>🚚 {{ trans('core/base::kho.confirm_delivery') }}: {{ $order->transaction_code }}</h4>
    </div>
    <div class="card-body">
      <p><strong>{{ trans('core/base::kho.to_store') }}:</strong> {{ $order->toStore->name }}</p>
      <p><strong>{{ trans('core/base::kho.total_amount') }}:</strong> {{ number_format($order->amount) }}đ</p>

      <h5>{{ trans('core/base::kho.product_list') }}:</h5>
      <ul>
        @foreach ($order->products as $item)
          <li>{{ $item->product->name ?? trans('core/base::kho.deleted_product') }} - {{ trans('core/base::kho.qty') }}: {{ $item->qty }}</li>
        @endforeach
      </ul>

      <form method="POST" action="{{ route('marketplace.vendor.store-orders.confirm', $order->id) }}">
        @csrf

        @if ($alternativeStores->count() > 0)
          <div class="mb-3">
            <label>👉 {{ trans('core/base::kho.change_from_store_optional') }}:</label>
            <select name="new_from_store" class="form-control">
              <option value="">{{ trans('core/base::kho.keep_current_store') }}</option>
              @foreach ($alternativeStores as $store)
                <option value="{{ $store->id }}">{{ $store->name }}</option>
              @endforeach
            </select>
          </div>
        @endif

        <button type="submit" class="btn btn-success">✔️ {{ trans('core/base::kho.confirm_delivery_btn') }}</button>
      </form>
    </div>
  </div>
@endsection
