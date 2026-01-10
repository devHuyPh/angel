<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kyc.form.create') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="createModalLabel"><i class="fa fa-sync-alt"></i>{{ trans('core/base::layouts.create_identity_form')}}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <label for="status" class="form-label"><strong>{{ trans('core/base::layouts.name')}}:</strong></label>
                    <input type="text" class="form-control" name="name" id="" value="" placeholder="Name">
                    <div class="row">
                        <div class="col-md-8 mb-3 mt-3">
                            <label for="status" class="form-label"><strong>{{ trans('core/base::layouts.status')}}:</strong></label>
                            <select class="form-control" name="status" id="status" required>
                                <option disabled selected>{{ trans('core/base::layouts.select_status')}}</option>
                                <option value="1" selected>{{ trans('core/base::layouts.active')}}</option>
                                <option value="0">{{ trans('core/base::layouts.deactive')}}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <a href="#" class="btn btn-success btn-sm w-100 generate form-control" id="add-field">
                                <i class="fa fa-plus-circle"></i>{{ trans('core/base::layouts.add_field')}}
                            </a>
                        </div>
                    </div>

                    <div class="addedField mt-3">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary p-2 d-flex justify-content-between">
                                    <h5 class="card-title text-white font-weight-bold">{{ trans('core/base::layouts.field_information')}}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label>{{ trans('core/base::layouts.field_name')}}</label>
                                            <input name="field_name[]" class="form-control" value="" type="text" required
                                                placeholder={{ trans('core/base::layouts.field_name') }}>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>{{ trans('core/base::layouts.form_type')}}</label>
                                            <select name="type[]" class="form-control">
                                                <option value="text">{{ trans('core/base::layouts.input_text')}}</option>
                                                <option value="textarea">{{ trans('core/base::layouts.text_area')}}</option>
                                                <option value="file" selected="selected">{{ trans('core/base::layouts.file_upload')}}</option>
                            					<option value="datetime" >{{ trans('core/base::layouts.date_time')}}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>{{ trans('core/base::layouts.field_length')}}</label>
                                            <input name="field_length[]" class="form-control" type="number" min="2"
                                                required value="" placeholder={{ trans('core/base::layouts.field_length') }}>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>{{ trans('core/base::layouts.field_length_type')}}</label>
                                            <select name="length_type[]" class="form-control">
                                                <option value="max" selected="selected">{{ trans('core/base::layouts.maximum_length')}}</option>
                                                <option value="digits">{{ trans('core/base::layouts.fixed_length')}}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>{{ trans('core/base::layouts.form_validation')}}</label>
                                            <select name="validation[]" class="form-control">
                                                <option value="required" selected="selected">{{ trans('core/base::layouts.required')}}</option>
                                                <option value="nullable">{{ trans('core/base::layouts.nullable')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="fieldsContainer"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">{{ trans('core/base::layouts.close')}}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('core/base::layouts.create')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>