@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ trans('core/base::layouts.User_Details') }}</h1>
                    </div>
                    {{-- <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">{{ trans('core/base::layouts.home') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('core/base::layouts.User_Details') }}</li>
                        </ol>
                    </div> --}}
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- User Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('core/base::layouts.User_Information') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ trans('core/base::layouts.Name') }}:</strong> {{ $reward->customer->name ?? trans('core/base::layouts.unknown') }}</p>
                                <p><strong>{{ trans('core/base::layouts.email') }}:</strong> {{ $reward->customer->email ?? trans('core/base::layouts.na') }}</p>
                                <p><strong>{{ trans('core/base::layouts.phone') }}:</strong> {{ $reward->customer->phone ?? trans('core/base::layouts.na') }}</p>
                                <p><strong>{{ trans('core/base::layouts.kyc_status') }}:</strong> 
                                    {{ $reward->customer->kyc_status ? trans('core/base::layouts.verified') : trans('core/base::layouts.not_verified') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ trans('core/base::layouts.wallet_1') }}:</strong> {{ format_price($reward->customer->walet_1 ?? 0, 0) }} </p>
                                <p><strong>{{ trans('core/base::layouts.wallet_2') }}:</strong> {{ format_price($reward->customer->walet_2 ?? 0, 0) }} </p>
                                <p><strong>{{ trans('core/base::layouts.referral') }}:</strong> 
                                    @if ($user)
                                        {{ $user->name }}
                                    @else
                                        {{ trans('core/base::layouts.none') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discounts List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('core/base::layouts.discounts_of') }} {{ $reward->customer->name ?? trans('core/base::layouts.unknown') }}</h3>
                    </div>
                    <div class="card-body">
                        @forelse ($discountCustomer as $dc)
                            @if ($dc->discount)
                                <div class="alert alert-success" style="border: 2px dashed #17a2b8; background-color: #28a745; color: white; margin-bottom: 15px;">
                                    <h5 style="font-weight: bold;">
                                        {{ trans('core/base::layouts.discount_code') }}: {{ $dc->discount->code }}
                                        <button class="btn btn-sm btn-outline-light float-right" onclick="navigator.clipboard.writeText('{{ $dc->discount->code }}')">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </h5>
                                    <p style="margin-bottom: 0;">
                                        {{ $dc->discount->title }} {{ trans('core/base::layouts.for_customer') }} {{ $reward->customer->name ?? trans('core/base::layouts.unknown') }}
                                    </p>
                                    <p>{{ trans('core/base::layouts.usage_count') }}:
                                        <span class="float-right">
                                            {{ $dc->discount->total_used }}/{{ $dc->discount->quantity }}
                                        </span>
                                    </p>
                                    <p style="color: #ffeb3b;">
                                        ({{ trans('core/base::layouts.discount_not_usable_with_promotion') }}).
                                    </p>
                                    <p class="float-right">
                                        {{ $dc->discount->start_date->format('Y-m-d') }} - 
                                        {{ $dc->discount->end_date ? $dc->discount->end_date->format('Y-m-d') : '—' }}
                                    </p>
                                </div>
                            @endif
                        @empty
                            <div class="alert alert-warning">
                                {{ trans('core/base::layouts.no_discounts_found') }}
                            </div>
                        @endforelse
                    </div>
                    <div class="card-footer">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ trans('core/base::layouts.back') }}</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection