@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid" style="padding: 0; margin: 0; min-height: 100vh; display: flex; flex-direction: column; background: #f8f9fa;">
        <div class="card" style="margin: 0; border: none; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); border-radius: 10px; flex-grow: 1;">
            <div class="card-header" style="background: #ffffff; padding: 1rem; border-bottom: none;">
                <div class="container" style="max-width: 1200px; margin: 0 auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="color: #000000; font-size: 1.5em;">{{ trans('core/base::layouts.edit_downline_revenue') }}</h5>
                        <a href="{{ route('totalrevenue.index') }}" class="btn btn-dark btn-sm shadow-sm" style="border: 1px solid #ffffff; color: #ffffff; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9em; transition: all 0.3s ease;">
                            <i class="fas fa-arrow-left"></i> {{ trans('core/base::layouts.back')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 20px; background: #f8f9fa;">
                <form method="POST" action="{{ route('totalrevenue.update', $total->id) }}" id="revenueForm">
                    @csrf
                    @method('PUT')
                    <div style="max-width: 500px; margin: 0 auto;">
                        <div style="margin-bottom: 15px;">
                            <label for="amount" style="display: block; font-size: 1em; color: #333333; margin-bottom: 5px;">{{ trans('core/base::layouts.amount')}} (VND)</label>
                            <input type="text" name="amount" id="amount" value="{{ number_format($total->amount, 0, '.', '.') }}" required style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1em;" placeholder="Enter amount">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label for="percentage" style="display: block; font-size: 1em; color: #333333; margin-bottom: 5px;">{{ trans('core/base::layouts.precentage')}} (%)</label>
                            <input type="number" step="0.01" name="percentage" id="percentage" value="{{ $total->percentage }}" required style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; font-size: 1em;" placeholder="Enter percentage" min="0" max="100">
                        </div>
                        <div style="text-align: right;">
                            <button type="submit" class="btn btn-primary" style="padding: 8px 20px; border-radius: 5px; background: #007bff; border: none; color: #ffffff;"><i class="fas fa-pen"></i> {{ trans('core/base::layouts.update')}}</button>
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
                console.error("Không tìm thấy amountInput hoặc form");
                return;
            }

            // Xử lý bộ gõ tiếng Việt
            amountInput.addEventListener("compositionstart", function () {
                isComposing = true;
            });

            amountInput.addEventListener("compositionend", function () {
                isComposing = false;
                formatNumber();
            });

            // Định dạng khi nhập
            amountInput.addEventListener("input", function (e) {
                if (isComposing) return;

                let value = e.target.value.replace(/\D/g, "");
                e.target.value = value ? new Intl.NumberFormat("vi-VN").format(value) : "";
            });

            // Định dạng khi rời khỏi input
            amountInput.addEventListener("blur", function () {
                formatNumber();
            });

            // Xử lý trước khi gửi form
            form.addEventListener("submit", function (e) {
                let rawValue = amountInput.value.replace(/\./g, "");
                amountInput.value = rawValue; // Gán giá trị số nguyên trước khi gửi
                console.log("Giá trị gửi đi: ", rawValue); // Debug
            });

            function formatNumber() {
                let value = amountInput.value.replace(/\D/g, "");
                amountInput.value = value ? new Intl.NumberFormat("vi-VN").format(value) : "";
            }
        });
    </script>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
@endpush

@push('js')
    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
@endpush
