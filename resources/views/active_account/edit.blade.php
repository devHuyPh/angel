@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid p-0 m-0 vh-100 d-flex flex-column">
        <div class="card m-0 border-0 shadow flex-grow-1">
            <div class="card-header bg-primary text-white py-3">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white fw-bold">{{ trans('core/base::layouts.edit-active-account-fee')}}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 bg-light">
                <form method="POST" action="{{route('active_account.update')}}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="activeAcount" class="form-label fw-semibold">{{ trans('core/base::layouts.active-account-fee')}} </label>
                        <input 
        type="number" 
        class="form-control @error('active_account') is-invalid @enderror" 
        id="active_account" 
        name="active_account" 
        value="{{ old('activeAcount', $activeAcount ?? 0.00) }}" 
        min="0" 
        step="1000" 
        required
    >
    @error('active_account')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('active_account.index') }}" class="btn btn-outline-secondary">{{ trans('core/base::layouts.cancel')}}</a>
                        <button type="submit" class="btn btn-primary">{{ trans('core/base::layouts.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
@endpush

@push('style')
    <style>
        .card {
            border-radius: 0;
            background-color: #ffffff;
        }
        .form-control {
            border-radius: 5px;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .btn {
            border-radius: 5px;
        }
        .commission-note {
            display: block;
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9em;
            color: #6c757d; /* Màu xám nhạt */
            font-style: italic;
            padding-left: 0.5rem;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
@endpush