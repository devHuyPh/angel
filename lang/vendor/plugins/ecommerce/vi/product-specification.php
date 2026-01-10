<?php

return [
    'product_specification' => 'Thông số kỹ thuật sản phẩm',
    'specification_groups' => [
        'title' => 'Nhóm đặc điểm kỹ thuật',
        'create' => [
            'title' => 'Tạo nhóm đặc tả',
        ],
        'edit' => [
            'title' => 'Chỉnh sửa nhóm thông số kỹ thuật ":name"',
        ],
    ],
    'specification_attributes' => [
        'title' => 'Thuộc tính đặc điểm kỹ thuật',
        'group' => 'Nhóm liên kết',
        'group_placeholder' => 'Chọn bất kỳ nhóm nào',
        'type' => 'Loại trường',
        'default_value' => 'Giá trị mặc định',
        'options' => [
            'heading' => 'Tùy chọn',
            'add' => [
                'label' => 'Thêm tùy chọn mới',
            ],
        ],
        'create' => [
            'title' => 'Tạo Thuộc tính Đặc tả',
        ],
        'edit' => [
            'title' => 'Chỉnh sửa Thuộc tính Đặc tả ":name"',
        ],
    ],
    'specification_tables' => [
        'title' => 'Bảng thông số kỹ thuật',
        'create' => [
            'title' => 'Tạo bảng thông số kỹ thuật',
        ],
        'edit' => [
            'title' => 'Chỉnh sửa Bảng thông số kỹ thuật ":name"',
        ],
        'fields' => [
            'groups' => 'Chọn các nhóm để hiển thị trong bảng này',
            'name' => 'Tên nhóm',
            'assigned_groups' => 'Nhóm được chỉ định',
            'sorting' => 'Phân loại',
        ],
    ],
    'product' => [
        'specification_table' => [
            'options' => 'Tùy chọn',
            'title' => 'Bảng thông số kỹ thuật',
            'select_none' => 'Không có',
            'description' => 'Chọn bảng thông số kỹ thuật để hiển thị trong sản phẩm này',
            'group' => 'Nhóm',
            'attribute' => 'Thuộc tính',
            'value' => 'Giá trị thuộc tính',
            'hide' => 'Hide',
            'sorting' => 'Sorting',
        ],
    ],
    'enums' => [
        'field_types' => [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'select' => 'Select',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio',
        ],
    ],
];
