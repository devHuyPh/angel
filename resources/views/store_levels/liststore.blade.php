@extends(BaseHelper::getAdminMasterLayoutTemplate())
@push('style')
    <style>
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
            display: none;
        }

        .dropdown-submenu:hover .dropdown-menu {
            display: block;
        }

        .store-link:hover {
            text-decoration: underline;
            color: #007bff;
        }

        .filter-container {
            margin-bottom: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        .state-section {
            margin-bottom: 2rem;
        }

        .state-section table {
            background-color: #fff;
            border: 2px solid #dee2e6;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .state-section table th,
        .state-section table td {
            border: 1px solid #ced4da;
            padding: 0.5rem;
            vertical-align: middle;
        }

        .state-section table th {
            background-color: #e9ecef;
            font-weight: 600;
            text-align: center;
        }

        .state-section table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .store-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .store-list li {
            margin-bottom: 0.5rem;
        }

        .no-stores {
            padding: 0.5rem;
            color: #6c757d;
            font-style: italic;
            text-align: center;
        }

        @media (max-width: 768px) {

            .state-section table th,
            .state-section table td {
                font-size: 0.9rem;
            }

            .filter-container select {
                width: 100%;
            }

            .state-section table {
                font-size: 0.85rem;
            }

            .store-list li {
                font-size: 0.9rem;
            }
        }
    </style>
@endpush

@section('content')
    {{-- @dd($t) --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-store me-2"></i>{{ trans('core/base::layouts.list_title') }}
                        </h3>
                        <div class="d-flex gap-2">
                            {{-- <a href="{{ route('store-levels.assign.form') }}" class="btn btn-success btn-sm"
                                data-bs-toggle="tooltip" title="Gán cửa hàng vào cấp độ">
                                <i class="fas fa-link"></i> {{ trans('core/base::layouts.assign_to_store') }}
                            </a> --}}

                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                            </div>
                        @endif

                        <!-- Bộ lọc tỉnh -->
                        <div class="filter-container">
                            <label for="state-filter" class="form-label fw-bold">Lọc theo tỉnh:</label>
                            <select id="state-filter" class="form-select w-auto d-inline-block">
                                <option value="">Tất cả tỉnh</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="state-sections">
                            @forelse($states as $index => $state)
                                @php
                                    $stateStores = $levels->flatMap->stores->where('state', $state->id);
                                    // dd($stateStores);
                                @endphp
                                <div class="state-section" data-state="{{ $state->id }}">
                                    <h4 class="mb-3">{{ $state->name }}</h4>
                                    @if ($stateStores->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 5%">#</th>
                                                        <!-- <th style="width: 20%">{{ trans('core/base::layouts.state') }}</th> -->
                                                        @foreach ($levels as $level)
                                                            <th>{{ $level->name }} ({{ number_format($level->value) }}
                                                                VNĐ)</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <!-- <td>{{ $state->name }}</td> -->
                                                        @foreach ($levels as $level)
                                                            @php
                                                                $stores = $level->stores->where('state', $state->id);
                                                            @endphp
                                                            <td>
                                                                @if ($stores->isNotEmpty())
                                                                    <ul class="store-list">
                                                                        {{-- @dd($stores) --}}
                                                                        @foreach ($stores as $store)
                                                                            <li>
                                                                                {{ $store->name . ' ' }}
                                                                                <small>(
                                                                                    {{ $store->address }},
                                                                                    {{ $store->cities?->name ?? 'N/A' }},
                                                                                    {{ $store->states?->name ?? 'N/A' }}
                                                                                    )</small>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <span
                                                                        class="no-stores">{{ trans('core/base::layouts.no_stores') }}</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="no-stores">{{ trans('core/base::layouts.no_stores') }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center text-muted">
                                    {{ trans('core/base::layouts.empty') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function escapeHtml(text) {
            return text ?
                String(text)
                .replace(/&/g, "&")
                .replace(/</g, "<")
                .replace(/>/g, ">")
                .replace(/"/g, "\"")
                .replace(/'/g, "'") :
                '';
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            console.log('jQuery loaded:', typeof $);
            console.log('Bootstrap loaded:', typeof bootstrap);

            // Xử lý bộ lọc tỉnh
            $('#state-filter').on('change', function() {
                var selectedState = $(this).val();
                if (selectedState === '') {
                    $('.state-section').show();
                } else {
                    $('.state-section').hide();
                    $(`.state-section[data-state="${selectedState}"]`).show();
                    if ($(`.state-section[data-state="${selectedState}"]`).length === 0) {
                        $('#state-sections').html(
                            '<div class="text-center text-muted">Không có cửa hàng trong tỉnh này</div>'
                        );
                    }
                }
            });

            // Xử lý click vào store-link
            $('.store-link').on('click', function(e) {
                e.preventDefault();
                console.log('Clicked store-link with ID:', $(this).data('store-id'));
                var storeId = $(this).data('store-id');
                var dropdown = $('#dropdown-' + storeId);

                dropdown.dropdown('show');
                console.log('Dropdown shown:', dropdown.is(':visible'));

                $.ajax({
                    url: '{{ url('stores/higher-level') }}/' + storeId,
                    method: 'GET',
                    beforeSend: function() {
                        dropdown.empty();
                        dropdown.append(
                            `<li><a class="dropdown-item disabled" href="#"><span class="spinner-border spinner-border-sm me-2"></span>Đang tải...</a></li>`
                        );
                    },
                    success: function(data) {
                        console.log('API Response:', data);
                        dropdown.empty();
                        if (data.length > 0) {
                            data.forEach(function(level) {
                                var levelItem = `
                  <li class="dropdown-submenu">
                    <a class="dropdown-item dropdown-toggle" href="#">${escapeHtml(level.level_name)} (${level.level_value} VNĐ)</a>
                    <ul class="dropdown-menu">
                `;
                                level.stores.forEach(function(store) {
                                    levelItem +=
                                        `<li><a class="dropdown-item" href="#">${escapeHtml(store.name)} (${escapeHtml(store.state_name) || '---'})</a></li>`;
                                });
                                levelItem += `</ul></li>`;
                                console.log('Appending:', levelItem);
                                dropdown.append(levelItem);
                            });
                        } else {
                            dropdown.append(
                                `<li><a class="dropdown-item disabled" href="#">Không có cửa hàng cấp cao hơn</a></li>`
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.status, error);
                        console.log('Response Text:', xhr.responseText);
                        dropdown.empty();
                        dropdown.append(
                            `<li><a class="dropdown-item disabled" href="#">Lỗi khi tải dữ liệu: ${xhr.status}</a></li>`
                        );
                    }
                });
            });

            // Khởi tạo tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
