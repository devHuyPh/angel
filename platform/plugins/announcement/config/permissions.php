<?php

return [
    [
        'name' => 'Thông báo',
        'flag' => 'announcements.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'announcements.create',
        'parent_flag' => 'announcements.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'announcements.edit',
        'parent_flag' => 'announcements.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'announcements.destroy',
        'parent_flag' => 'announcements.index',
    ],
    [
        'name' => 'Thông báo',
        'flag' => 'announcements.settings',
        'parent_flag' => 'settings.others',
    ],
];
