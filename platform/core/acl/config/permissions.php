<?php

return [
    [
        'name' => 'Người dùng',
        'flag' => 'users.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'users.create',
        'parent_flag' => 'users.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'users.edit',
        'parent_flag' => 'users.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'users.destroy',
        'parent_flag' => 'users.index',
    ],

    [
        'name' => 'Quyền',
        'flag' => 'roles.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'roles.create',
        'parent_flag' => 'roles.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'roles.edit',
        'parent_flag' => 'roles.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'roles.destroy',
        'parent_flag' => 'roles.index',
    ],
];
