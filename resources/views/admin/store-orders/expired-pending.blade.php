@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="card">
    <div class="card-header">
    <h4>📦 {{ trans('core/base::kho.orders_pending_24h') }}</h4>
    </div>
    <div class="card-body">
    <table class="table table-bordered">
      <thead>
      <tr>
        <th>{{ trans('core/base::kho.order_code') }}</th>
        <th>{{ trans('core/base::kho.from_store') }}</th>
        <th>{{ trans('core/base::kho.to_store') }}</th>
        <th>{{ trans('core/base::kho.total_amount') }}</th>
        <th>{{ trans('core/base::kho.created_at') }}</th>
        <th>{{ trans('core/base::kho.actions') }}</th>
      </tr>
      </thead>
      <tbody>
      @foreach ($orders as $order)
      <tr>
      <td>{{ $order->transaction_code }}</td>
      <td>{{ $order->fromStore->name }} ({{ $order->fromStore->storeLevel->name }})</td>
      <td>{{ $order->toStore->name }} ({{ $order->toStore->storeLevel->name }})</td>
      <td>{{ number_format($order->amount) }}đ</td>
      <td>{{ $order->created_at->diffForHumans() }}</td>
      <td>
      <a href="{{ route('admin.store-orders.edit-from-store', $order->id) }}" class="btn btn-primary btn-sm">
        {{ trans('core/base::kho.change_from_store') }}
      </a>
      </td>
      </tr>
    @endforeach
      </tbody>
    </table>
    <div class="mt-3">{{ $orders->links() }}</div>
    </div>
  </div>
@endsection
