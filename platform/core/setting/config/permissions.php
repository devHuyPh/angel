<?php

return [
    [
        'name' => 'Cài đặt',
        'flag' => 'settings.index',
    ],
    [
        'name' => 'Chung',
        'flag' => 'settings.common',
        'parent_flag' => 'settings.index',
    ],
    [
        'name' => 'Tổng quan',
        'flag' => 'settings.options',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Email',
        'flag' => 'settings.email',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Phương tiện truyền thông',
        'flag' => 'settings.media',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Giao diện của quản trị viên',
        'flag' => 'settings.admin-appearance',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Bộ nhớ đệm',
        'flag' => 'settings.cache',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Bảng dữ liệu',
        'flag' => 'settings.datatables',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Quy tắc Email',
        'flag' => 'settings.email.rules',
        'parent_flag' => 'settings.common',
    ],
    [
        'name' => 'Khác',
        'flag' => 'settings.others',
        'parent_flag' => 'settings.index',
    ],
];
