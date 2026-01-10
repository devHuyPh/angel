@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="card">
    <div class="card-header with-border d-flex justify-content-between align-items-center">
    <h4 class="card-title"><i class="fa fa-history"></i> Lịch sử thưởng</h4>
    </div>

    <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
      <thead>
        <tr>
        <th>Mã đơn</th>
        <th>Mã giao dịch</th>
        <th>Kho gửi</th>
        <th>Kho nhận</th>
        <th>Tổng tiền</th>
        <th>Tiền thưởng</th>
        <th class="text-center">Hành động</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders as $order)
        @if ($order->bonus_amount != 0 && $order->bonus_confirmed)
      <tr>
      <td>{{ $order->id }}</td>
      <td>{{ $order->transaction_code ?? '-' }}</td>
      <td>{{ $order->fromStore->name ?? '-' }}</td>
      <td>{{ $order->toStore->name ?? '-' }}</td>
      <td>{{ format_price($order->amount) }}</td>
      <td class="text-success fw-bold">{{ format_price($order->bonus_amount) }}</td>
      <td class="text-center">
        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $order->id }}">
        <i class="fa fa-eye"></i> Xem
        </button>
      </td>
      </tr>
      @endif
      @endforeach
      </tbody>
      </table>
    </div>

    
    </div>
  </div>

  {{-- Modal: đặt bên ngoài table --}}
  @foreach($orders as $order)
    @if ($order->bonus_amount != 0 && $order->bonus_confirmed)
    <div class="modal fade" id="detailModal{{ $order->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $order->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title">Chi tiết đơn hàng #{{ $order->id }}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
      <p><strong>Mã giao dịch:</strong> {{ $order->transaction_code }}</p>
      <p><strong>Kho gửi:</strong> {{ $order->fromStore->name ?? '-' }}</p>
      <p><strong>Kho nhận:</strong> {{ $order->toStore->name ?? '-' }}</p>
      <p><strong>Ngày giao:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}</p>
      <p><strong>Người tạo:</strong> {{ $order->fromStore->customer->name ?? '-' }}</p>
      <p><strong>Tiền thưởng:</strong> <span class="text-success">{{ format_price($order->bonus_amount) }}</span></p>

      <h6 class="mt-3">Danh sách sản phẩm đã giao:</h6>
      <table class="table table-sm table-striped">
      <thead>
      <tr>
      <th>Sản phẩm</th>
      <th>Số lượng</th>
      <th>Giá</th>
      </tr>
      </thead>
      <tbody>
      @foreach($order->products as $item)
      <tr>
      <td>{{ $item->product->name ?? 'Sản phẩm đã xoá' }}</td>
      <td>{{ $item->qty }}</td>
      <td>{{ format_price($item->price) }}</td>
      </tr>
      @endforeach
      </tbody>
      </table>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
    </div>
    </div>
    @endif
  @endforeach
@endsection

@once
  @push('footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @endpush
@endonce
