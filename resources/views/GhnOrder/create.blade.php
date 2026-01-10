
@if ($shipment->ghn_created == 0)
    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
    data-bs-target="#orderModal">
        Tạo đơn hàng GHN
    </button>

    <div class="text-start">
    <!-- Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1"
    aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="orderModalLabel">Tạo đơn hàng GHN</h4>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form id="orderForm">
            <div class="modal-body">
                
                    @php
                        $parts = explode(',', $storeLocator->address);
                        $from_ward = trim(end($parts));
                    @endphp
                    <input type="text" hidden id="ghn_token" value="{{setting('shipping_ghn_token')}}">
                    <input type="text" hidden id="ghn_shop_id" value="{{setting('shipping_ghn_shop_id')}}">
                    
                    <h4 class="card-title">Thông tin người gửi</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="from_name" class="form-label">Tên người gửi *</label>
                            <input type="text" class="form-control" id="from_name" name="from_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="from_phone" class="form-label">SĐT người gửi *</label>
                            <input type="text" class="form-control" id="from_phone" name="from_phone" value="{{setting('ecommerce_store_phone')}}" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="from_district" class="form-label">Địa chỉ *</label>
                            <input class="form-control" required type="text" id="from_address" name="from_address" value="{{$storeLocator->full_address}}">
                        </div>

                    </div>

                    <!-- Chọn tỉnh, quận, phường của người gửi -->
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control" hidden id="from_province" name="from_province" value="{{$storeLocator->state_name}}">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" hidden id="from_district" name="from_district" value="{{$storeLocator->city_name}}">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" hidden id="from_ward" name="from_ward" value="{{$from_ward}}">
                        </div>
                    </div>

                    <hr>
                    <!-- Người nhận -->
                    @php
                        $address = $shipment->order->address;
                        // dd($address);
                        $parts = explode(',', $address->address);
                        $to_ward = trim(end($parts));
                        // dd($to_ward);
                    @endphp
                    <h4 class="card-title">Thông tin người nhận</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="to_name" class="form-label">Tên người nhận *</label>
                            <input type="text" class="form-control" id="to_name" name="to_name" value="{{$address->name}}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="to_phone" class="form-label">SĐT người nhận *</label>
                            <input type="text" class="form-control" id="to_phone" name="to_phone" value="{{$address->phone}}" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="to_address" class="form-label">Địa chỉ nhận *</label>
                            <input type="text" required class="form-control" id="to_address" name="to_address" value="{{$address->address.', '.$address->city_name.', '.$address->state_name}}" required>
                        </div>
                    </div>

                    <!-- Chọn tỉnh, quận, phường của người nhận -->
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control" hidden id="to_province" name="to_province" value="{{$address->state_name}}" type="text">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" hidden id="to_district" name="to_district" value="{{$address->city_name}}" type="text">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" hidden id="to_ward" name="to_ward" value="{{$to_ward}}" type="text">
                        </div>
                    </div>

                    <hr>

                    <!-- Thông tin đơn hàng -->
                    <h4 class="card-title">Thông tin đơn hàng</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="cod_amount" class="form-label">Tiền thu hộ (COD) *</label>
                            @if ($shipment->order->payment->payment_channel == 'cod')
                                <input type="number" class="form-control" id="cod_amount" value="{{$shipment->order->payment->amount}}" name="cod_amount" required>
                            @else
                                <input type="number" class="form-control" id="cod_amount" name="cod_amount">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label for="service_type_id" class="form-label">Loại dịch vụ *</label>
                            <select class="form-control" id="service_type_id" name="service_type_id" required>
                                <option value="2">Hàng nhẹ</option>
                                <option value="5">Hàng nặng</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="payment_type_id" class="form-label">Ai trả phí? *</label>
                            <select class="form-control" id="payment_type_id" name="payment_type_id">
                                <option value="1">Người gửi</option>
                                <option value="2">Người nhận</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <!-- Thông tin sản phẩm -->
                    <h4 class="card-title">Sản phẩm</h4>
                    @foreach ($shipment->order->products as $orderProduct)
                    @php
                        $product = $orderProduct->product->original_product;
                        // dd($product->id);
                    @endphp
                        <div class="row">
                            <div class="col-md-4">
                                <label for="item_name"
                                    class="form-label">Tên sản phẩm</label>
                                <input type="text" class="form-control"
                                    id="item_name" name="{{$product->id.'_item_name'}}" value="{{$product->name}}">
                            </div>
                            <div class="col-md-4">
                                <label for="item_code" class="form-label">Mã
                                    sản phẩm</label>
                                <input type="text" class="form-control"
                                    id="item_code" name="{{$product->id.'_item_code'}}" value="{{$product->sku}}">
                            </div>
                            <div class="col-md-4">
                                <label for="item_quantity"
                                    class="form-label">Số lượng</label>
                                <input type="number" class="form-control"
                                    id="item_quantity" name="{{$product->id.'_item_quantity'}}" value="{{ $orderProduct->qty }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="number" hidden class="form-control" id="length" name="{{$product->id.'_length'}}" value="{{$product->length}}" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" hidden class="form-control" id="width" name="{{$product->id.'_wide'}}" value="{{$product->wide}}" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" hidden class="form-control" id="height" name="{{$product->id.'_height'}}" value="{{$product->height}}" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" hidden class="form-control" id="weight" name="{{$product->id.'_weight'}}" value="{{$product->weight}}" required>
                            </div>
                        </div>
                    @endforeach
                    <hr>

                    <!-- Mã đơn hàng riêng -->
                    <h4 class="card-title">Thông tin thêm</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="client_order_code" class="form-label">Mã đơn hàng riêng (Tùy chọn)</label>
                            <input type="text" class="form-control" id="client_order_code" placeholder="Nhập mã đơn hàng riêng" value="{{$shipment->order->code}}">
                        </div>
                        <div class="col-md-4">
                            <label for="pick_shift" class="form-label">Ca lấy hàng *</label>
                            <select type="text" class="form-control" id="pick_shift">
                                <option value="">Chọn ca lấy hàng</option>
                                <option value="2">Ca hôm nay (12h00 - 18h00)</option>
                                <option value="3">Ca sáng mai (7h00 - 12h00)</option>
                                <option value="4">Ca chiều mai (12h00 - 18h00)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="required_note" class="form-label">Ghi chú bắt buộc *</label>
                            <select required class="form-control" id="required_note">
                                <option value="">Ghi chú bắt buộc</option>
                                <option value="CHOTHUHANG">Cho thử hàng</option>
                                <option value="CHOXEMHANGKHONGTHU">Cho xem hàng không thử</option>
                                <option value="KHONGCHOXEMHANG">Không cho xem hàng</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="" id="note" cols="30" rows="5"></textarea>
                        </div>
                    </div>
                    <input type="text" hidden id="shipment_id" value="{{$shipment->id}}">
                
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary" name="createOrderGhn" id="submitOrder">Tạo đơn hàng</button>
            </div>
        </form>
        </div>
    </div>
    </div>
    </div>
@else
{{-- eidt ghn --}}
<div class="text-start">
    <!-- Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1"
    aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="orderModalLabel">Chỉnh sửa đơn hàng GHN</h4>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="orderForm">
                    @php
                        $parts = explode(',', $storeLocator->address);
                        $from_ward = trim(end($parts));
                    @endphp
                    <input type="text" hidden id="ghn_token" value="{{setting('shipping_ghn_token')}}">
                    <input type="text" hidden id="ghn_shop_id" value="{{setting('shipping_ghn_shop_id')}}">
                    
                    <h4 class="card-title">Thông tin người gửi</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="from_name" class="form-label">Tên người gửi</label>
                            <input type="text" class="form-control" id="from_name" name="from_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="from_phone" class="form-label">SĐT người gửi</label>
                            <input type="text" class="form-control" id="from_phone" name="from_phone" value="{{setting('ecommerce_store_phone')}}" required>
                        </div>
                        <div class="col-md-12">
                            <label for="from_district" class="form-label mt-3">Địa chỉ</label>
                            <input class="form-control" type="text" id="from_address" name="from_address" value="{{$storeLocator->full_address}}">
                        </div>

                    </div>

                    <!-- Chọn tỉnh, quận, phường của người gửi -->
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control" hidden id="from_province" name="from_province" value="{{$storeLocator->state_name}}">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" hidden id="from_district" name="from_district" value="{{$storeLocator->city_name}}">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" hidden id="from_ward" name="from_ward" value="{{$from_ward}}">
                        </div>
                    </div>

                    <hr>
                    <!-- Người nhận -->
                    @php
                        $address = $shipment->order->address;
                        // dd($address->city_name);
                        $parts = explode(',', $address->address);
                        $to_ward = trim(end($parts));
                        // dd($to_ward);
                    @endphp
                    <h4 class="card-title">Thông tin người nhận</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="to_name" class="form-label">Tên người nhận</label>
                            <input type="text" class="form-control" id="to_name" name="to_name" value="{{$address->name}}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="to_phone" class="form-label">SĐT người nhận</label>
                            <input type="text" class="form-control" id="to_phone" name="to_phone" value="{{$address->phone}}" required>
                        </div>
                        <div class="col-md-12">
                            <label for="to_address" class="form-label mt-3">Địa chỉ nhận</label>
                            <input type="text" class="form-control" id="to_address" name="to_address" value="{{$address->address.', '.$address->city_name.', '.$address->state_name}}" required>
                        </div>
                    </div>

                    <!-- Chọn tỉnh, quận, phường của người nhận -->
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control" hidden id="to_province" name="to_province" value="{{$address->state_name}}" type="text">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" hidden id="to_district" name="to_district" value="{{$address->city_name}}" type="text">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" hidden id="to_ward" name="to_ward" value="{{$to_ward}}" type="text">
                        </div>
                    </div>

                    <hr>

                    <!-- Thông tin đơn hàng -->
                    <h4 class="card-title">Thông tin đơn hàng</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="cod_amount" class="form-label">Tiền thu hộ (COD)</label>
                            <input type="number" class="form-control" id="cod_amount" name="cod_amount" required>
                        </div>
                        <div class="col-md-4">
                            <label for="service_type_id" class="form-label">Loại dịch vụ</label>
                            <select class="form-control" id="service_type_id" name="service_type_id">
                                <option value="2">Hàng nhẹ</option>
                                <option value="5">Hàng nặng</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="payment_type_id" class="form-label">Ai trả phí?</label>
                            <select class="form-control" id="payment_type_id" name="payment_type_id">
                                <option value="1">Người gửi</option>
                                <option value="2">Người nhận</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <!-- Thông tin sản phẩm -->
                    <h4 class="card-title">Sản phẩm</h4>
                    @foreach ($shipment->order->products as $index => $orderProduct)
                        @php
                            $product = $orderProduct->product->original_product;
                        @endphp
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Tên sản phẩm</label>
                                <input type="text" class="form-control" name="{{$index}}_item_name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mã sản phẩm</label>
                                <input type="text" class="form-control" name="{{$index}}_item_code">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số lượng</label>
                                <input type="number" class="form-control" name="{{$index}}_item_quantity">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="number" hidden class="form-control" name="{{$index}}_length" >
                            </div>
                            <div class="col-md-3">
                                <input type="number" hidden class="form-control" name="{{$index}}_wide" >
                            </div>
                            <div class="col-md-3">
                                <input type="number" hidden class="form-control" name="{{$index}}_height" >
                            </div>
                            <div class="col-md-3">
                                <input type="number" hidden class="form-control" name="{{$index}}_weight" >
                            </div>
                        </div>
                    @endforeach                
                    <hr>

                    <!-- Mã đơn hàng riêng -->
                    <h4 class="card-title">Thông tin thêm</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Mã đơn hàng riêng (Tùy chọn)</label>
                            <input type="text" class="form-control" id="client_order_code" placeholder="Nhập mã đơn hàng riêng" value="">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ca lấy hàng</label>
                            <select type="text" class="form-control" id="pick_shift">
                                <option value="2">Ca 2</option>
                                <option value="3">Ca 3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ghi chú bắt buộc</label>
                            <select type="text" class="form-control" id="required_note">

                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label mt-3">Ghi chú</label>
                            <textarea class="form-control" name="" id="note" cols="30" rows="5"></textarea>
                        </div>
                    </div>
                    <input type="text" hidden id="shipment_id" value="{{$shipment->id}}">
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" name="createOrderGhn" id="submitOrderUpdate">Lưu</button>
            </div>
        </div>
    </div>
    </div>
</div>

    <input type="text" hidden id="order_codes" value="{{$shipment->ghn_code}}">
    <input type="text" hidden id="shipment_id" value="{{$shipment->id}}">
    <input type="text" hidden id="ghn_token" value="{{setting('shipping_ghn_token')}}">
    <input type="text" hidden id="ghn_shop_id" value="{{setting('shipping_ghn_shop_id')}}">
@endif

<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#submitOrder').click(function (event) {
        event.preventDefault();
        // Lấy dữ liệu từ form
        let token = $('#ghn_token').val();
        let shop_id = $('#ghn_shop_id').val();
        let shipment_id = $('#shipment_id').val(); // Lấy ID của shipment cần cập nhật

        let orderData = {
            payment_type_id: parseInt($('#payment_type_id').val()),
            note: $('#note').val(),
            required_note: $('#required_note').val(),
            from_name: $('#from_name').val(),
            from_phone: $('#from_phone').val(),
            from_address: $('#from_address').val(),
            from_ward_name: $('#from_ward').val(),
            from_district_name: $('#from_district').val(),
            from_province_name: $('#from_province').val(),
            to_name: $('#to_name').val(),
            to_phone: $('#to_phone').val(),
            to_address: $('#to_address').val(),
            to_ward_name: $('#to_ward').val(),
            to_district_name: $('#to_district').val(),
            to_province_name: $('#to_province').val(),
            pick_shift: $('#pick_shift').val(),
            pick_shift: [parseInt($('#pick_shift').val())], 
            cod_amount: parseInt($('#cod_amount').val()),
            service_type_id: parseInt($('#service_type_id').val()),
            length: parseInt($('#length').val()),
            width: parseInt($('#width').val()),
            height: parseInt($('#height').val()),
            weight: parseInt($('#weight').val()),
            client_order_code: $('#client_order_code').val(),
            items: []
        };

        // Lấy danh sách sản phẩm
        let totalWeight = 0;
        let maxLength = 0;
        let maxWidth = 0;
        let totalHeight = 0;

        $('input[id^="item_name"]').each(function () {
            let id = $(this).attr('name').split('_')[0];
            let weight = parseInt($(`input[name='${id}_weight']`).val());
            let length = parseInt($(`input[name='${id}_length']`).val());
            let width = parseInt($(`input[name='${id}_wide']`).val());
            let height = parseInt($(`input[name='${id}_height']`).val());

            let item = {
                name: $(this).val(),
                code: $(`input[name='${id}_item_code']`).val(),
                quantity: parseInt($(`input[name='${id}_item_quantity']`).val()),
                price: 200000,
                length: length,
                width: width,
                height: height,
                weight: weight,
                category: {
                    level1: "Hàng hóa"
                }
            };

            orderData.items.push(item);

            // Tính tổng weight (gram)
            totalWeight += weight;

            // Lấy giá trị lớn nhất của length và width
            maxLength = Math.max(maxLength, length);
            maxWidth = Math.max(maxWidth, width);

            // Tổng chiều cao của tất cả sản phẩm
            totalHeight += height;
        });

        // Cập nhật lại giá trị tổng hợp vào `orderData`
        orderData.length = maxLength;
        orderData.width = maxWidth;
        orderData.height = totalHeight;
        orderData.weight = totalWeight;

        // Log dữ liệu trước khi gửi
        // console.log("Dữ liệu gửi đi:", JSON.stringify(orderData, null, 2));

        // Gửi yêu cầu tạo đơn hàng đến GHN
        $.ajax({
            url: 'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'Token': token,
                'ShopId': shop_id
            },
            data: JSON.stringify(orderData),
            success: function (response) {
                if (response.code === 200) {
                    showAlert('success', 'Tạo đơn hàng thành công')
                    
                    // Lấy dữ liệu từ phản hồi GHN để gửi đến server của bạn
                    let updateData = {
                        ghn_created: true,
                        ghn_code: response.data.order_code,
                        estimate_date_shipped: response.data.expected_delivery_time
                    };
                    // console.log("GHN Code:", updateData.ghn_code, "Estimate Date:", updateData.estimate_date_shipped);

                    // Gửi yêu cầu cập nhật shipment đến server của bạn
                    $.ajax({
                        url: `/ghn/update-shipment/${shipment_id}`,
                        type: 'POST',
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: JSON.stringify(updateData),
                        success: function (res) {
                            showAlert('success', 'Cập nhật đơn hàng thành công')
                            setTimeout(function () {
                                window.location.reload();
                            }, 2500);
                        },
                        error: function (err) {
                            console.error("Lỗi khi cập nhật shipment:", err);
                            showAlert('error', 'Có lỗi xảy ra khi cập nhật đơn hàng!', err.responseJSON || err);
                        }
                    });
                    // console.log();
                    
                }
            },
            error: function (error) {
                let errorMessage = "Có lỗi xảy ra khi tạo đơn hàng!";
                let errorData = error.responseJSON ? error.responseJSON : error; // Lấy dữ liệu lỗi nếu có
                showAlert('error', errorMessage, errorData.code_message_value);
            }
        });
    });

    $('#ghnCancel').click(function (){
        let token = $('#ghn_token').val();
        let shop_id = $('#ghn_shop_id').val();
        let order_codes = $('#order_codes').val();
        let shipment_id = $('#shipment_id').val();

        $.ajax({
            url: 'https://online-gateway.ghn.vn/shiip/public-api/v2/switch-status/cancel',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'Token': token,
                'ShopId': shop_id
            },
            data: JSON.stringify({ "order_codes": [`${order_codes}`] }),
            success: function (response) {
                if (response.code === 200) {
                    $.ajax({
                        url: `/ghn/cancel-order/${shipment_id}`,
                        type: 'POST',
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            // console.log("Hủy đơn hàng thành công:", res.data);

                            // Gọi alert thành công
                            showAlert('success', 'Hủy đơn hàng thành công!');

                            // Reload lại trang sau 3 giây
                            setTimeout(function () {
                                window.location.reload();
                            }, 2500);
                        },
                        error: function (err) {
                            console.error("Lỗi khi cập nhật shipment:", err);
                            showAlert('error', 'Có lỗi xảy ra khi cập nhật đơn hàng!', err.responseJSON || err);
                        }
                    });
                }
            },
            error: function (error) {
                console.error("Lỗi khi hủy đơn trên GHN:", error);
                showAlert('error', 'Lỗi từ hệ thống GHN, vui lòng thử lại sau!', error.responseJSON || error);
            }

        });



    });

    function showAlert(type, message, errorData = null) {
        let alertBox = type === 'success' ? $("#ghn-success-alert") : $("#ghn-error-alert");

        let content = `<strong>${message}</strong>`;
        
        if (errorData) {
            content += `<br><small>${JSON.stringify(errorData, null, 2)}</small>`;
        }

        alertBox.find("div").html(content);

        alertBox.fadeIn().delay(10000).fadeOut(400, function() {
            $(this).css("display", "none").attr("style", "display: none !important;");
        });
    }

    $("#ghn-update-order").click(function () {
        let order_code = $("#order_codes").val();
        let token = $("#ghn_token").val();

        if (!order_code) {
            alert("Không tìm thấy mã đơn hàng!");
            return;
        }

        // Gọi API để lấy chi tiết đơn hàng từ GHN
        $.ajax({
            url: "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail",
            type: "POST",
            contentType: "application/json",
            headers: {
                "Token": token
            },
            data: JSON.stringify({ "order_code": order_code }),
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    // console.log(data);
                    
                    
                    // **Tách địa chỉ FROM**
                    let fromAddress = splitAddress(data.from_address);
                    $("#from_name").val(data.from_name);
                    $("#from_phone").val(data.from_phone);
                    $("#from_address").val(data.from_address);
                    $("#from_ward").val(fromAddress.ward);
                    $("#from_district").val(fromAddress.district);
                    $("#from_province").val(fromAddress.province);

                    // **Tách địa chỉ TO**
                    let toAddress = splitAddress(data.to_address);
                    $("#to_name").val(data.to_name);
                    $("#to_phone").val(data.to_phone);
                    $("#to_address").val(data.to_address);
                    $("#to_ward").val(toAddress.ward);
                    $("#to_district").val(toAddress.district);
                    $("#to_province").val(toAddress.province);

                    // Các thông tin khác
                    $("#cod_amount").val(data.cod_amount);
                    $("#service_type_id").val(data.service_type_id);
                    $("#payment_type_id").val(data.payment_type_id);
                    $("#client_order_code").val(data.client_order_code);
                    $("#note").text(data.note);

                    // Đổ dữ liệu sản phẩm (items)
                    if (Array.isArray(data.items) && data.items.length > 0) {
                        data.items.forEach((item, index) => {
                            $(`input[name='${index}_item_name']`).val(item.name);
                            $(`input[name='${index}_item_code']`).val(item.code || "");
                            $(`input[name='${index}_item_quantity']`).val(item.quantity);
                            $(`input[name='${index}_length']`).val(item.length || 0);
                            $(`input[name='${index}_wide']`).val(item.width || 0);
                            $(`input[name='${index}_height']`).val(item.height || 0);
                            $(`input[name='${index}_weight']`).val(item.weight || 0);
                        });
                    }

                    // Xử lý `pickup_time`
                    if (data.pickup_time) {
                        let pickupTime = formatPickupTime(data.pickup_time);
                        let pickShiftSelect = $("#pick_shift");
                        pickShiftSelect.empty(); // Xóa dữ liệu cũ
                        pickShiftSelect.append(
                            `<option value="3" selected>${pickupTime}</option>
                            <option value="2">Ca hôm nay (12h00 - 18h00)</option>
                            <option value="3">Ca sáng mai (7h00 - 12h00)</option>
                            <option value="4">Ca tối mai (12h00 - 18h00)</option>`
                        );
                    }

                    if (data.required_note) {
                        // console.log(data.required_note);

                        let requiredNote = data.required_note;

                        // Mapping để hiển thị văn bản dễ hiểu
                        let requiredNoteLabels = {
                            "CHOTHUHANG": "Cho thử hàng",
                            "CHOXEMHANGKHONGTHU": "Cho xem hàng nhưng không thử",
                            "KHONGCHOXEMHANG": "Không cho xem hàng"
                        };

                        let displayText = requiredNoteLabels[requiredNote] || requiredNote;

                        let requiredNoteSelect = $("#required_note");
                        requiredNoteSelect.empty(); // Xóa dữ liệu cũ

                        // Tạo danh sách tùy chọn và loại bỏ tùy chọn trùng với giá trị hiện tại
                        let options = Object.entries(requiredNoteLabels)
                            .filter(([value, label]) => value !== requiredNote) // Loại bỏ option trùng
                            .map(([value, label]) => `<option value="${value}">${label}</option>`)
                            .join(""); // Nối các option thành chuỗi

                        // Thêm option đang chọn + các option còn lại vào select
                        requiredNoteSelect.append(`
                            <option value="${requiredNote}" selected>${displayText}</option>
                            ${options}
                        `);
                    }

                    // Hiển thị modal sau khi load dữ liệu thành công
                    $("#orderModal").modal("show");

                } else {
                    alert("Không thể lấy thông tin đơn hàng từ GHN!");
                }
            },
            error: function (error) {
                console.error("Lỗi khi lấy thông tin đơn hàng:", error);
                alert("Có lỗi xảy ra khi gọi API GHN!");
            }
        });
    });

    $('#submitOrderUpdate').click(function () {
        // Lấy dữ liệu từ form
        let token = $('#ghn_token').val();
        let shop_id = $('#ghn_shop_id').val();
        let shipment_id = $('#shipment_id').val(); // Lấy ID của shipment cần cập nhật

        let orderData = {
            payment_type_id: parseInt($('#payment_type_id').val()),
            note: $('#note').val(),
            required_note: $('#required_note').val(),
            from_name: $('#from_name').val(),
            from_phone: $('#from_phone').val(),
            from_address: $('#from_address').val(),
            from_ward_name: $('#from_ward').val(),
            from_district_name: $('#from_district').val(),
            from_province_name: $('#from_province').val(),
            to_name: $('#to_name').val(),
            to_phone: $('#to_phone').val(),
            to_address: $('#to_address').val(),
            to_ward_name: $('#to_ward').val(),
            to_district_name: $('#to_district').val(),
            to_province_name: $('#to_province').val(),
            pick_shift: [parseInt($('#pick_shift').val())], 
            cod_amount: parseInt($('#cod_amount').val()),
            service_type_id: parseInt($('#service_type_id').val()),
            client_order_code: $('#client_order_code').val(),
            order_code: $('#order_codes').val(),
            items: []
        };

        // Lấy danh sách sản phẩm
        let totalWeight = 0;
        let maxLength = 0;
        let maxWidth = 0;
        let totalHeight = 0;

        $('input[name$="_item_name"]').each(function () {
            let nameAttr = $(this).attr("name"); // Lấy name, ví dụ: "0_item_name"
            let index = nameAttr.split("_")[0]; // Lấy số index (0,1,2...)

            let weight = parseInt($(`input[name='${index}_weight']`).val()) || 1;
            let length = parseInt($(`input[name='${index}_length']`).val()) || 1;
            let width = parseInt($(`input[name='${index}_wide']`).val()) || 1;
            let height = parseInt($(`input[name='${index}_height']`).val()) || 1;

            let item = {
                name: $(this).val(),
                code: $(`input[name='${index}_item_code']`).val(),
                quantity: parseInt($(`input[name='${index}_item_quantity']`).val()) || 1,
                price: 200000,
                length: Math.max(1, length),
                width: Math.max(1, width),
                height: Math.max(1, height),
                weight: Math.max(1, weight),
                category: {
                    level1: "Hàng hóa"
                }
            };

            orderData.items.push(item);

            // Tính tổng weight (gram)
            totalWeight += item.weight;

            // Lấy giá trị lớn nhất của length và width
            maxLength = Math.max(maxLength, item.length);
            maxWidth = Math.max(maxWidth, item.width);

            // Tổng chiều cao của tất cả sản phẩm
            totalHeight += item.height;
        });


        // Cập nhật lại giá trị tổng hợp vào `orderData`
        orderData.length = Math.max(10, maxLength);  // GHN yêu cầu chiều dài tối thiểu là 10cm
        orderData.width = Math.max(10, maxWidth);    // GHN yêu cầu chiều rộng tối thiểu là 10cm
        orderData.height = Math.max(10, totalHeight); // GHN yêu cầu chiều cao tối thiểu là 10cm
        orderData.weight = Math.max(100, totalWeight); // GHN yêu cầu trọng lượng tối thiểu là 100g

        // Log dữ liệu trước khi gửi
        // console.log("Dữ liệu gửi đi:", JSON.stringify(orderData, null, 2));

        // Gửi yêu cầu tạo đơn hàng đến GHN
        $.ajax({
            url: 'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/update',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'Token': token,
                'ShopId': shop_id
            },
            data: JSON.stringify(orderData),
            success: function (response) {
                if (response.code === 200) {
                    showAlert('success', 'Cập nhật đơn hàng thành công')
                    setTimeout(function () {
                        window.location.reload();
                    }, 2500);
                    // window.location.reload();
                }
            },
            error: function (error) {
                let errorMessage = "Có lỗi xảy ra khi tạo đơn hàng!";
                let errorData = error.responseJSON ? error.responseJSON : error; // Lấy dữ liệu lỗi nếu có
                showAlert('error', errorMessage, errorData);
            }
        });
    });

    function formatPickupTime(isoDate) {
        if (!isoDate) return "Không có dữ liệu";
        
        let date = new Date(isoDate);
        
        let day = String(date.getDate()).padStart(2, '0'); 
        let month = String(date.getMonth() + 1).padStart(2, '0'); 
        let year = date.getFullYear();
        
        let hours = date.getHours();
        let minutes = String(date.getMinutes()).padStart(2, '0');
        
        let ampm = hours >= 12 ? "PM" : "AM";
        hours = hours % 12 || 12; // Chuyển sang 12-hour format
        
        return `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
    }

    function splitAddress(address) {
        if (!address) return { address: "", ward: "", district: "", province: "" };

        let parts = address.split(",").map(part => part.trim());
        let length = parts.length;

        return {
            address: length > 3 ? parts.slice(0, length - 3).join(", ") : "",  // Địa chỉ cụ thể (không gồm phường, quận, tỉnh)
            ward: length > 2 ? parts[length - 3] : "",      // Phường/Xã
            district: length > 1 ? parts[length - 2] : "",  // Quận/Huyện
            province: length > 0 ? parts[length - 1] : "" //hidden  // Tỉnh/Thành phố
        };
    }
});
</script>

<script>
    $(document).ready(function () {
        let order_code = $("#order_codes").val();
        let token = $("#ghn_token").val();
        
        if (!order_code) {
            $("#order-detail").html("<p class='text-danger'>Không tìm thấy mã đơn hàng.</p>");
            return;
        }

        // Gửi yêu cầu lấy chi tiết đơn hàng
        $.ajax({
            url: "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail",
            type: "POST",
            contentType: "application/json",
            headers: {
                "Token": token
            },
            data: JSON.stringify({ "order_code": order_code }),
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    
                    // Tạo HTML để hiển thị thông tin đơn hàng
                    let orderHtml = `
                        <h4>Chi tiết đơn hàng</h4>
                        <p><strong>Mã đơn hàng GHN:</strong> ${data.order_code}</p>
                        <p><strong>Người gửi:</strong> ${data.from_name} - ${data.from_phone}</p>
                        <p><strong>Địa chỉ gửi:</strong> ${data.from_address}</p>
                        <p><strong>Người nhận:</strong> ${data.to_name} - ${data.to_phone}</p>
                        <p><strong>Địa chỉ nhận:</strong> ${data.to_address}</p>
                        <p><strong>Trạng thái:</strong> ${data.status}</p>
                        <p><strong>Khối lượng (gram):</strong> ${data.weight}</p>
                        <p><strong>Kích thước (cm):</strong> ${data.length} x ${data.width} x ${data.height}</p>
                        <p><strong>Tiền COD:</strong> ${data.cod_amount} VNĐ</p>
                        <h5>Lịch sử đơn hàng</h5>
                        <ul id="order-log">
                    `;

                    // Kiểm tra nếu `log` tồn tại và là mảng trước khi duyệt
                    if (Array.isArray(data.log) && data.log.length > 0) {
                        data.log.forEach(log => {
                            orderHtml += `<li>${log.status} - ${log.updated_date}</li>`;
                        });
                    } else {
                        orderHtml += `<li>Không có lịch sử đơn hàng.</li>`;
                    }

                    orderHtml += `</ul>`;

                    // Đổ dữ liệu vào div
                    $("#order-detail").html(orderHtml);
                } else {
                    $("#order-detail").html("<p class='text-danger'>Không thể lấy thông tin đơn hàng.</p>");
                }
            },
            error: function (error) {
                console.error("Lỗi khi lấy thông tin đơn hàng:", error);
                $("#order-detail").html("<p class='text-danger'>Có lỗi xảy ra khi lấy thông tin đơn hàng.</p>");
            }
        });
    });

</script>