document.addEventListener("DOMContentLoaded", function () {
    const addFieldButton = document.getElementById("add-field");
    if (addFieldButton) {
        addFieldButton.addEventListener("click", function (event) {
            event.preventDefault();

            let newField = document.createElement("div");
            newField.classList.add("addedField", "mt-3");
            newField.innerHTML = `
                <div class="col-md-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary p-2 d-flex justify-content-between">
                            <h5 class="card-title text-white font-weight-bold">Field Information</h5>
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
                                        <option value="datetime" >Date time</option>

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

            document.getElementById("fieldsContainer").appendChild(newField);

            newField.querySelector(".delete_desc").addEventListener("click", function () {
                newField.remove();
            });
        });
    }
});