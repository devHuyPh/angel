@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid wrapper">
        <div class="card card-custom">
            <div class="card-header">
                <div class="container header-container">
                    <div class="header-content">
                        <h2>{{ trans('core/base::layouts.rewarded_users_list') }}</h2>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <form id="rewardForm" action="{{ route('store.reward') }}" method="POST">
                        @csrf
                        <table class="table table-hover table-striped align-middle table-custom">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th scope="col">{{ trans('core/base::layouts.key') }}</th>
                                    <th scope="col">{{ trans('core/base::layouts.value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fixed-cell">
                                        @php
                                            $datas = ['money', 'discount'];
                                        @endphp
                                        <select class="form-select key-select" name="keys" onchange="updateInputType(this)">
                                            @foreach($datas as $data)
                                                <option value="{{ $data }}" {{ $type == $data ? 'selected' : '' }}>
                                                    {{ trans("core/base::layouts.$data") }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="fixed-cell">
                                        <div class="value-container">
                                            <input type="{{ $type == 'money' ? 'text' : 'number' }}" 
                                                   class="form-control value-input mb-3" 
                                                   name="values" 
                                                   placeholder="Enter value"
                                                   value="{{ $value }}"
                                                   min="{{ $type == 'discount' ? 0 : '' }}"
                                                   max="{{ $type == 'discount' ? 100 : '' }}"
                                                   step="{{ $type == 'discount' ? 0.1 : '' }}">
                                            <!-- Nút Back và Save được đặt ở đây -->
                                            <div class="action-buttons text-end">
                                                <a href="{{ route('kyc.reward') }}" class="btn btn-secondary btn-sm btn-custom me-2">
                                                    <i class="fas fa-arrow-left"></i> {{ trans('core/base::layouts.back') }}
                                                </a>
                                                <button type="submit" class="btn btn-success btn-sm btn-custom" id="saveButton">
                                                    <i class="fas fa-save"></i> {{ trans('core/base::layouts.save') }}
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
    <style>
        .fixed-cell {
            min-width: 200px; /* Giữ chiều rộng cố định cho ô */
            width: 200px;
            vertical-align: top; /* Căn trên cùng cho nội dung trong ô */
        }

        .value-container {
            display: flex;
            flex-direction: column;
            gap: 10px; /* Khoảng cách giữa input và nút */
        }

        .value-input {
            width: 100%; /* Đảm bảo input chiếm toàn bộ ô */
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end; /* Căn phải các nút */
            gap: 10px; /* Khoảng cách giữa các nút */
        }

        .btn-custom {
            font-size: 0.9rem;
            padding: 0.5rem 1rem; /* Điều chỉnh kích thước nút */
        }
    </style>
@endpush

@push('js')
<script>
    function updateInputType(selectElement) {
        let row = selectElement.closest('tr');
        let input = row.querySelector('.value-input');

        if (selectElement.value === 'money') {
            input.type = 'text';
            input.removeAttribute('min');
            input.removeAttribute('max');
            input.removeAttribute('step');
        } else if (selectElement.value === 'discount') {
            input.type = 'number';
            input.min = 0;
            input.max = 100;
            input.step = 0.1;
        }
    }
</script>
@endpush