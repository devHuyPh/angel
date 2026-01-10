@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ trans('core/base::layouts.reward_notifications') }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('core/base::layouts.list_of_rewarded_users') }}</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ trans('core/base::layouts.customer') }}</th>
                                    <th>{{ trans('core/base::layouts.reward_type') }}</th>
                                    <th>{{ trans('core/base::layouts.reward_value') }}</th>
                                    <th>{{ trans('core/base::layouts.description') }}</th>
                                    <th>{{ trans('core/base::layouts.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rewards as $reward)
                                    <tr>
                                        <td>
                                            <a href="{{ route('reward.view', $reward->id) }}">
                                                {{ $reward->customer->name ?? trans('core/base::layouts.unknown') }}
                                            </a>
                                        </td>
                                        <td>{{ trans('core/base::layouts.reward_type_' . $reward->reward_type) }}</td>
                                        <td>
                                            @if ($reward->reward_type === 'money')
                                                {{ format_price($reward->reward_value, 0) }} 
                                            @else
                                                {{ $reward->reward_value }}%
                                            @endif
                                        </td>
                                        <td>{{ $reward->description }}</td>
                                        <td>{{ $reward->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ trans('core/base::layouts.no_rewards_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="float-right">
                            {{ $rewards->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection