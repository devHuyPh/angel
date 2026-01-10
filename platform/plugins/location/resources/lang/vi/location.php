<?php

return [
    'name' => 'Vị trí',
    'all_states' => 'Tất cả tiểu bang',
    'abbreviation' => 'Viết tắt',
    'abbreviation_placeholder' => 'Ví dụ: CA',
    'enums' => [
        'import_type' => [
            'country' => 'Quốc gia',
            'state' => 'Tiểu bang',
            'city' => 'Thành phố',
        ],
    ],
    'export' => [
        'total' => 'Tổng số vị trí',
        'total_countries' => 'Tổng số quốc gia',
        'total_states' => 'Tổng số tiểu bang',
        'total_cities' => 'Tổng số thành phố',
        'description' => 'Xuất dữ liệu vị trí của bạn như quốc gia, tiểu bang và thành phố.',
    ],
    'import' => [
        'description' => 'Nhập dữ liệu vị trí dễ dàng từ dữ liệu có sẵn hoặc bằng cách tải lên tệp CSV/Excel.',
        'rules' => [
            'name' => 'Tên của vị trí là bắt buộc và không được vượt quá 120 ký tự.',
            'slug' => 'Slug của vị trí, nếu được cung cấp, không được vượt quá 120 ký tự.',
            'import_type' => 'Loại nhập là bắt buộc và phải là một trong các giá trị được xác định trước.',
            'order' => 'Thứ tự của vị trí, nếu được cung cấp, phải là số nguyên dương từ 0 đến 127.',
            'abbreviation' => 'Viết tắt của vị trí, nếu được cung cấp, không được vượt quá 10 ký tự.',
            'status' => 'Trạng thái của vị trí là bắt buộc và phải là một trong các giá trị được xác định trước.',
            'country' => 'Trường quốc gia là bắt buộc nếu loại nhập là tiểu bang hoặc thành phố.',
            'state' => 'Trường tiểu bang là bắt buộc nếu loại nhập là thành phố.',
            'nationality' => 'Quốc tịch của vị trí, nếu được cung cấp, không được vượt quá 120 ký tự.',
        ],
    ],
];