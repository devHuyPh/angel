@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
	<section id="report-chart" class="ps-dashboard report-chart-content">
		<div class="row mb-3 mt-5">
			<div class="col-12 col-sm-6 col-md-6">
				<div class="ps-block--stat green">
					<div class="ps-block__left">
						<span><i class="icon-database"></i></span>
					</div>
					<div class="ps-block__content">
						<p>{{ trans('core/base::kho.your_import_orders') }}(đang xử lí)</p>
						<h4>{{$myStoreOrdersCount}}</h4>
					</div>
				</div>
			</div>
			<div class="col-12 col-sm-6 col-md-6">
				<div class="ps-block--stat pink">
					<div class="ps-block__left">
						<span><i class="icon-bag-dollar"></i></span>
					</div>
					<div class="ps-block__content">
						<p>{{ trans('core/base::kho.from_you_orders') }}(chưa xử lý)</p>
						<h4>{{ $fromMyStoreOrdersCount }}</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="card mb-3">
					<div class="card-header row">
						<h4 class="card-title col-6">{{ trans('core/base::kho.your_orders_list') }}</h4>
						<div class="col-6 text-end">
							<a href="{{ route('marketplace.vendor.store-orders.create') }}"
								class="btn">{{ trans('core/base::kho.create_order') }}</a>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table-vcenter card-table table-hover table-striped table">
							<thead>
								<tr>
									<th>{{ trans('core/base::kho.id') }}</th>
									<th>{{ trans('core/base::kho.create_at') }}</th>
									<th>{{ trans('core/base::kho.delivery person') }}</th>
									<th>{{ trans('core/base::kho.delivery date') }}</th>
									<th>{{ trans('core/base::kho.payment') }}</th>
									<th>{{ trans('core/base::kho.status') }}</th>
									<th>{{ trans('core/base::kho.unit') }}</th>
									<th class="text-center">{{ trans('core/base::kho.action') }}</th>
								</tr>
							</thead>
							<tbody>
								@forelse ($myStoreOrders as $myStoreOrder)
														<tr>
															<td>
																{{ $myStoreOrder->transaction_code }}
															</td>
															<td>
																{{ date_format($myStoreOrder->created_at, 'H:i d-m-Y') }}
															</td>
															<td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
																title="{{ $myStoreOrder->fromStore ? $myStoreOrder->fromStore->fullAddress : 'Từ công ty' }}">
																{!! optional($myStoreOrder->fromStore)->name
									? $myStoreOrder->fromStore->name .
									'<br>' .
									$myStoreOrder->fromStore->phone .
									'<br>' .
									$myStoreOrder->fromStore->fullAddress
									: 'Từ công ty' !!}
															</td>
															<td>
																{{ $myStoreOrder->confirm_date ? date_format($myStoreOrder->confirm_date, 'H:i d-m-Y') : '--' }}
															</td>
<td>
											@php
												$payment_status = $myStoreOrder->payment_status ?? 'unknown';
																	
												$paymentStatusMap = [
													'completed' => [
														'class' => 'badge bg-success text-success-fg',
														'text' => 'Đã hoàn thành',
													],
													'pending' => [
														'class' => 'badge bg-warning text-warning-fg',
														'text' => 'Đang chờ',
													],
													'failed' => [
														'class' => 'badge bg-danger text-danger-fg',
														'text' => 'Thanh toán lỗi',
													],
													'processing' => [
														'class' => 'badge bg-info text-info-fg',
														'text' => 'Đang xử lý',
													],
													'unknown' => [
														'class' => 'badge bg-secondary text-secondary-fg',
														'text' => 'Không xác định',
													],
												];

												$paymentStatusData =
													$paymentStatusMap[$payment_status] ?? $paymentStatusMap['unknown'];
											  @endphp

											<span class="{{ $paymentStatusData['class'] }}">
												{{ $paymentStatusData['text'] }}
											</span>
										</td>
															<td>
																@php
																	$status = $myStoreOrder->status ?? 'unknown';

																	$statusMap = [
																		'completed' => [
																			'class' => 'badge bg-success text-success-fg',
																			'text' => trans('core/base::kho.status_completed'),
																		],
																		'pending' => [
																			'class' => 'badge bg-warning text-warning-fg',
																			'text' => trans('core/base::kho.status_pending'),
																		],
																		'processing' => [
																			'class' => 'badge bg-info text-info-fg',
																			'text' => trans('core/base::kho.status_processing'),
																		],
																		'shipping' => [
																			'class' => 'badge bg-info text-info-fg',
																			'text' => trans('core/base::kho.status_shipping'),
																		],
																		'delivered' => [
																			'class' => 'badge bg-info text-info-fg',
																			'text' => trans('core/base::kho.status_delivered'),
																		],
																		'cancelled' => [
																			'class' => 'badge bg-danger text-danger-fg',
																			'text' => trans('core/base::kho.status_cancelled'),
																		],
																		'unknown' => [
																			'class' => 'badge bg-secondary text-secondary-fg',
																			'text' => trans('core/base::kho.status_unknown'),
																		],
																	];

																	$statusData = $statusMap[$status] ?? $statusMap['unknown'];
																  @endphp
																<span class="{{ $statusData['class'] }}">
																	{{ $statusData['text'] }}
																</span>
															</td>
															<td>
																@php
																	$totalWeight = $myStoreOrder->products->sum(function ($item) {
																		return $item->product?->weight ?? 0;
																	});
																  @endphp
																{{ number_format($totalWeight / 1000, 2) }} kg
															</td>
															<td class="text-center">
																<div class="table-actions">
																	<a href="{{ route('marketplace.vendor.store-orders.view', $myStoreOrder->transaction_code) }}"
																		class="btn btn-sm btn-icon btn-success">
																		<x-core::icon name="ti ti-eye" data-bs-title="Xem" />
																		<span class="sr-only">{{ trans('core/base::kho.view_update') }}</span> </a>
																</div>
															</td>
														</tr>
								@empty
									<tr>
										<td colspan="100%" class="text-muted py-4 text-center">
											{{ trans('core/base::kho.no_orders') }}
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
					<div class="card-footer">
						<a href="http://chichi.hisotechgroup.com/vendor/orders"> Xem toàn bộ đơn hàng
							<svg class="icon svg-icon-ti-ti-chevron-right" xmlns="http://www.w3.org/2000/svg" width="24"
								height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M9 6l6 6l-6 6"></path>
							</svg>
						</a>
					</div>
				</div>
			</div>






		</div>
	</section>
@endsection

@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const currentPath = window.location.pathname;

			const links = document.querySelectorAll('a[href]');
			links.forEach(function (link) {
				const linkPath = new URL(link.href).pathname;

				if (linkPath === '/vendor/storemanager/index') {
					if (currentPath.startsWith('/vendor/storemanager/')) {
						link.classList.add('active');
					} else {
						link.classList.remove('active');
					}
				}
			});
		});
	</script>
@endpush
