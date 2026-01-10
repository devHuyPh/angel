@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
<div class="row">
  <div class="col-12">
    <form action="{{ route('marketplace.vendor.store-orders.store') }}" method="POST">
      @csrf
      <input hidden type="text" name="to_store" value="{{ $store->id }}">

      <div class="card mb-3">
        <div class="card-header row">
          <div class="col-6">
            <label for="from_store" class="form-label">
              {{ trans('core/base::kho.order_from') }}:
            </label>
            <select name="from_store" id="from_store" class="form-control form-select">
              @if ($parentStores->isNotEmpty())
                @foreach ($parentStores as $parentStore)
                  @if ($parentStore->products->isNotEmpty())
                    <option value="{{ $parentStore->id }}" title="{{ $parentStore->full_address }}">
                      {{ $parentStore->name }} - {{ $parentStore->full_address }}
                    </option>
                  @endif
                @endforeach
              @else
                <option value="">{{ trans('core/base::kho.from_company') }}</option>
              @endif
            </select>
          </div>
          <div class="col-6 text-end">
            <button type="submit" class="btn">{{ trans('core/base::kho.create_order') }}</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-vcenter card-table table-hover table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>{{ trans('core/base::kho.image') }}</th>
                <th>{{ trans('core/base::kho.product_name') }}</th>
                <th>{{ trans('core/base::kho.import_price') }}</th>
                <th>{{ trans('core/base::kho.retail_price') }}</th>
                <th>{{ trans('core/base::kho.stock') }}</th>
                <th>{{ trans('core/base::kho.qty') }}</th>
              </tr>
            </thead>
            <tbody>
              @php
                $comission = $store?->storeLevel?->commission;
                $comission = $store?->storeLevel?->commission ?? 0;
              @endphp
              @forelse ($products as $product)
                <tr>
                  <td>{{ $product->id }}</td>
                  <td>
                    <img src="{{ RvMedia::getImageUrl($product->image, 'thumb') }}" width="50"
                      alt="{{ $product->name }}">
                  </td>
                  <td>{{ $product->name }}</td>
                  @php
                    $priceAfterCommission =  $product->price - ($product->price * $comission) / 100;

                    $importPrice = $comission != 0
                        ? $priceAfterCommission
                        : ($product->warehouse_price ?? $product->sale_price ?? $product->price);
                  @endphp
                  <td class="product-price" data-price="{{ $importPrice }}">
                    {{ format_price($importPrice) }}
                  </td>
                  <td>{{ format_price($product->sale_price ?? $product->price) }}</td>
                  <td>{{ $product->quantity }}</td>
                  <td>
                    <input class="form-control qty-input" type="number" name="{{ $product->id }}_qty" value="0">
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="100%" class="text-center text-muted py-4">
                    {{ trans('core/base::kho.no_products') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex align-items-center justify-content-between">
          <div>
            <strong>{{ trans('core/base::kho.total') }}: <span id="total-amount">0₫</span></strong>
            <input type="number" id="total-amount-ipn" name="amount" value="0" hidden>
          </div>
          <div>
            <button type="submit" class="btn">{{ trans('core/base::kho.create_order') }}</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qtyInputs = document.querySelectorAll('.qty-input');
            const totalDisplay = document.getElementById('total-amount');
            const totalInput = document.getElementById('total-amount-ipn');

            function calculateTotal() {
                let total = 0;

                qtyInputs.forEach(function(input) {
                    const row = input.closest('tr');
                    const priceElement = row.querySelector('.product-price');
                    const price = parseFloat(priceElement.dataset.price);
                    const qty = parseInt(input.value) || 0;

                    total += price * qty;
                });

                totalDisplay.textContent = new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(total);

                totalInput.value = total;
            }

            qtyInputs.forEach(function(input) {
                input.addEventListener('input', calculateTotal);
            });

            // Initial call in case inputs have preset values
            calculateTotal();
        });
    </script>
@endpush
