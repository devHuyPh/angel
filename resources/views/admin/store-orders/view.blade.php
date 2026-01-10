@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="row">
    <div class="col-md-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="card-title mb-0">
        📄 {{ trans('core/base::kho.order_detail') }}:
        <code>{{ $order->transaction_code }}</code>
      </h4>
      <a href="{{ route('admin.store-orders.pending-bonus') }}" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left"></i> {{ trans('core/base::kho.back') }}
      </a>
      </div>
      <div class="card-body">
      <table class="table table-borderless">
        <tr>
        <th width="150">{{ trans('core/base::kho.from_store') }}</th>
        <td>{{ $order->fromStore->name }} <span
          class="text-muted">({{ $order->fromStore->storeLevel->name }})</span></td>
        </tr>
        <tr>
        <th>{{ trans('core/base::kho.to_store') }}</th>
        <td>{{ $order->toStore->name }} <span class="text-muted">({{ $order->toStore->storeLevel->name }})</span>
        </td>
        </tr>
        <tr>
        <th>{{ trans('core/base::kho.total_amount') }}</th>
        <td><strong class="text-success">{{ format_price($order->amount) }}</strong></td>
        </tr>
        <tr>
        <th>{{ trans('core/base::kho.status') }}</th>
        <td>
          <span class="badge bg-success text-success-fg">{{ trans('core/base::kho.status_completed') }}</span>
        </td>
        </tr>
      </table>

      <hr>

      <h5 class="mb-3">📦 {{ trans('core/base::kho.product_list') }}</h5>
      <table class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>{{ trans('core/base::kho.product') }}</th>
          <th width="100" class="text-center">{{ trans('core/base::kho.quantity') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order->products as $item)
      <tr>
        <td>{{ $item->product->name ?? trans('core/base::kho.deleted_product') }}</td>
        <td class="text-center">{{ $item->qty }}</td>
      </tr>
      @endforeach
        </tbody>
      </table>

      @if ($order->completed_image)
      <hr>
      <h5 class="mb-2">🖼 {{ trans('core/base::kho.completed_image') }}</h5>
      <img src="{{ Storage::url($order->completed_image) }}" alt="Ảnh hoàn thành" class="img-thumbnail"
      style="max-width: 100%; max-height: 400px;">
    @endif
      </div>
    </div>
    </div>

    <div class="col-md-4">
    <div class="card bg-light">
      <div class="card-header">
      🎁 {{ trans('core/base::kho.order_bonus') }}
      </div>
      <div class="card-body">
      @if ($order->bonus_confirmed)
      <div class="alert alert-success mb-0">
      ✅ {{ trans('core/base::kho.bonus_granted') }}:
      <strong>{{ format_price($order->bonus_amount) }}</strong>
      </div>
    @elseif ($order->status === 'completed')
      <form method="POST" action="{{ route('admin.store-orders.confirm-bonus', $order->id) }}">
      @csrf
      <button type="submit" class="btn btn-success w-100">
      ✔️ {{ trans('core/base::kho.confirm_bonus') }}
      </button>
      </form>
    @else
      <div class="alert alert-warning mb-0">
      ⚠️ {{ trans('core/base::kho.order_not_completed') }}
      </div>
    @endif
      </div>
    </div>
    </div>
  </div>
@endsection
