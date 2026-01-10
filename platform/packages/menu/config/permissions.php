<?php

return [
    [
        'name' => 'Menu',
        'flag' => 'menus.index',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'menus.create',
        'parent_flag' => 'menus.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'menus.edit',
        'parent_flag' => 'menus.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'menus.destroy',
        'parent_flag' => 'menus.index',
    ],
];
