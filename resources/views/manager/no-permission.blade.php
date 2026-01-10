@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Overview'))

@section('content')
    <style>
        <style>@media (max-width: 767.98px) {

            .profile__tab-content {
                padding: 0 !important;
            }

            .form-control {
                font-size: 16px !important;
            }
        }
    </style>
    <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <a href="{{ route('setting') }}" class="back-btn text-success">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="header-title text-success">{{ __('Quản lý khu vực') }}</h1>
    </div>
    @include('notification_alert.active_account')
    <div class="alert alert-warning text-center mt-5">
        <h4>{{ trans('core/base::layouts.you_are_not_assigned_to_manage_any_area') }}.</h4>
        <p>{{ trans('core/base::layouts.please_contact_administrator_for_more_information') }}.</p>
    </div>
@endsection
