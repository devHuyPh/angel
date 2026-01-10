{{-- notification --}}
@if(!$customer->is_active_account)
<style>
    .notification_active_account{
        display: none;
    }

    @media (max-width: 768px) {
        .notification_active_account {
            display: block;
        }
    
    	.notification_active_account h4{
    		font-size: 9px;
    	}
    }
</style>
<div class="col-12 mt-3 mb-md-0 text-md-start notification_active_account">
    <h3 class="fs-2 fw-bold text-uppercase mb-0" style="font-family: 'Inter';">
        <div class="mt-2">
            <div role="alert" class="alert alert-danger">
                <div class="d-flex">
                    <div>
                        <svg class="icon alert-icon svg-icon-ti-ti-info-circle" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                            <path d="M12 9h.01"></path>
                            <path d="M11 12h1v4h1"></path>
                        </svg>
                    </div>
                    <div class="w-100">
                        <h4 class="alert-title mb-0">
                            {{trans('core/base::layouts.sepay-activation-instruction')}}. <a href="{{route('withdrawals.setup-sepay')}}">Thiết lập ngay</a>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </h3>
</div>
@endif