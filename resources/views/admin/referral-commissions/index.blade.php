@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="card">
    <div class="card-body table-responsive">
    <h4 class="mb-3">{{trans('core/base::layouts.List of orders with commission')}}</h4>

    <table class="table table-bordered">
    <thead>
    <tr>
    <th>{{trans('core/base::layouts.Order code:')}}</th>
    <th>{{trans('core/base::layouts.Date created')}}</th>
    <th>{{trans('core/base::layouts.Number of beneficiaries')}}</th>
    <th>{{trans('core/base::layouts.actions')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($orders as $order)
    <tr>
    <td>{{ $order->order->code ?? $order->order_id }}</td>
    <td>{{ optional($order->order)->created_at?->format('d/m/Y H:i') ?? '---' }}</td>
    <td>{{ $order->order_commissions_count }}</td>
    <td>
    <a href="{{ route('referral-commissions.detail', $order->order_id) }}" class="btn btn-sm btn-primary">
    Xem chi tiết
    </a>
    </td>
    </tr>
    @endforeach
    </tbody>
    </table>

    {{ $orders->links() }}
    </div>
    </div>
@endsection
