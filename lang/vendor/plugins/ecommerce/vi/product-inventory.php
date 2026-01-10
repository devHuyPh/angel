<?php

return [
    'name' => 'Hàng tồn kho sản phẩm',
    'storehouse_management' => 'Quản lý kho',
    'import' => [
        'name' => 'Cập nhật tồn kho sản phẩm',
        'description' => 'Cập nhật hàng loạt kho sản phẩm bằng cách tải lên tệp CSV/Excel.',
        'done_message' => 'Đã cập nhật thành công :count sản phẩm.',
        'rules' => [
            'id' => 'Trường ID là bắt buộc và phải tồn tại trong bảng sản phẩm.',
            'name' => 'Trường tên là bắt buộc và phải là một chuỗi.',
            'sku' => 'Trường SKU phải là một chuỗi.',
            'with_storehouse_management' => 'Trường quản lý kho phải là "Có" hoặc "Không".',
            'quantity' => 'Trường số lượng là bắt buộc khi quản lý kho là "Có".',
            'stock_status' => 'Trường trạng thái tồn kho là bắt buộc khi quản lý kho là "Không" và phải là một trong các giá trị sau: :statuses.',
        ],
    ],
    'export' => [
        'description' => 'Xuất kho sản phẩm sang tệp CSV/Excel.',
    ],
];