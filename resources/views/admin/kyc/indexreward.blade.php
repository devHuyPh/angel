@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid wrapper">
        <div class="card card-custom">
            <div class="card-header">
                <div class="container header-container">
                    <div class="header-content">
                        <h5>{{ trans('core/base::layouts.rewarded_users_list') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-custom">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th scope="col">{{ trans('core/base::layouts.key') }}</th>
                                <th scope="col">{{ trans('core/base::layouts.value') }}</th>
                                <th scope="col" class="text-center">{{ trans('core/base::layouts.Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{trans('core/base::layouts.'.$type) }}</td>
                                @if($type=="money")
                                <td class="value-text">{{format_price($value)}}</td>
                                @else
                                <td class="value-text">{{$value}}</td>
                                @endif
                                <td class="text-center">
                                    <a href="{{ route('update.reward') }}" class="btn btn-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
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
    <style>
        /* Wrapper */
        .wrapper {
            padding: 0;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f8f9fa; /* Màu nền giống hình */
        }

        /* Card */
        .card-custom {
            margin: 0;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); /* Bóng giống hình */
            border-radius: 10px;
            flex-grow: 1;
            background: #ffffff;
        }

        /* Header */
        .card-header {
            background: #ffffff;
            padding: 1rem;
            border-bottom: none; /* Không có viền dưới */
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-content h5 {
            color: #000000; /* Màu tiêu đề đen */
            font-size: 1.5em; /* Kích thước tiêu đề giống hình */
            font-weight: 500;
            margin: 0;
        }

        /* Card Body */
        .card-body {
            padding: 0;
            overflow: auto;
            background: #f8f9fa; /* Màu nền giống hình */
        }

        .table-responsive {
            height: 100%;
        }

        /* Table */
        .table-custom {
            margin: 0;
            border-collapse: collapse;
            background: #ffffff;
        }

        .table-custom th {
            background: #2c3e50; /* Màu đầu bảng giống hình */
            color: #ffffff;
            font-weight: 500;
            text-transform: uppercase;
            padding: 10px 15px; /* Padding giống hình */
            border-bottom: 1px solid #dee2e6;
            text-align: left;
            font-size: 0.9em; /* Kích thước chữ nhỏ hơn */
        }

        .table-custom th.text-center {
            text-align: center; /* Căn giữa cột Action */
        }

        .table-custom td {
            padding: 10px 15px; /* Padding giống hình */
            font-size: 1em;
            color: #333333; /* Màu chữ giống hình */
            font-weight: 400;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .value-text {
            color: #007bff; /* Màu giá trị giống hình */
            font-weight: 500;
        }

        .text-center {
            text-align: center;
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 1; /* Z-index giống hình */
        }

        /* Action Button */
        .btn-action {
            padding: 6px 12px; /* Kích thước nút giống hình */
            font-size: 0.9em;
            border-radius: 5px;
            background: #f39c12; /* Màu cam giống hình */
            color: #ffffff;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center; /* Căn giữa theo chiều ngang */
            width: 40px; /* Đặt chiều rộng cố định để nút có hình vuông */
            height: 30px; /* Đặt chiều cao cố định để nút có hình vuông */
        }

        .btn-action i {
            margin: 0; /* Xóa khoảng cách thừa */
        }

        .btn-action:hover {
            background: #e67e22; /* Màu khi hover giống hình */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .table-custom th,
            .table-custom td {
                font-size: 0.85rem;
                padding: 10px;
            }
        }
    </style>
@endpush

@push('js')
    <!-- Load Bootstrap JS -->
    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
@endpush