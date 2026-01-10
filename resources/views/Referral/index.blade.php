@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('title', trans('core/base::layouts.referral_comission'))

@section('content')
    <div class=" m-0 m-md-4 my-4 m-md-0">
        <div class="row justify-content-between ">
            <div class="col-md-5">
                <div class="card card-primary shadow">
                    <div class="card-body">
                        <form method="post" action="{{ route('referral.save') }}"
                            class="form-row align-items-center justify-content-between">
                            @csrf
                            <div class="form-group col-md-6">
                                <div class="d-flex align-items-center">
                                    <label class="font-weight-bold me-3">{{ trans('core/base::layouts.investment_commission')}}</label>
                                    <div class="form-check form-switch switch">
                                        <input type="hidden" value="1" name="investment_commission">
                                        <input type="checkbox" name="investment_commission" class="form-check-input"
                                            id="investment_commission" value="0" <?php
                                            if (setting('investment_commission') == 0):
                                                echo 'checked';
                                            endif;
                                            ?>>
                                        <label class="form-check-label" for="investment_commission"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="d-flex align-items-center">
                                    <label class="font-weight-bold me-3">{{ trans('core/base::layouts.upline_deposit_bonus')}}</label>
                                    <div class="form-check form-switch switch">
                                        <input type="hidden" value="1" name="deposit_commission">
                                        <input type="checkbox" name="deposit_commission" class="form-check-input"
                                            id="deposit_commission" value="0" <?php
                                            if (setting('deposit_commission') == 0):
                                                echo 'checked';
                                            endif;
                                            ?>>
                                        <label class="form-check-label" for="deposit_commission"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="d-flex align-items-center">
                                    <label class="font-weight-bold me-3">{{ trans('core/base::layouts.profit_commission')}}</label>
                                    <div class="form-check form-switch switch">
                                        <input type="hidden" value="1" name="profit_commission">
                                        <input type="checkbox" name="profit_commission" class="form-check-input"
                                            id="profit_commission" value="0"
                                            {{ setting('profit_commission') == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="profit_commission"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group  col-md-6">
                                <button type="submit" class="btn btn-primary btn-block btn-rounded  mt-4 mx-2">
                                    <i class="fas fa-save"></i><span>{{ trans('core/base::layouts.save_changes')}}</span></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card card-primary shadow">
                    <div class="card-body">
                        <h5 class="card-title">{{ trans('core/base::layouts.investment_bonus')}}</h5>
                        <div class="table-responsive">
                            <table class="categories-show-table table table-hover table-striped table-bordered"
                                id="zero_config">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">{{ trans('core/base::layouts.level')}}</th>
                                        <th scope="col">{{ trans('core/base::layouts.bonus')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($referrals->where('commission_type','invest') as $item)
                                        <tr>
                                            <td data-label="Level">@lang('LEVEL')# {{ $item->level }}</td>

                                            <td data-label="@lang('Bonus')">
                                                {{ $item->percent }} %
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">{{ trans('core/base::layouts.no_data_found')}}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <h5 class="card-title">{{ trans('core/base::layouts.funding_bonus')}}</h5>
                        <div class="table-responsive">
                            <table class="categories-show-table table table-hover table-striped table-bordered"
                                id="zero_config">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">{{ trans('core/base::layouts.level')}}</th>
                                        <th scope="col">{{ trans('core/base::layouts.bonus')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($referrals->where('commission_type','deposit') as $item)
                                        <tr>
                                            <td data-label="Level">{{ trans('core/base::layouts.level')}}# {{ $item->level }}</td>
                                            <td data-label="@lang('Bonus')">
                                                {{ $item->percent }} %
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">{{ trans('core/base::layouts.no_data_found')}}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <h5 class="card-title">{{ trans('core/base::layouts.profit_commission')}}</h5>
                        <div class="table-responsive">
                            <table class="categories-show-table table table-hover table-striped table-bordered"
                                id="zero_config">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">{{ trans('core/base::layouts.level')}}</th>
                                        <th scope="col">{{ trans('core/base::layouts.bonus')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($referrals->where('commission_type','profit_commission') as $item)
                                        <tr>
                                            <td data-label="Level">{{ trans('core/base::layouts.level')}}# {{ $item->level }}</td>
                                            <td data-label="Bonus">
                                                {{ $item->percent }} %
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">{{ trans('core/base::layouts.no_data_found')}}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card card-primary shadow">
                    <div class="card-body">
                        <div class="row  formFiled justify-content-between ">
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label class="font-weight-bold">{{ trans('core/base::layouts.select_type')}}</label>
                                    <select name="type" class="form-control  type">
                                        <option value="" disabled>{{ trans('core/base::layouts.select_type')}}</option>
                                        <option value="invest">{{ trans('core/base::layouts.investment_bonus')}}</option>
                                        <option value="deposit">{{ trans('core/base::layouts.funding_bonus')}}</option>
                                        <option value="profit_commission">{{ trans('core/base::layouts.profit_commission')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ trans('core/base::layouts.set_level')}}</label>
                                    <input type="number" name="level" placeholder="Number Of Level"
                                        class="form-control  numberOfLevel">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-primary btn-block makeForm "
                                        style="margin-top: 20px">
                                        <i class="fa fa-spinner"></i>{{ trans('core/base::layouts.generate')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('referral.action') }}" method="post" class="form-row">
                            @csrf
                            <input type="hidden" name="commission_type" value="">
                            <div class="col-md-12 newFormContainer">
                            </div>
                            <div class="col-md-12">
                                <button type="submit"
                                    class="btn btn-primary btn-block mt-3 submit-btn">{{ trans('core/base::layouts.submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>





<style>
  .switch{
    margin-top: 10px;
  }
</style>
@endsection
@push('style-lib')
@endpush
@push('js')
    @if ($errors->any())
        @php
            $collection = collect($errors->all());
            $errors = $collection->unique();
        @endphp
        <script>
            "use strict";
            @foreach ($errors as $error)
                Notiflix.Notify.Failure("{{ trans($error) }}");
            @endforeach
        </script>
    @endif

    <script>
        "use strict";
        $(document).ready(function() {

            $('.submit-btn').addClass('d-none');

            $(".makeForm").on('click', function() {

                var levelGenerate = $(this).parents('.formFiled').find('.numberOfLevel').val();
                var selectType = $('.type :selected').val();
                if (selectType == '') {
                    Notiflix.Notify.Failure("{{ trans('Please Select a type') }}");
                    return 0
                }

                $('input[name=commission_type]').val(selectType)
                var value = 1;
                var viewHtml = '';
                if (levelGenerate !== '' && levelGenerate > 0) {
                    for (var i = 0; i < parseInt(levelGenerate); i++) {
                        viewHtml += `<div class="input-group mt-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text no-right-border">LEVEL</span>
                            </div>
                            <input name="level[]" class="form-control" type="number" readonly value="${value++}" required placeholder="@lang('Level')">
                            <input name="percent[]" class="form-control" type="text" required placeholder="@lang('Level Bonus (%)')">
                            <span class="input-group-btn">
                            <button class="btn btn-danger removeForm" type="button"><i class='fa fa-trash'></i></button></span>
                            </div>`;
                    }

                    $('.newFormContainer').html(viewHtml);
                    $('.submit-btn').addClass('d-block');
                    $('.submit-btn').removeClass('d-none');

                } else {

                    $('.submit-btn').addClass('d-none');
                    $('.submit-btn').removeClass('d-block');
                    $('.newFormContainer').html(``);
                    Notiflix.Notify.Failure("{{ trans('Please Set number of level') }}");
                }
            });

            $(document).on('click', '.removeForm', function() {
                $(this).closest('.input-group').remove();
            });


            $('select').select2({
                selectOnClose: true
            });

        });
    </script>
@endpush
