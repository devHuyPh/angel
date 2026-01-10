<?php

return [
    'tools' => [
        'export_import_data' => 'Xuất/Nhập Dữ liệu',
    ],

    'import' => [
        'name' => 'Nhập',
        'heading' => 'Nhập :label',
        'failed_to_read_file' => 'Tệp không hợp lệ, bị hỏng hoặc quá lớn để đọc.',

        'form' => [
            'quick_export_message' => 'Nếu bạn muốn xuất dữ liệu :label, bạn có thể thực hiện nhanh chóng bằng cách nhấp vào :export_csv_link hoặc :export_excel_link.',
            'quick_export_button' => 'Xuất sang :format',
            'dropzone_message' => 'Kéo và thả tệp vào đây hoặc nhấp để tải lên',
            'allowed_extensions' => 'Chọn một tệp có các phần mở rộng sau: :extensions.',
            'import_button' => 'Nhập',
            'chunk_size' => 'Kích thước phân đoạn',
            'chunk_size_helper' => 'Số lượng hàng được nhập cùng một lúc được xác định bởi kích thước phân đoạn. Tăng giá trị này nếu bạn có tệp lớn và dữ liệu được nhập rất nhanh. Giảm giá trị này nếu bạn gặp giới hạn bộ nhớ hoặc vấn đề hết thời gian cổng khi nhập dữ liệu.',
        ],

        'failures' => [
            'title' => 'Lỗi',
            'attribute' => 'Thuộc tính',
            'errors' => 'Lỗi',
        ],

        'example' => [
            'title' => 'Ví dụ',
            'download' => 'Tải xuống tệp ví dụ :type',
        ],

        'rules' => [
            'title' => 'Quy tắc',
            'column' => 'Cột',
        ],

        'uploading_message' => 'Bắt đầu tải tệp lên...',
        'uploaded_message' => 'Tệp :file đã được tải lên thành công. Bắt đầu xác thực dữ liệu...',
        'validating_message' => 'Đang xác thực từ :from đến :to...',
        'importing_message' => 'Đang nhập từ :from đến :to...',
        'done_message' => 'Đã nhập :count :label thành công.',
        'validating_failed_message' => 'Xác thực thất bại. Vui lòng kiểm tra các lỗi bên dưới.',
        'no_data_message' => 'Dữ liệu của bạn đã được cập nhật hoặc không có dữ liệu để nhập.',
    ],

    'export' => [
        'name' => 'Xuất',
        'heading' => 'Xuất :label',

        'form' => [
            'all_columns_disabled' => 'Các cột sau sẽ được xuất: :columns.',
            'columns' => 'Cột',
            'format' => 'Định dạng',
            'export_button' => 'Xuất',
        ],

        'success_message' => 'Xuất thành công.',
        'error_message' => 'Xuất thất bại.',

        'empty_state' => [
            'title' => 'Không có dữ liệu để xuất',
            'description' => 'Có vẻ như không có dữ liệu để xuất.',
            'back' => 'Quay lại :page',
        ],
    ],
    'check_all' => 'Chọn tất cả',
];