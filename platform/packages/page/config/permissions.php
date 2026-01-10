<?php

return [
    [
        'name' => 'Trang',
        'flag' => 'pages.index',
        'parent_flag' => 'core.cms',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'pages.create',
        'parent_flag' => 'pages.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'pages.edit',
        'parent_flag' => 'pages.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'pages.destroy',
        'parent_flag' => 'pages.index',
    ],
];
