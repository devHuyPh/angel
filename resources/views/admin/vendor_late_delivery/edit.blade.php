@php
    $store = $vendorLateDelivery->store;
    $storeId = $store->id ?? 0;
    $storeLabel = $store?->name ?: __('Công ty');
    $order = $vendorLateDelivery->order;
    $orderAddress = $order->address;

    // Danh sách sản phẩm cần so sánh: [ 'Tẩy đa năng' => 1, ... ]
    $requiredProducts = collect($order->products)->mapWithKeys(function ($product) {
        return [$product->product_name => $product->qty];
    });

    // Lấy danh sách store gần và lọc theo điều kiện đủ sản phẩm
    $listStoreNear = \Botble\Marketplace\Models\Store::with('products') // tránh N+1 query
        ->where('id', '!=', $storeId)
        ->where(function ($query) use ($orderAddress) {
            $query->where('city', $orderAddress->city)
                  ->orWhere('state', $orderAddress->state);
        })
        ->orderByRaw('city = ? desc', [$orderAddress->city])
        ->orderByRaw('state = ? desc', [$orderAddress->state])
        ->get()
        ->filter(function ($nearStore) use ($requiredProducts) {
            $products = $nearStore->products;

            foreach ($requiredProducts as $name => $requiredQty) {
                $matchingProduct = $products->first(function ($product) use ($name, $requiredQty) {
                    return $product->name === $name && $product->quantity >= $requiredQty;
                });

                if (! $matchingProduct) {
                    return false; // không đủ sản phẩm hoặc không trùng tên
                }
            }

            return true; // tất cả sản phẩm đều đủ
        })
        ->values(); // reset lại key
@endphp

<form action="{{ route('vendor-late-delivery.update', $order->token) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="modal fade modal-blur" id="updateVendorLate_{{ $vendorLateDelivery->id }}" tabindex="-1" role="dialog"
        data-select2-dropdown-parent="true">

        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-status bg-info"></div>

                <div class="modal-body text-center py-4">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>

                    <div class="mb-2">
                        <svg class="icon icon-lg text-info svg-icon-ti-ti-info-circle"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                            <path d="M12 9h.01"></path>
                            <path d="M11 12h1v4h1"></path>
                        </svg>
                    </div>

                    <h3>Chuyển tiếp đơn</h3>

                    <div class="text-muted text-wrap w-100">
                        Khi gán cho kho khác thực hiện giao hàng, kho được chỉ định khi hoàn thành sẽ nhận
                        số tiền tương ứng
                        với cấp kho của họ.
                    </div>
                </div>
                <div class="form-update  mb-3 mx-2 text-start">
                    <input type="text" hidden name="id" value="{{ $vendorLateDelivery->id }}">
                    <label for="store_id" class="form-group">{{ __('Chuyển kho') }}</label>
                    <select name="store_id" id="store_id" class="form-select">
                        <option value="none">{{ __('Công ty') }}</option>
                        @foreach ($listStoreNear as $storeNear)
                            <option value="{{ $storeNear->id }}">{{ $storeNear->name }} -
                                {{ $storeNear->fullAddress }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="w-100 btn btn-info form-control"
                                    id="confirm-payment-order-button">
                                    Xác nhận
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" class="w-100 btn btn- form-control" data-bs-dismiss="modal">
                                    Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
