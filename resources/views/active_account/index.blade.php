@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid" style="padding: 0; margin: 0; min-height: 100vh; display: flex; flex-direction: column; background: #f8f9fa;">
        <div class="card" style="margin: 0; border: none; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); border-radius: 10px; flex-grow: 1;">
            <div class="card-header" style="background: #ffffff; color: #fafafa; padding: 1rem; border-bottom: none;">
                <div class="container" style="max-width: 1200px; margin: 0 auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="color: #000000;  font-size: 1.5em;">{{ trans('core/base::layouts.active_account')}}</h5>
                        <a href="{{ route('active_account.edit') }}" class="btn btn-primary btn-sm shadow-sm text-light" style="border: 1px solid #ffffff; color: #000000; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9em; transition: all 0.3s ease;">
                            <i class="fas fa-edit"></i> {{ trans('core/base::layouts.edit')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 0; overflow: auto; background: #f8f9fa;">
                <div class="table-responsive" style="height: 100%;">
                    <table class="table table-hover table-striped align-middle" style="margin: 0; border-collapse: collapse; background: #ffffff;">
                        <thead class="table-dark sticky-top" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th scope="col" style="background: #e9ecef; color: #6c757d; font-weight: 500; text-transform: uppercase; padding: 10px 15px; border-bottom: 1px solid #dee2e6; text-align: left;">{{ trans('core/base::layouts.key')}}</th>
                                <th scope="col" style="background: #e9ecef; color: #6c757d; font-weight: 500; text-transform: uppercase; padding: 10px 15px; border-bottom: 1px solid #dee2e6; text-align: left;">{{ trans('core/base::layouts.value')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-id="1" style="background: #ffffff; border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 10px 15px; font-size: 1em; color: #333333; font-weight: 400; border: none;">{{ trans(key: 'core/base::layouts.active_account')}}</td>
                                <td style="padding: 10px 15px; font-size: 1em; color: #007bff; font-weight: 500; border: none;">{{format_price( $activeAcount) }}</td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <!-- Load Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
@endpush

@push('js')
    <!-- Load Bootstrap JS -->
    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
@endpush
