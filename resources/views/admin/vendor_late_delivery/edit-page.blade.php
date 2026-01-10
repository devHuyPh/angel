@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="row">
      <div class="col-lg-8">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title mb-0">{{ __('Chỉ định kho giao ưu tiên') }}</h4>
              </div>
              <div class="card-body">
                  <dl class="row">
                      <dt class="col-sm-4">{{ __('Mã đơn hàng') }}</dt>
                      <dd class="col-sm-8">{{ $order?->code }}</dd>

                      <dt class="col-sm-4">{{ __('Kho hiện tại') }}</dt>
                      <dd class="col-sm-8">{{ $vendorLateDelivery->store->name ?? __('Công ty') }}</dd>

                      <dt class="col-sm-4">{{ __('Địa chỉ giao') }}</dt>
                      <dd class="col-sm-8">
                          {{ $address?->address }}, {{ $address?->city_name }}, {{ $address?->state_name }}
                      </dd>

                      <dt class="col-sm-4">{{ __('Sản phẩm') }}</dt>
                      <dd class="col-sm-8">
                          <ul class="mb-0">
                              @foreach ($order?->products ?? [] as $product)
                                  <li>{{ $product->product_name }} (x{{ $product->qty }})</li>
                              @endforeach
                          </ul>
                      </dd>
                  </dl>

                  <form method="POST" action="{{ route('vendor-late-delivery.update', $order->token) }}">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="id" value="{{ $vendorLateDelivery->id }}">

                      <div class="mb-3">
                          <label for="store_id" class="form-label">{{ __('Chọn kho giao ưu tiên') }}</label>
                          <select name="store_id" id="store_id" class="form-select">
                              <option value="none" data-enough="{{ $storeHasEnough['none'] ? '1' : '0' }}">{{ __('Công ty') }}</option>
                              @if ($recommendedStores->isNotEmpty())
                                  <optgroup label="{{ __('Gợi ý gần nhất') }}">
                                      @foreach ($recommendedStores as $store)
                                          <option value="{{ $store->id }}" data-enough="{{ $storeHasEnough[$store->id] ? '1' : '0' }}">
                                              {{ $store->name }} - {{ $store->full_address }}
                                              @unless ($storeHasEnough[$store->id]) ({{ __('thiếu hàng') }}) @endunless
                                          </option>
                                      @endforeach
                                  </optgroup>
                              @endif
                              @if ($otherStores->isNotEmpty())
                                  <optgroup label="{{ __('Kho khác') }}">
                                      @foreach ($otherStores as $store)
                                          <option value="{{ $store->id }}" data-enough="{{ $storeHasEnough[$store->id] ? '1' : '0' }}">
                                              {{ $store->name }} - {{ $store->full_address }}
                                              @unless ($storeHasEnough[$store->id]) ({{ __('thiếu hàng') }}) @endunless
                                          </option>
                                      @endforeach
                                  </optgroup>
                              @endif
                          </select>
                          <small class="text-muted d-block">{{ __('Ưu tiên kho cùng quận/huyện/tỉnh và đủ hàng, vẫn có thể chọn kho khác nếu cần.') }}</small>
                          <small id="inventory-note" class="text-muted"></small>
                      </div>

                      <button type="submit" class="btn btn-primary" id="submit-btn">{{ __('Cập nhật') }}</button>
                      <a href="{{ route('vendor-late-delivery.index') }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                  </form>
              </div>
          </div>
      </div>

      <div class="col-lg-4">
          <div class="card">
              <div class="card-header">
                  <h5 class="card-title mb-0">{{ __('Tồn kho kho đang chọn') }}</h5>
              </div>
              <div class="card-body">
                  <div id="inventory-panel">
                      <p class="text-muted mb-0">{{ __('Chọn kho để xem tồn kho từng sản phẩm.') }}</p>
                  </div>
              </div>
          </div>
      </div>
  </div>

  @push('footer')
    <script>
        (function () {
            const inventories = @json($storeInventories);
            const hasEnough = @json($storeHasEnough);

            const select = document.getElementById('store_id');
            const panel = document.getElementById('inventory-panel');
            const submitBtn = document.getElementById('submit-btn');
            const note = document.getElementById('inventory-note');

            function renderInventory(storeId) {
                const items = inventories[storeId] || [];
                if (!items.length) {
                    panel.innerHTML = '<p class=\"text-muted mb-0\">{{ __('Không có dữ liệu tồn kho.') }}</p>';
                    return;
                }

                const rows = items.map(item => {
                    const status = item.enough
                        ? '<span class=\"badge bg-success-subtle text-success fw-semibol\">{{ __('Đủ') }}</span>'
                        : '<span class=\"badge bg-danger-subtle text-danger fw-semibold\">{{ __('Thiếu') }}</span>';
                    return `
                        <tr>
                            <td>${item.name}</td>
                            <td class=\"text-center\">${item.required}</td>
                            <td class=\"text-center\">${item.available}</td>
                            <td class=\"text-center\">${status}</td>
                        </tr>
                    `;
                }).join('');

                panel.innerHTML = `
                    <div class=\"table-responsive\">
                        <table class=\"table table-sm mb-0\">
                            <thead>
                                <tr>
                                    <th>{{ __('Sản phẩm') }}</th>
                                    <th class=\"text-center\">{{ __('Cần') }}</th>
                                    <th class=\"text-center\">{{ __('Tồn') }}</th>
                                    <th class=\"text-center\">{{ __('Trạng thái') }}</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                `;
            }

            function updateState() {
                const value = select.value || 'none';
                const enough = hasEnough[value] ?? false;
                renderInventory(value);
                if (!enough) {
                    note.textContent = '{{ __('Kho này không đủ hàng cho toàn bộ sản phẩm, không thể chuyển.') }}';
                    submitBtn.setAttribute('disabled', 'disabled');
                } else {
                    note.textContent = '';
                    submitBtn.removeAttribute('disabled');
                }
            }

            select.addEventListener('change', updateState);
            updateState();
        })();
    </script>
  @endpush
@endsection
