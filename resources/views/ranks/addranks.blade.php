@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="container-fluid">
      <div class="card">

          <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
              <h4 class="card-title mb-0">
                  <i class="fas fa-crown me-2"></i>
                  {{ trans('core/base::layouts.create_new_rank') }}
              </h4>

              <div class="card-toolbar">
                  <a href="{{ route('rank.index') }}" class="btn btn-outline-secondary btn-sm">
                      <i class="fas fa-arrow-left me-1"></i>
                      {{ trans('core/base::layouts.back') }}
                  </a>
              </div>
          </div>

          <div class="card-body">
              <form method="post"
                    action="{{ route('rank.store') }}"
                    enctype="multipart/form-data"
                    class="form-horizontal">
                  @csrf

                  {{-- BASIC INFO --}}
                  <div class="mb-4">
                      <h5 class="fw-bold text-primary border-bottom pb-2">
                          <i class="fas fa-info-circle me-2"></i>
                          {{ trans('core/base::layouts.basic_information') }}
                      </h5>
                  </div>

                  <div class="mb-3 row">
                      <label for="rankName" class="col-md-3 col-form-label text-md-end">
                          {{ trans('core/base::layouts.rank_name') }}
                          <span class="text-danger">*</span>
                      </label>
                      <div class="col-md-6">
                          <input type="text"
                                 name="rank_name"
                                 id="rankName"
                                 class="form-control @error('rank_name') is-invalid @enderror"
                                 value="{{ old('rank_name') }}"
                                 required>
                          @error('rank_name')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>

                  <div class="mb-3 row">
                      <label for="rankLevel" class="col-md-3 col-form-label text-md-end">
                          {{ trans('core/base::layouts.rank_level') }}
                          <span class="text-danger">*</span>
                      </label>
                      <div class="col-md-6">
                          <input type="text"
                                 name="rank_lavel"
                                 id="rankLevel"
                                 class="form-control @error('rank_lavel') is-invalid @enderror"
                                 value="{{ old('rank_lavel') }}"
                                 required>
                          @error('rank_lavel')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>

                  {{-- MAINTAIN RANK --}}
                  <div class="mt-4 mb-4">
                      <h5 class="fw-bold text-success border-bottom pb-2">
                          <i class="fas fa-arrow-up me-2"></i>
                          {{ trans('core/base::layouts.maintain_ranks') }}
                      </h5>
                  </div>

                  <div class="mb-3 row">
                      <label for="numberReferrals" class="col-md-3 col-form-label text-md-end">
                          {{ trans('core/base::layouts.number_referrals') }}
                          <span class="text-danger">*</span>
                      </label>
                      <div class="col-md-6">
                          <input type="number"
                                 name="number_referrals"
                                 id="numberReferrals"
                                 class="form-control @error('number_referrals') is-invalid @enderror"
                                 value="{{ old('number_referrals') }}"
                                 required>
                          @error('number_referrals')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>

                  <div class="mb-3 row">
                      <label for="totalRevenue" class="col-md-3 col-form-label text-md-end">
                          {{ trans('core/base::layouts.total_revenue') }}
                          <span class="text-danger">*</span>
                      </label>
                      <div class="col-md-6">
                          <input type="number"
                                 name="total_revenue"
                                 id="totalRevenue"
                                 class="form-control @error('total_revenue') is-invalid @enderror"
                                 value="{{ old('total_revenue') }}"
                                 step="1000000"
                                 required>
                          @error('total_revenue')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>
                  <div class="mb-3 row">
                    <label class="col-md-3 col-form-label text-md-end" for="ranking_date_conditions">
                      {{ trans('core/base::layouts.ranking_date_conditions') }}
                    </label>
                    <div class="col-md-6">
                      <input type="number" name="ranking_date_conditions" id="ranking_date_conditions"
                        class="form-control @error('ranking_date_conditions') is-invalid @enderror"
                        value="{{ old('ranking_date_conditions') }}" min="0">
                      @error('ranking_date_conditions')
                        <span class="text-danger small d-block mt-1">{{ $message }}</span>
                      @enderror
                    </div>
                  </div>

                  {{-- DEMOTION CONDITIONS --}}
                  <div class="mt-4 mb-4">
                      <h5 class="fw-bold text-danger border-bottom pb-2">
                          <i class="fas fa-arrow-down me-2"></i>
                          {{ trans('core/base::layouts.demotion_conditions') }}
                      </h5>
                  </div>

                  <div class="mb-3 row">
                      <label class="col-md-3 col-form-label text-md-end" for="demotion_investment">
                          {{ trans('core/base::layouts.total_revenue_if_not_achieved') }}
                      </label>
                      <div class="col-md-6">
                          <input type="number"
                                 name="demotion_investment"
                                 id="demotion_investment"
                                 class="form-control @error('demotion_investment') is-invalid @enderror"
                                 value="{{ old('demotion_investment') }}"
                                 step="1000000">
                          @error('demotion_investment')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>

                  <div class="mb-3 row">
                      <label class="col-md-3 col-form-label text-md-end" for="demotion_referrals">
                          {{ trans('core/base::layouts.number_of_referrals_if_not_met') }}
                      </label>
                      <div class="col-md-6">
                          <input type="number"
                                 name="demotion_referrals"
                                 id="demotion_referrals"
                                 class="form-control @error('demotion_referrals') is-invalid @enderror"
                                 value="{{ old('demotion_referrals') }}">
                          @error('demotion_referrals')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>

                  <div class="mb-3 row">
                      <label class="col-md-3 col-form-label text-md-end" for="demotion_time_months">
                          {{ trans('core/base::layouts.demotion_time_months') }}
                      </label>
                      <div class="col-md-6">
                          <input type="number"
                                 name="demotion_time_months"
                                 id="demotion_time_months"
                                 class="form-control @error('demotion_time_months') is-invalid @enderror"
                                 value="{{ old('demotion_time_months') }}">
                          @error('demotion_time_months')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>



                  {{-- REWARDS --}}
                  <div class="mt-4 mb-4">
                      <h5 class="fw-bold text-info border-bottom pb-2">
                          <i class="fas fa-gift me-2"></i>
                          {{ trans('core/base::layouts.rewards_and_addtional') }}
                      </h5>
                  </div>

                  <div class="mb-3 row">
                      <label for="rewardType" class="col-md-3 col-form-label text-md-end">
                          {{ trans('core/base::layouts.reward_type') }}
                          <span class="text-danger">*</span>
                      </label>
                      <div class="col-md-6">
                          <select class="form-control @error('reward_type') is-invalid @enderror"
                                  id="rewardType"
                                  name="reward_type"
                                  required>
                              <option value="percent" {{ old('reward_type') == 'percent' ? 'selected' : '' }}>
                                  {{ trans('core/base::layouts.percent') }}
                              </option>
                              <option value="amount" {{ old('reward_type') == 'amount' ? 'selected' : '' }}>
                                  {{ trans('core/base::layouts.amount') }}
                              </option>
                          </select>
                          @error('reward_type')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>

                  <div class="mb-3 row">
                      <label for="rewardValue" class="col-md-3 col-form-label text-md-end">
                          {{ trans('core/base::layouts.reward_value') }}
                          <span class="text-danger">*</span>
                      </label>
                      <div class="col-md-6">
                          <input type="number"
                                 step="0.01"
                                 name="reward_value"
                                 id="rewardValue"
                                 class="form-control @error('reward_value') is-invalid @enderror"
                                 value="{{ old('reward_value') }}"
                                 required>
                          @error('reward_value')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>

                  {{-- ICON --}}
                  <div class="mt-4 mb-4">
                      <h5 class="fw-bold border-bottom pb-2">
                          <i class="fas fa-image me-2"></i>
                          {{ trans('core/base::layouts.icon') }}
                      </h5>
                  </div>

                  <div class="mb-3 row">
                      <label class="col-md-3 col-form-label text-md-end" for="rank_icon">
                          {{ trans('core/base::layouts.icon') }}
                      </label>
                      <div class="col-md-6">
                          <input type="file"
                                 name="rank_icon"
                                 id="rank_icon"
                                 class="form-control @error('rank_icon') is-invalid @enderror"
                                 accept="image/*">

                          @error('rank_icon')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror

                          <div class="mt-2">
                              <img id="image_preview_container"
                                   class="img-thumbnail"
                                   src="{{ asset('default-image.png') }}"
                                   alt="Preview"
                                   style="max-height: 150px;">
                          </div>
                      </div>
                  </div>

                  {{-- DESCRIPTION --}}
                  <div class="mb-3 row">
                      <label for="description" class="col-md-3 col-form-label text-md-end">
                          {{ trans('core/base::layouts.description') }}
                      </label>
                      <div class="col-md-6">
                          <textarea name="description"
                                    id="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="4">{{ old('description') }}</textarea>
                          @error('description')
                              <span class="text-danger small d-block mt-1">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>

                  {{-- STATUS --}}
                  <div class="mb-3 row">
                      <label class="col-md-3 col-form-label text-md-end">
                          {{ trans('core/base::layouts.status') }}
                      </label>
                      <div class="col-md-6 d-flex align-items-center">
                          <div class="form-check form-switch">
                              <input class="form-check-input"
                                     type="checkbox"
                                     name="status"
                                     id="statusSwitch"
                                     checked>
                              <label class="form-check-label ms-2" for="statusSwitch">
                                  <span class="badge bg-success text-white">
                                      {{ trans('core/base::layouts.active') }}
                                  </span>
                              </label>
                          </div>
                          @error('status')
                              <span class="text-danger small d-block ms-3">{{ $message }}</span>
                          @enderror
                      </div>
                  </div>

                  {{-- ACTION --}}
                  <div class="mt-4 row">
                      <div class="col-md-9 offset-md-3">
                          <button type="submit" class="btn btn-primary">
                              <i class="fas fa-save me-1"></i>
                              {{ trans('core/base::layouts.save_now') }}
                          </button>
                      </div>
                  </div>

              </form>
          </div>
      </div>
  </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageInput = document.getElementById('rank_icon');
            const imagePreview = document.getElementById('image_preview_container');

            if (imageInput) {
                imageInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            imagePreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endpush
