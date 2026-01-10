document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".add-field-btn").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            let itemId = this.id.split("_")[1];
            let modal = this.closest(".modal");
            let container = modal.querySelector(`#fieldsContainer_${itemId}`);

            if (!container) {
                console.error(`Container #fieldsContainer_${itemId} not found!`);
                return;
            }

            let newField = document.createElement("div");
            newField.classList.add("addedField", "mt-3");
            newField.innerHTML = `
                <div class="col-md-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary p-2 d-flex justify-content-between">
                            <h5 class="card-title text-white font-weight-bold">Field Information</h5>
                            <button class="btn btn-danger btn-sm remove-field" type="button">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>Field Name</label>
                                    <input name="field_name_${itemId}[]" class="form-control" value="" type="text" required placeholder="Field Name">
                                </div>
                                <div class="col-md-4 form-group ">
                                    <label>Form Type</label>
                                    <select name="type_${itemId}[]" class="form-control form-type-select">
                                        <option value="text">Input Text</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="file" >File upload</option>
                                        <option value="datetime" selected>Date time</option>

                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Field Length</label>
                                    <input name="field_length_${itemId}[]" class="form-control" type="number" min="2" required value="" placeholder="Length">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Field Length Type</label>
                                    <select name="length_type_${itemId}[]" class="form-control">
                                        <option value="max" selected>Maximum Length</option>
                                        <option value="digits">Fixed Length</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Form Validation</label>
                                    <select name="validation_${itemId}[]" class="form-control">
                                        <option value="required" selected>Required</option>
                                        <option value="nullable">Optional</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(newField);
            console.log(`Field added to #fieldsContainer_${itemId}:`, newField);

            // Xử lý xóa field
            newField.querySelector(".remove-field").addEventListener("click", function () {
                newField.remove();
            });

            // Lắng nghe sự kiện thay đổi Form Type
            const formTypeSelect = newField.querySelector(".form-type-select");
            
        });
    });

    // Debug dữ liệu gửi đi
    document.querySelectorAll(".modal form").forEach(form => {
        form.addEventListener("submit", function (e) {
            const formData = new FormData(this);
            console.log("Form data before submit:");
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
        });
    });
});