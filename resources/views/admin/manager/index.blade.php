@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ trans('core/base::layouts.manager_list') }}</h5>
        <a href="{{ route('admin.manager.create') }}" class="btn btn-primary">
            {{ trans('core/base::layouts.manager_add') }}
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center">{{ trans('core/base::layouts.id') }}</th>
                    <th>{{ trans('core/base::layouts.manager_name') }}</th>
                    <th>{{ trans('core/base::layouts.customer_name') }}</th>
                    <th>{{ trans('core/base::layouts.state_names') }}</th>
                    <th class="text-center">{{ trans('core/base::layouts.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($managers as $manager)
                    <tr>
                        
                        <td class="text-center">{{ $manager->manager_id }}</td>
                        <td>{{ $manager->manager_name }}</td>
                        <td>{{ $manager->customer_name }}</td>
                        <td>{{ $manager->state_names }}</td>
                        <td class="text-center">
                            <div class="table-actions">
                                <!-- Nút Sửa -->
                                <a href="{{ route('admin.manager.edit', ['customer_id' => $manager->customer_id, 'manager_name' => $manager->manager_name]) }}"
                                   class="btn btn-sm btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-title="{{ trans('core/base::layouts.edit') }}"
                                   data-bs-placement="top">
                                    <svg class="icon svg-icon-ti-ti-edit" xmlns="http://www.w3.org/2000/svg"
                                         width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                        <path d="M16 5l3 3"></path>
                                    </svg>
                                </a>

                                <!-- Nút Xóa -->
                                <button type="button" class="btn btn-danger btn-sm btn-icon btn-delete"
    data-bs-toggle="modal" data-bs-target="#deleteModal"
    data-hash="{{ $manager->hash }}"
    data-manager-name="{{ $manager->manager_name }}"
    data-bs-title="{{ trans('core/base::layouts.delete') }}"
    data-bs-placement="top">
                                    <svg class="icon svg-icon-ti-ti-trash" xmlns="http://www.w3.org/2000/svg"
                                         width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M4 7l16 0"></path>
                                        <path d="M10 11l0 6"></path>
                                        <path d="M14 11l0 6"></path>
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ trans('core/base::layouts.no_managers_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $managers->links() }}
        </div>
    </div>
</div>

<!-- Modal Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <!-- Hidden input để chứa manager_name -->
    <input type="hidden" name="hash" id="hashInput">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ trans('core/base::layouts.delete_confirm') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/base::layouts.close') }}"></button>
                </div>
                <div class="modal-body">
                    {!! trans('core/base::layouts.delete_message', ['name' => '<strong id="managerNameDisplay"></strong>']) !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('core/base::layouts.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ trans('core/base::layouts.delete') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForm = document.getElementById('deleteForm');
    const managerNameDisplay = document.getElementById('managerNameDisplay');
    const hashInput = document.getElementById('hashInput');

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const hash = this.dataset.hash;
            const managerName = this.dataset.managerName;

            if (!hash || !managerName) {
                alert('Thiếu thông tin, không thể xóa.');
                return;
            }

            deleteForm.action = `/admin/manager/${hash}`;
            hashInput.value = hash; // nếu bạn cần gửi hash ẩn, nhưng thực ra không cần nữa
            managerNameDisplay.textContent = managerName;
        });
    });
});
</script>

