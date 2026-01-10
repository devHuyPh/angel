@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-coins mb-3"></i>
                    {{ trans('core/base::layouts.Commission sharing details - Orders') }} #{{ $id }}
                </h4>
                <a href="/admin/ecommerce/orders/edit/{{ $id }}" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-edit me-1"></i>{{ trans('core/base::layouts.Edit order') }}
                </a>
            </div>

            <div class="card-body">
                <p class="text-muted mb-4">
                    <i class="fas fa-receipt me-1"></i>
                    {{ trans('core/base::layouts.Order code:') }}
                    <strong>{{ $commissions->first()->order->code ?? '---' }}</strong>
                </p>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-nowrap">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 30%"> {{ trans('core/base::layouts.Beneficiary') }}</th>
                                <th style="width: 10%"> {{ trans('core/base::layouts.Customer') }}</th>
                                <th style="width: 20%"> {{ trans('core/base::layouts.Profit Commission') }}</th>
                                <th style="width: 20%">{{ trans('core/base::layouts.Time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($commissions as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->customer->name ?? '---' }}</strong><br>
                                        <span class="text-muted small">ID: {{ $item->customer_id }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-dark px-3 py-1">KH{{ $item->level }}</span>
                                    </td>
                                    <td class="text-success fw-bold">
                                        {{ format_price($item->commission_amount) }}
                                    </td>
                                    <td class="text-muted small">
                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-1"></i>Không có dữ liệu hoa hồng.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
