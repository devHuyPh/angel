@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="container-fluid">
    <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="card-title mb-0">
      <i class="fas fa-link me-2"></i>{{ trans('core/base::layouts.assign_title') }}
      </h3>
    </div>

    <div class="card-body">
      @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
      </div>
    @endif

      <form action="{{ route('store-levels.assign.edit') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label for="level_id" class="form-label">
        {{ trans('core/base::layouts.select_level') }}
        </label>
        <select name="level_id" id="level_id" class="form-select" required>
        @foreach($levels as $level)
      <option value="{{ $level->id }}">
        {{ $level->name }} ({{ number_format($level->value) }} VNĐ)
      </option>
      @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">{{ trans('core/base::layouts.select_stores') }}</label>
        <div class="row">
        @foreach($stores as $store)
      <div class="col-md-4">
        <div class="form-check">
        <input class="form-check-input" type="checkbox" name="store_ids[]" value="{{ $store->id }}"
        id="store_{{ $store->id }}">
        <label class="form-check-label" for="store_{{ $store->id }}">
        {{ $store->name }}
        </label>
        </div>
      </div>
      @endforeach
        </div>
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="fas fa-paper-plane me-1"></i> {{ trans('core/base::layouts.assign_submit') }}
      </button>
      </form>
    </div>
    </div>
  </div>
@endsection
