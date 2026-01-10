@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="container-fluid">
    <div class="card">

      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="card-title mb-0">
          <i class="fas fa-pen me-2"></i>
          {{ trans('core/base::layouts.edit_ranks') }}
        </h4>
      </div>

      <div class="card-body">
        <form method="POST" action="{{ route('rank.update', $data->id) }}" enctype="multipart/form-data"
          class="form-horizontal">
          @csrf
          @method('PUT')

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
              <input type="text" name="rank_name" id="rankName"
                class="form-control @error('rank_name') is-invalid @enderror"
                value="{{ old('rank_name', $data->rank_name) }}" required>
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
              <input type="text" name="rank_lavel" id="rankLevel"
                class="form-control @error('rank_lavel') is-invalid @enderror"
                value="{{ old('rank_lavel', $data->rank_lavel) }}" required>
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
            <label for="number_referrals" class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.number_referrals') }}
              <span class="text-danger">*</span>
            </label>
            <div class="col-md-6">
              <input type="number" name="number_referrals" id="number_referrals"
                class="form-control @error('number_referrals') is-invalid @enderror"
                value="{{ old('number_referrals', $data->number_referrals) }}" step="1" required>
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
              <input type="number" name="total_revenue" id="totalRevenue"
                class="form-control @error('total_revenue') is-invalid @enderror"
                value="{{ old('total_revenue', $data->total_revenue) }}" step="1000" required>
              @error('total_revenue')
                <span class="text-danger small d-block mt-1">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="mb-3 row">
            <label for="rankingDateConditions" class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.ranking_date_conditions') }}
            </label>
            <div class="col-md-6">
              <input type="number" name="ranking_date_conditions" id="rankingDateConditions"
                class="form-control @error('ranking_date_conditions') is-invalid @enderror"
                value="{{ old('ranking_date_conditions', $data->ranking_date_conditions) }}" step="1" min="0">
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
            <label for="demotionInvestment" class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.total_revenue_if_not_achieved') }}
            </label>
            <div class="col-md-6">
              <input type="number" name="demotion_investment" id="demotionInvestment"
                class="form-control @error('demotion_investment') is-invalid @enderror"
                value="{{ old('demotion_investment', $data->demotion_investment) }}" step="1000000">
              @error('demotion_investment')
                <span class="text-danger small d-block mt-1">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="mb-3 row">
            <label for="demotionReferrals" class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.number_of_referrals_if_not_met') }}
            </label>
            <div class="col-md-6">
              <input type="number" name="demotion_referrals" id="demotionReferrals"
                class="form-control @error('demotion_referrals') is-invalid @enderror"
                value="{{ old('demotion_referrals', $data->demotion_referrals) }}" step="1">
              @error('demotion_referrals')
                <span class="text-danger small d-block mt-1">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="mb-3 row">
            <label for="demotionTime" class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.demotion_time_months') }}
            </label>
            <div class="col-md-6">
              <input type="number" name="demotion_time_months" id="demotionTime"
                class="form-control @error('demotion_time_months') is-invalid @enderror"
                value="{{ old('demotion_time_months', $data->demotion_time_months) }}" step="1">
              @error('demotion_time_months')
                <span class="text-danger small d-block mt-1">{{ $message }}</span>
              @enderror
            </div>
          </div>



          {{-- REWARD LOGIC --}}
          <div class="mt-4 mb-4">
            <h5 class="fw-bold text-info border-bottom pb-2">
              <i class="fas fa-gift me-2"></i>
              {{ trans('core/base::layouts.rewards_and_addtional') }}
            </h5>
          </div>

          <div class="mb-3 row">
            <label for="reward_type" class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.reward_type') }}
            </label>
            <div class="col-md-6">
              <select name="reward_type" id="reward_type" class="form-control @error('reward_type') is-invalid @enderror">
                <option value="percent" {{ old('reward_type', $data->reward_type) == 'percent' ? 'selected' : '' }}>
                  {{ trans('core/base::layouts.percent') }}
                </option>
                <option value="amount" {{ old('reward_type', $data->reward_type) == 'amount' ? 'selected' : '' }}>
                  {{ trans('core/base::layouts.amount') }}
                </option>
              </select>
              @error('reward_type')
                <span class="text-danger small d-block mt-1">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="mb-3 row">
            <label for="reward_value" class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.reward_value') }}
              <span class="text-danger">*</span>
            </label>
            <div class="col-md-6">
              <input type="number" name="reward_value" id="reward_value" step="1"
                class="form-control @error('reward_value') is-invalid @enderror"
                value="{{ old('reward_value', $data->reward_value) }}" required>
              @error('reward_value')
                <span class="text-danger small d-block mt-1">{{ $message }}</span>
              @enderror
            </div>
          </div>

          {{-- HIDDEN FOR JS --}}
          <input type="hidden" id="original_percentage_reward" value="{{ $data->percentage_reward }}">
          <input type="hidden" id="total_revenue" value="{{ $data->total_revenue }}">

          {{-- STATUS --}}
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.status') }}
            </label>
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" {{ $data->status ? 'checked' : '' }}>
                <label class="form-check-label ms-2" for="statusSwitch">
                  <span class="badge bg-{{ $data->status ? 'success' : 'danger' }} text-white">
                    {{ $data->status ? trans('core/base::layouts.active') : trans('core/base::layouts.inactive') }}
                  </span>
                </label>
              </div>
            </div>
          </div>

          {{-- ICON UPLOAD --}}
          <div class="mb-3 row">
            <label for="image" class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.icon') }}
            </label>
            <div class="col-md-6">
              <input type="file" name="rank_icon" id="image" class="form-control @error('rank_icon') is-invalid @enderror"
                accept="image/*">
              @error('rank_icon')
                <span class="text-danger small d-block mt-1">{{ $message }}</span>
              @enderror

              @if ($data->rank_icon)
                <div class="mt-2">
                  <img id="image_preview_container" class="img-thumbnail" src="{{ asset($data->rank_icon) }}" alt="Preview"
                    style="max-height: 150px;">
                </div>
              @else
                <img id="image_preview_container" class="img-thumbnail mt-2 d-none" src="" alt="Preview"
                  style="max-height: 150px;">
              @endif
            </div>
          </div>

          {{-- DESCRIPTION --}}
          <div class="mb-3 row">
            <label for="description" class="col-md-3 col-form-label text-md-end">
              {{ trans('core/base::layouts.description') }}
            </label>
            <div class="col-md-6">
              <textarea name="description" id="description"
                class="form-control @error('description') is-invalid @enderror"
                rows="4">{{ old('description', $data->description) }}</textarea>
              @error('description')
                <span class="text-danger small d-block mt-1">{{ $message }}</span>
              @enderror
            </div>
          </div>

          {{-- ACTIONS --}}
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
      const rewardTypeSelect = document.getElementById('reward_type');
      const rewardInput = document.getElementById('reward_value');
      const originalPercent = parseFloat(document.getElementById('original_percentage_reward').value);
      const totalRevenue = parseFloat(document.getElementById('total_revenue').value);

      function updateRewardInput() {
        const type = rewardTypeSelect.value;

        if (type === 'amount') {
          const amount = (originalPercent / 100) * totalRevenue;
          rewardInput.value = amount.toFixed(2);
        } else {
          rewardInput.value = originalPercent.toFixed(2);
        }
      }

      rewardTypeSelect.addEventListener('change', updateRewardInput);
      updateRewardInput();

      // Image preview
      const imageInput = document.getElementById('image');
      const imagePreview = document.getElementById('image_preview_container');

      if (imageInput) {
        imageInput.addEventListener('change', function () {
          const file = this.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
              imagePreview.src = e.target.result;
              imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
          }
        });
      }
    });
  </script>
@endpush
