@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="card">
    <div class="card-header">
    <h4 class="card-title">📦 {{ trans('core/base::kho.completed_orders_unrewarded') }}</h4>
    </div>
    <div class="card-body">
    <table class="table table-bordered table-striped">
      <thead class="thead-light">
      <tr class="text-center">
        <th>{{ trans('core/base::kho.order_code') }}</th>
        <th>{{ trans('core/base::kho.from_store') }}</th>
        <th>{{ trans('core/base::kho.to_store') }}</th>
        <th>{{ trans('core/base::kho.total_amount') }}</th>
        <th>{{ trans('core/base::kho.actions') }}</th>
      </tr>
      </thead>
      <tbody>
      @forelse ($orders as $order)
      <tr>
      <td>{{ $order->transaction_code }}</td>
      <td>
      {{ $order->fromStore->name }}<br>
      <small class="text-muted">({{ $order->fromStore->storeLevel->name }})</small>
      </td>
      <td>
      {{ $order->toStore->name }}<br>
      <small class="text-muted">({{ $order->toStore->storeLevel->name }})</small>
      </td>
      <td class="text-end text-success">
      {{ format_price($order->amount) }}
      </td>
      <td class="text-center">
      <a href="{{ route('admin.store-orders.view', $order->id) }}" class="btn btn-sm btn-icon btn-primary"
        data-bs-toggle="tooltip" title="{{ trans('core/base::kho.view_order_details') }}">
        <x-core::icon name="ti ti-eye" />
      </a>
      </td>
      </tr>
    @empty
      <tr>
      <td colspan="5" class="text-center text-muted">
      {{ trans('core/base::kho.no_orders_to_reward') }}
      </td>
      </tr>
    @endforelse
      </tbody>
    </table>

    <div class="mt-3">
      {{ $orders->links() }}
    </div>
    </div>
  </div>
@endsection
