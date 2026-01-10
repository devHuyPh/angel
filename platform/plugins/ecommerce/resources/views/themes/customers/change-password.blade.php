@extends(EcommerceHelper::viewPath('customers.layouts.account-settings'))

@section('title', __('Change password'))

@section('account-content')
<style>
    @media (max-width: 767.98px) {
        .profile__tab-content {
            padding: 0 !important;

        }

        .form-control {
            /* font-size: 16px !important; */
        }

        .active {
            span {
                color: #fff;
            }
        }
    }
</style>
<div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
    style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
    <a href="{{ route('setting') }}" class="back-btn text-success">
        <i class="bi bi-chevron-left"></i>
    </a>
    <h1 class="header-title text-success">{{ __('Change password') }}</h1>
</div>
<div class="container pb-3">
    {!! $form->renderForm() !!}
</div>
@stop