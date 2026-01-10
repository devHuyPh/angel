<form action="{{ route('bank_accounts.store') }}" method="post">
    @csrf
    <div class="modal fade" id="addBankAccount" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{trans('core/base::layouts.add_bank_account')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">{{trans('core/base::layouts.select_bank')}}<span class="text-danger">*</span></label>
                        <select name="bank_code" id="api_bank_vn" class="form-control">
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div >
                        <div class="mb-3">
                            <label class="form-label">{{trans('core/base::layouts.bank_code')}}<span class="text-danger">*</span></label>
                            <input type="text" name="bank_code_new" id="custom_bank_code" class="form-control" value="{{old('bank_code')}}" placeholder="Ex: ACB">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{trans('core/base::layouts.bank_name')}}<span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{old('bank_name')}}" placeholder="Ex: Ngân hàng Á Châu">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{trans('core/base::layouts.account_number')}}<span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control" value="{{old('account_number')}}" placeholder="Ex: 09999999999">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{trans('core/base::layouts.account_holder')}}<span class="text-danger">*</span></label>
                        <input type="text" name="account_holder" class="form-control" value="{{old('account_holder')}}" placeholder="Ex: NGUYEN VAN A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{trans('core/base::layouts.branch')}} {{trans('core/base::layouts.none_required')}}</label>
                        <input type="text" name="branch" class="form-control" value="{{old('branch')}}" placeholder="Ex: Nam Từ Liêm, Hà Nội">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{trans('core/base::layouts.swift_code')}} {{trans('core/base::layouts.none_required')}}</label>
                        <input type="text" name="swift_code" class="form-control" value="{{old('swift_code')}}" placeholder="Ex: 1234ADSEF">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{trans('core/base::layouts.close')}}</button>
                    <button type="submit" class="btn btn-primary">{{trans('core/base::layouts.add')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>
@push('footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        const bankSelect = $("#api_bank_vn");
        const bankInputs = $("#custom_bank_inputs");
        const bankNameInput = $("#bank_name");
        const bankCodeInput = $("#custom_bank_code");

        // Lấy danh sách ngân hàng từ API VietQR
        $.ajax({
            url: "https://api.vietqr.io/v2/banks",
            method: "GET",
            dataType: "json",
            success: function(response) {
                if (response.code === "00" && response.data.length > 0) {
                    response.data.forEach(bank => {
                        bankSelect.append(new Option(`${bank.name} - ${bank.shortName}`, bank.code));
                    });
                }
            },
            error: function(error) {
                console.error("Lỗi khi tải danh sách ngân hàng:", error);
            }
        });

        // Xử lý khi chọn ngân hàng
        bankSelect.on("change", function() {
            let selectedOption = $(this).val();

            if (selectedOption === "other") {
                // Nếu chọn "Khác" → Hiện input để nhập tay
                bankInputs.removeClass("d-none");
                bankNameInput.val("").focus();
                bankCodeInput.val("");
            } else {
                // Nếu chọn ngân hàng có sẵn → Ẩn input nhưng vẫn set giá trị
                bankInputs.addClass("d-none");
                let selectedText = $(this).find("option:selected").text().split(" - ");
                bankNameInput.val(selectedText[0]); // Set tên ngân hàng
                bankCodeInput.val(selectedOption); // Set mã ngân hàng
            }
        });
    });
</script>

@endpush