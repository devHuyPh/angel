@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
  <div class="container">
    <h1>{{ trans('core/base::layouts.add_manager') }}</h1>

    <form action="{{ route('admin.manager.store') }}" method="POST">
      @csrf

      <!-- Manager Name -->
      <div class="form-group mb-3">
        <label for="manager_name">{{ trans('core/base::layouts.manager_name') }}</label>
        <input type="text" name="manager_name" class="form-control" required>
      </div>
      <div class="form-group mb-3">
        <label for="hash">{{ trans('core/base::layouts.hash') }}</label>
        <input type="text" name="hash" class="form-control" value="{{ Str::random(10) }}" readonly>
      </div>

      <!-- Customer -->
      <div class="form-group mb-3">
        <label for="customer_id">{{ trans('core/base::layouts.customer_name') }}</label>
        <select name="customer_id" class="form-control" required>
          @foreach($customers as $customer)
            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- States -->
      <div class="form-group mb-3">
        <label for="state_ids">{{ trans('core/base::layouts.state_names') }}</label>
        <select name="state_ids[]" id="state_ids" class="form-control select2" multiple required>
          @foreach($states as $state)
            <option value="{{ $state->id }}">{{ $state->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- Submit -->
      <button type="submit" class="btn btn-primary">{{ trans('core/base::layouts.Save') }}</button>
      <a href="{{ route('admin.manager.index') }}" class="btn btn-secondary">{{ trans('core/base::layouts.cancel') }}</a>
    </form>
  </div>
@endsection

@push('style-lib')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/jquery-ui.min.css') }}" rel="stylesheet">
@endpush

@push('js')
    <!-- jQuery (Select2 yêu cầu jQuery) -->
    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- jQuery UI (nếu cần cho các tính năng khác) -->
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>

    <!-- Khởi tạo Select2 -->
    <script>
        $(document).ready(function() {
            $('#state_ids').select2({
                placeholder: "Chọn trạng thái",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
