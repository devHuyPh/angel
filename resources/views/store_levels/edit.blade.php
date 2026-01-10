@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
    <div class="row justify-content-center">
    <div class="col-md-12">

      <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="mb-0">
      <i class="fas fa-edit me-2"></i>{{ trans('core/base::layouts.edit_title') }}
      </h4>
      <a href="{{ route('store-levels.index') }}" class="btn btn-secondary btn-sm">
      <i class="fas fa-arrow-left me-1"></i> {{ trans('core/base::layouts.back') }}
      </a>
      </div>

      <div class="card-body">
      <form action="{{ route('store-levels.editstore-levels.update', $storeLevel->id) }}" method="POST" novalidate>
      @csrf

      <div class="mb-3">
        <label for="name" class="form-label">{{ trans('core/base::layouts.name') }}</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
        value="{{ old('name', $storeLevel->name) }}" required autofocus>
        @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      </div>

      
              <div class="mb-3">
        <label for="commission" class="form-label">{{ trans('core/base::layouts.commission') }} (%)</label>
        <input type="number" class="form-control @error('value') is-invalid @enderror" id="commission" name="commission"
        value="{{ old('commission', $storeLevel->commission) }}" required min="0" max="100">
        @error('commission')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      </div>


      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i> {{ trans('core/base::layouts.update') }}
      </button>
      <a href="{{ route('store-levels.index') }}" class="btn btn-secondary ms-2">
        {{ trans('core/base::layouts.cancel') }}
      </a>
      </form>
      </div>
      </div>

    </div>
    </div>
    </div>
@endsection
