@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid" style="padding: 0; margin: 0; min-height: 100vh; display: flex; flex-direction: column; background: #f8f9fa;">
        <div class="card" style="margin: 0; border: none; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); border-radius: 10px; flex-grow: 1;">
            <div class="card-header" style="background: #ffffff; color: #fafafa; padding: 1rem; border-bottom: none;">
                <div class="container" style="max-width: 1200px; margin: 0 auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="color: #000000; font-size: 1.5em;">{{ trans('core/base::layouts.add_new_upline_revenue')}}</h5>
                        <a href="{{ route('totalrevenue.index') }}" class="btn btn-dark btn-sm shadow-sm text-light" style="border: 1px solid #ffffff; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9em; transition: all 0.3s ease;">
                            <i class="fas fa-arrow-left"></i>{{ trans('core/base::layouts.back')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 20px; background: #f8f9fa;">
                <form method="POST" action="{{ route('totalrevenue.store') }}" id="revenueForm">
                    @csrf
                    <div style="max-width: 500px; margin: 0 auto;">
                        <div style="margin-bottom: 15px;">
                            <label for="amount" style="display: block; font-size: 1em; color: #333333; margin-bottom: 5px;">{{ trans('core/base::layouts.amount')}} (VND)</label>
                            <input type="text" name="amount" id="amount" required style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1em;" placeholder="Enter amount">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label for="percentage" style="display: block; font-size: 1em; color: #333333; margin-bottom: 5px;">{{ trans('core/base::layouts.precentage')}} (%)'</label>
                            <input type="number" step="0.01" name="percentage" id="percentage" required style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1em;" placeholder="Enter percentage" min="0" max="100">
                        </div>
                        <div style="text-align: right;">
                            <button type="submit" class="btn btn-primary" style="padding: 8px 20px; border-radius: 5px; background: #007bff; border: none; color: #ffffff;"><i class="fas fa-plus"></i> {{ trans('core/base::layouts.add_new')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            const amountInput = document.getElementById("amount");
            const form = document.getElementById("revenueForm");
            let isComposing = false;

            if (!amountInput || !form) {
                console.error("Amount input or form not found");
                return;
            }

            // Handle Vietnamese input method
            amountInput.addEventListener("compositionstart", function () {
                isComposing = true;
            });

            amountInput.addEventListener("compositionend", function () {
                isComposing = false;
                formatNumber();
            });

            // Format on input
            amountInput.addEventListener("input", function (e) {
                if (isComposing) return;

                let value = e.target.value.replace(/\D/g, "");
                e.target.value = value ? new Intl.NumberFormat("vi-VN").format(value) : "";
            });

            // Format on blur
            amountInput.addEventListener("blur", function () {
                formatNumber();
            });

            // Handle before form submission
            form.addEventListener("submit", function (e) {
                let rawValue = amountInput.value.replace(/\./g, "");
                amountInput.value = rawValue; // Set raw value before submission
                console.log("Submitted value: ", rawValue); // Debug
            });

            function formatNumber() {
                let value = amountInput.value.replace(/\D/g, "");
                amountInput.value = value ? new Intl.NumberFormat("vi-VN").format(value) : "";
            }
        });
    </script>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
@endpush

@push('js')
    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
@endpush
