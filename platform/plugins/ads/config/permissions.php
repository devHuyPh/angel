<?php

return [
    [
        'name' => 'Quảng cáo',
        'flag' => 'ads.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'ads.create',
        'parent_flag' => 'ads.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'ads.edit',
        'parent_flag' => 'ads.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'ads.destroy',
        'parent_flag' => 'ads.index',
    ],
    [
        'name' => 'Quảng cáo',
        'flag' => 'ads.settings',
        'parent_flag' => 'settings.others',
    ],
];
