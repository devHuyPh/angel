document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("add-field").addEventListener("click", function (event) {
        event.preventDefault(); // Ngăn chặn load lại trang

        // Tạo một div chứa form mới
        let newField = document.createElement("div");
        newField.classList.add("addedField", "mt-3");

        newField.innerHTML = `
            <div class="col-md-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary p-2 d-flex justify-content-between">
                        <h5 class="card-title text-white font-weight-bold">Field information</h5>
                        <button class="btn btn-danger btn-sm delete_desc" type="button">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Field Name</label>
                                <input name="field_name[]" class="form-control" value="" type="text" required placeholder="Field Name">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Form Type</label>
                                <select name="type[]" class="form-control">
                                    <option value="text">Input Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="file" selected>File upload</option>
                                    <option value="datetime" > Date Time </option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Field Length</label>
                                <input name="field_length[]" class="form-control" type="number" min="2" required value="" placeholder="Length">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Field Length Type</label>
                                <select name="length_type[]" class="form-control">
                                    <option value="max" selected>Maximum Length</option>
                                    <option value="digits">Fixed Length</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Form Validation</label>
                                <select name="validation[]" class="form-control">
                                    <option value="required" selected>Required</option>
                                    <option value="nullable">Optional</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Thêm vào container
        document.getElementById("fieldsContainer").appendChild(newField);

        // Xóa Field khi nhấn vào nút delete
        newField.querySelector(".delete_desc").addEventListener("click", function () {
            newField.remove();
        });
    });
});
document.querySelectorAll(".add-field-btn").forEach(button => {
    button.addEventListener("click", function (e) {
        e.preventDefault();

        // Lấy ID của item từ nút bấm
        let itemId = this.id.split("_")[1]; // Tách ID từ add-field-btn_{id}

        // Tìm modal chứa nút này
        let modal = this.closest(".modal");

        // Tìm container chứa các trường trong modal tương ứng
        let container = modal.querySelector(`#fieldsContainer_${itemId}`);

        // Mẫu trường nhập liệu mới với ID động
        let fieldHTML = `
        <div class="addedField mt-3">
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
                            <div class="col-md-4 form-group">
                                <label>Form Type</label>
                                <select name="type_${itemId}[]" class="form-control">
                                    <option value="text">Input Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="file" selected>File upload</option>
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
        </div>`;

        // Thêm trường mới vào container
        container.insertAdjacentHTML("beforeend", fieldHTML);
    });
});

// Xóa trường nhập liệu khi bấm "Remove"
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-field") || e.target.closest(".remove-field")) {
        e.target.closest(".addedField").remove();
    }
});



document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            let url = this.getAttribute("data-url");
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

            if (confirm("Are you sure you want to delete this form?")) {
                fetch(url, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": token,
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Form deleted successfully!");
                        location.reload(); // Refresh trang sau khi xoá
                    } else {
                        alert("Error deleting form!");
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        });
    });
});



