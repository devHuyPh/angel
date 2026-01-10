@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
    <div class="row justify-content-center">
    <div class="col-md-12">

      <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>{{ trans('core/base::layouts.add_new') }}</h4>
      <a href="{{ route('store-levels.index') }}" class="btn btn-secondary btn-sm">
      <i class="fas fa-arrow-left me-1"></i> {{ trans('core/base::layouts.back') }}
      </a>
      </div>

      <div class="card-body">
      @if ($errors->any())
      <div class="alert alert-danger">
      <strong>{{ trans('core/base::layouts.error_title') }}</strong>
      <ul class="mb-0 mt-2">
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
      </ul>
      </div>
      @endif

      <form action="{{ route('store-levels.createstore-levels.store') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label for="name" class="form-label">{{ trans('core/base::layouts.name') }}:</label>
        <input type="text" class="form-control" name="name" id="name"
        value="{{ old('name', $storeLevel->name ?? '') }}" required>
      </div>

      <div class="mb-3">
        <label for="value" class="form-label">{{ trans('core/base::layouts.value') }} (VNĐ):</label>
        <input type="number" class="form-control" name="value" id="value"
        value="{{ old('value', $storeLevel->value ?? '') }}" required>
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i> {{ trans('core/base::layouts.save') }}
      </button>
      </form>
      </div>
      </div>

    </div>
    </div>
    </div>
@endsection
