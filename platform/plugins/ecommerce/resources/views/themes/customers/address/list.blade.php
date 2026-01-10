@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Address books'))

@section('content')
    <style>
        @media (max-width: 767.98px) {
            .profile__tab-content {
                padding: 0 !important;

            }

            .form-control {
                font-size: 16px !important;
            }

            .profile__address-title {
                font-size: 20px !important;
            }
        }
    </style>
    <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <a href="{{ route('setting') }}" class="back-btn text-success">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="header-title text-success">{{ __('Địa chỉ') }}</h1>
    </div>
    @if ($addresses->isNotEmpty())
        <div class="container pb-3">
            <div class="dashboard-address">
                @if ($addresses->isNotEmpty())
                    <div class="row row-cols-md-2 row-cols-1 g-3">
                        @foreach ($addresses as $address)
                            @include(EcommerceHelper::viewPath('customers.address.item'), [
                                'address' => $address,
                            ])
                        @endforeach
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-start mt-4">
                    <a class="btn btn-primary" href="{{ route('customer.address.create') }}">
                        {{ __('Add a new address') }}
                    </a>
                </div>
            </div>
        </div>
    @else
        @include(EcommerceHelper::viewPath('customers.partials.empty-state'), [
            'title' => __('No addresses!'),
            'subtitle' => __('You have not added any addresses yet.'),
            'actionUrl' => route('customer.address.create'),
            'actionLabel' => __('Add a new address'),
        ])
    @endif
@endsection
