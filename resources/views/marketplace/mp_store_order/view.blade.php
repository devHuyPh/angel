@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
	@if (session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif
	@if (session('error'))
		<div class="alert alert-danger">{{ session('error') }}</div>
	@endif
	<div class="row">
		<div class="col-lg-12">
			<div class="main-form">
				<div class="card">
					<div class="card-header bg-success text-white">
						<strong><i class="ti ti-file-description me-1"></i>
							{{ trans('core/base::kho.Order details created:') }}
							#{{ $storeOrder->transaction_code }}</strong>
					</div>

					<div class="card-body">
						<div class="mb-4">
							<p><strong>{{ trans('core/base::kho.status') }}:</strong>
								<span class="badge bg-info text-white text-uppercase">
									{{ $storeOrder->status }}
								</span>
							</p>
							<p><strong>{{ trans('core/base::kho.create_at') }}:</strong>
								{{ $storeOrder->created_at->format('d/m/Y H:i') }}</p>
						</div>

						<h6 class="text-uppercase fw-bold">{{ trans('core/base::kho.Products ordered') }}</h6>
						<div class="table-responsive">
							<table class="table table-bordered table-striped mb-0">
								<thead class="table-light">
									<tr>
										<th>{{ trans('core/base::kho.product_name') }}</th>
										<th class="text-center">{{ trans('core/base::kho.qty') }}</th>
									</tr>
								</thead>
								<tbody>
									@forelse ($storeOrder->products as $item)
										<tr>
											<td>{{ $item->product->name ?? '[Sản phẩm đã bị xóa]' }}</td>
											<td class="text-center">{{ $item->qty }}</td>
										</tr>
									@empty
										<tr>
											<td colspan="2" class="text-center text-muted">
												{{trans('core/base::kho.no_product_in_order')}}
											</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
						@if (!$storeOrder->stock_imported && $storeOrder->status == 'completed' && $storeOrder->to_store === auth('customer')->user()->store->id)
							<form action="{{ route('marketplace.vendor.store-orders.confirm-import', $storeOrder->id) }}"
								method="POST" class="mt-3">
								@csrf
								<button type="submit" class="btn btn-success">
									<i class="ti ti-package-import me-1"></i> Xác nhận nhập kho
								</button>
							</form>
						@endif

						<a href="{{ route('marketplace.vendor.store-orders.index') }}" class="btn btn-secondary mt-3">
							<i class="ti ti-arrow-left me-1"></i>{{trans('core/base::kho.back_list')}}
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection