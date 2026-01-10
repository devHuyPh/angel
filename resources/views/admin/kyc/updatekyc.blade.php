<div class="modal fade" id="updateFiled_{{$item->id}}" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kyc.form.update', $item->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title" id="createModalLabel"><i class="fa fa-sync-alt"></i>{{ trans('core/base::layouts.update_identity_form')}}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <label for="status" class="form-label"><strong>{{ trans('core/base::layouts.name')}}:</strong></label>
                    <input type="text" class="form-control" name="name" id="" value="{{$item->name}}" placeholder="Name">
                    <div class="row">
                        <div class="col-md-8 mb-3 mt-3">
                            <label for="status" class="form-label"><strong>{{ trans('core/base::layouts.status')}}:</strong></label>
                            <select class="form-control" name="status" id="status" required>
                                <option disabled selected>{{ trans('core/base::layouts.select_status')}}</option>
                                <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>{{ trans('core/base::layouts.active')}}</option>
                                <option value="0" {{ $item->status != 1 ? 'selected' : '' }}>{{ trans('core/base::layouts.deactive')}}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <a href="#" id="add-field-btn_{{$item->id}}"
                                class="btn btn-success btn-sm w-100 generate form-control add-field-btn">
                                <i class="fa fa-plus-circle"></i> {{ trans('core/base::layouts.add_field')}}
                            </a>
                        </div>
                    </div>

                    @php
                        $formFields = json_decode($item->form, true);
                    @endphp

                    @if($formFields && isset($formFields['field_name']) && isset($formFields['field_labels']))
                        @foreach($formFields['field_name'] as $index => $fieldName)
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
                                                    <input name="field_name_{{$item->id}}[]" class="form-control"
                                                        value="{{ $formFields['field_labels'][$index] ?? $fieldName }}" type="text" required placeholder="Field Name">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{ trans('core/base::layouts.form_type')}}</label>
                                                    <select name="type_{{$item->id}}[]" class="form-control">
                                                        <option value="text" {{ $formFields['type'][$index] == 'text' ? 'selected' : '' }}>{{ trans('core/base::layouts.input_text')}}</option>
                                                        <option value="textarea" {{ $formFields['type'][$index] == 'textarea' ? 'selected' : '' }}>{{ trans('core/base::layouts.text_area')}}</option>
                                                        <option value="file" {{ $formFields['type'][$index] == 'file' ? 'selected' : '' }}>{{ trans('core/base::layouts.file_upload')}}</option>
                                                        <option value="datetime" {{ $formFields['type'][$index] == 'datetime' ? 'selected' : '' }}>{{ trans('core/base::layouts.date_time')}}</option>

                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{ trans('core/base::layouts.field_length')}}</label>
                                                    <input name="field_length_{{$item->id}}[]" class="form-control" type="number"
                                                        min="2" required value="{{ $formFields['field_length'][$index] }}"
                                                        placeholder="Length">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{ trans('core/base::layouts.field_length_type')}}</label>
                                                    <select name="length_type_{{$item->id}}[]" class="form-control">
                                                        <option value="max" {{ $formFields['length_type'][$index] == 'max' ? 'selected' : '' }}>{{ trans('core/base::layouts.maximum_length')}}</option>
                                                        <option value="digits" {{ $formFields['length_type'][$index] == 'digits' ? 'selected' : '' }}>{{ trans('core/base::layouts.fixed_length')}}</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>{{ trans('core/base::layouts.form_validation')}}</label>
                                                    <select name="validation_{{$item->id}}[]" class="form-control">
                                                        <option value="required" {{ $formFields['validation'][$index] == 'required' ? 'selected' : '' }}>{{ trans('core/base::layouts.required')}}</option>
                                                        <option value="nullable" {{ $formFields['validation'][$index] == 'nullable' ? 'selected' : '' }}>{{ trans('core/base::layouts.optional')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div id="fieldsContainer_{{$item->id}}"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">{{ trans('core/base::layouts.close')}}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('core/base::layouts.update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>