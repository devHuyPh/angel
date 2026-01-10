@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-header">
				<h4 class="card-title">{{trans('core/base::kho.order_detail')}}: {{ $storeOrder->transaction_code }}</h4>
			</div>

			<div class="card-body">
				<p><strong>{{trans('core/base::kho.current_from_store')}}:</strong>
					@if ($storeOrder->fromStore)
						{{ $storeOrder->fromStore->name }} - {{ $storeOrder->fromStore->phone }} <br>
						{{ $storeOrder->fromStore->fullAddress }}
					@else
						<em>{{trans('core/base::kho.from_company')}}</em>
					@endif
				</p>

				<p><strong>{{trans('core/base::kho.create_at')}}:</strong>
					{{ $storeOrder->created_at->format('H:i d-m-Y') }}</p>


				<hr>

				<h5>{{trans('core/base::kho.products_in_order')}}</h5>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>{{trans('core/base::kho.product_name')}}</th>
							<th>SKU</th>
							<th>{{trans('core/base::kho.quantity')}}</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($storeOrder->products as $item)
							<tr>
								<td>{{ $item->product->name ?? '--' }}</td>
								<td>{{ $item->product->sku ?? '--' }}</td>
								<td>{{ $item->qty }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>

				@if ($storeOrder->stock_imported)
					<div class="alert alert-success mt-4">
						Đơn hàng này đã được nhập kho.
					</div>
				@else
					<form method="POST" action="{{ route('store-orders.auto-import', $storeOrder->id) }}">
						@csrf
						<button type="submit" class="btn btn-sm btn-success">
							<x-core::icon name="ti ti-check" data-bs-title="Nhập kho" />
							{{ __('core/base::kho.confirm_import') }}
						</button>
					</form>
				@endif
			</div>
		</div>
	</div>
@endsection
