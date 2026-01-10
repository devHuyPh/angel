<?php
return [
    'yes' => 'Có', // Yes
    'no' => 'Không', // No
    'is_default' => 'Mặc định?', // Is it default?
    'proc_close_disabled_error' => 'Hàm proc_close() đã bị tắt. Vui lòng liên hệ nhà cung cấp hosting để mở hàm này. Hoặc có thể thêm vào .env: CAN_EXECUTE_COMMAND=false để tắt tính năng này.', // Error message explaining that proc_close() is disabled and how to resolve it
    'email_template' => [
        'date_time' => 'Ngày giờ hiện tại', // Current date and time
        'date_year' => 'Năm hiện tại', // Current year
        'footer' => 'Mẫu chân trang email', // Email footer template
        'header' => 'Mẫu đầu trang email', // Email header template
        'site_admin_email' => 'Email quản trị viên', // Admin email
        'site_logo' => 'Logo của trang', // Site logo
        'site_title' => 'Tiêu đề trang', // Site title
        'site_url' => 'URL trang', // Site URL
        'site_email' => 'Email quản trị trang web', // Site admin email
        'site_copyright' => 'Bản quyền trang web', // Site copyright
        'site_social_links' => 'Liên kết xã hội của trang web (kiểu dữ liệu: mảng)', // Site social links (data type: array)
        'settings' => 'Đặt giá trị', // Set values/settings
        'email_css' => 'CSS gửi email', // CSS for email
        'variable' => 'Biến đổi', // Variable
        'preview' => 'Xem trước', // Preview
        'icon_variables' => 'Biến biểu tượng', // Icon variables
        'usage' => 'Cách sử dụng:', // Usage:
        'icon_variable_usage_description' => 'Bạn có thể sao chép biến :variable và dán:', // Instruction to copy and paste variable :variable
        'add_new_icons' => 'Thêm biểu tượng mới:', // Add new icons:
        'add_more_icon_description' => 'Bạn có thể thêm biểu tượng bằng cách tải chúng lên (hỗ trợ định dạng PNG, JPEG, JPG và GIF) vào đường dẫn sau: :path', // Instruction to add icons by uploading to a specific path
        'missing_icons' => 'Thiếu các biểu tượng', // Missing icons
        'missing_icons_description' => 'Các biểu tượng sau thiếu trong đường dẫn: :to, vui lòng sao chép tất cả tệp biểu tượng từ :from đến :to.', // Explanation of missing icons and how to copy them
        'twig' => [
            'tag' => [
                'apply' => 'Thẻ áp dụng cho phép bạn áp dụng các bộ lọc Twig', // Apply tag allows applying Twig filters
                'for' => 'Lặp lại từng mục theo trình tự', // Iterate over each item in sequence
                'if' => 'Câu lệnh if trong Twig có thể so sánh với câu lệnh if của PHP', // Twig if statement comparable to PHP if
            ],
        ],
    ],
    'change_image' => 'Đổi ảnh', // Change image
    'delete_image' => 'Xóa ảnh', // Delete image
    'preview_image' => 'Ảnh xem trước', // Preview image
    'image' => 'Hình ảnh', // Image
    'using_button' => 'Sử dụng nút', // Using button
    'select_image' => 'Chọn ảnh', // Select image
    'click_here' => 'Bấm vào đây', // Click here
    'to_add_more_image' => 'để thêm hình ảnh', // To add images
    'add_image' => 'Thêm ảnh', // Add image
    'tools' => 'Công cụ', // Tools
    'close' => 'Đóng', // Close
    'panel' => [
        'others' => 'Người khác', // Others
        'system' => 'Hệ thống', // System
        'manage_description' => 'Quản lý :name', // Manage :name
    ],
    'global_search' => [
        'title' => 'Tìm kiếm', // Search
        'search' => 'Tìm kiếm', // Search
        'clear' => 'Xóa', // Clear
        'no_result' => 'Không có kết quả nào', // No results
        'to_select' => 'chọn', // Select
        'to_navigate' => 'để điều hướng', // To navigate
        'to_close' => 'đóng', // Close
    ],
    'validation' => [
        'email_in_blacklist' => ':attribute nằm trong danh sách đen. Vui lòng sử dụng địa chỉ email khác.', // :attribute is blacklisted, use another email
        'domain' => ':attribute phải là một tên miền hợp lệ.', // :attribute must be a valid domain
    ],
    'showing_records' => 'Hiển thị :from đến :to trong số :total bản ghi', // Showing :from to :to of :total records
    'copy' => 'Sao chép', // Copy
    'copied' => 'Đã sao chép', // Copied
];