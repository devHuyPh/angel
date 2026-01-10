@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid" style="padding: 0; margin: 0; min-height: 100vh; display: flex; flex-direction: column; background: #f8f9fa;">
        <div class="card" style="margin: 0; border: none; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); border-radius: 10px; flex-grow: 1;">
            <div class="card-header" style="background: #ffffff; padding: 1rem; border-bottom: none;">
                <div class="container" style="max-width: 1200px; margin: 0 auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="margin: 0; color: #000000; font-size: 1.5em;">{{ trans('core/base::layouts.total_upline_revenue')}}</h5>
                        <a href="{{ route('totalrevenue.add') }}" class="btn btn-sm btn-outline-light shadow-sm" style="background: #007bff; border: 1px solid #ffffff; color: #ffffff; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9em; transition: all 0.3s ease;">
                            <i class="fas fa-plus"></i> {{ trans('core/base::layouts.add_new')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 0; overflow: auto; background: #f8f9fa;">
                <div class="table-responsive" style="height: 100%;">
                    <table class="table table-hover table-striped align-middle" style="margin: 0; border-collapse: collapse; background: #ffffff;">
                        <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th scope="col" style="background: #e9ecef; color: #6c757d; font-weight: 500; text-transform: uppercase; padding: 10px 15px; border-bottom: 1px solid #dee2e6; text-align: center;">{{ trans('core/base::layouts.no.')}}</th>
                                <th scope="col" style="background: #e9ecef; color: #6c757d; font-weight: 500; text-transform: uppercase; padding: 10px 15px; border-bottom: 1px solid #dee2e6; text-align: center;">{{ trans('core/base::layouts.amount')}}</th>
                                <th scope="col" style="background: #e9ecef; color: #6c757d; font-weight: 500; text-transform: uppercase; padding: 10px 15px; border-bottom: 1px solid #dee2e6; text-align: center;">{{ trans('core/base::layouts.percentage')}}</th>
                                <th scope="col" style="background: #e9ecef; color: #6c757d; font-weight: 500; text-transform: uppercase; padding: 10px 0px; border-bottom: 1px solid #dee2e6; text-align: center;">{{ trans('core/base::layouts.actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($totals as $index => $total)
                                <tr>
                                    <td style="padding: 10px 15px; text-align: center; font-size: 1em; color: #333333; font-weight: 400; border: none;">{{ $index + 1 }}</td>
                                    <td style="padding: 10px 15px; text-align: center; font-size: 1em; color: #333333; font-weight: 400; border: none;">{{ format_price($total->amount) }} </td>
                                    <td style="padding: 10px 15px; text-align: center; font-size: 1em; color: #333333; font-weight: 400; border: none;">{{ number_format($total->percentage, 2) }}%</td>
                                    <td style="padding: 10px 0px; text-align: center; font-size: 1em; color: #333333; font-weight: 400; border: none;">
                                        <a href="{{ route('totalrevenue.edit', $total->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-route="{{ route('totalrevenue.delete', $total->id) }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-lg">
                                <div class="modal-header bg-primary text-white">
                                    <h4 class="modal-title fw-bold" id="deleteModalLabel">{{ trans('core/base::layouts.confirm_deletion')}}</h4>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">{{ trans('core/base::layouts.are_you_sure_you_want_to_delete_this_item') }}?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ trans('core/base::layouts.close')}}</button>
                                    <form id="deleteForm" method="POST" action="" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">{{ trans('core/base::layouts.yes')}}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to handle modal -->
    <script>
        (function($) {
            $(document).ready(function() {
                $('.delete-btn').on('click', function() {
                    const route = $(this).data('route');
                    $('#deleteForm').attr('action', route);
                });
            });
        })(jQuery);
    </script>
@endsection

@push('style-lib')
    <!-- Bootstrap CSS -->
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
@endpush

@push('js')
    <!-- jQuery -->
    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    <!-- jQuery UI -->
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
    <!-- Bootstrap JS -->
@endpush
