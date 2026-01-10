<?php

return [
    [
        'name' => 'Phương tiện truyền thông',
        'flag' => 'media.index',
        'parent_flag' => 'core.cms',
    ],
    [
        'name' => 'Tệp tin',
        'flag' => 'files.index',
        'parent_flag' => 'media.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'files.create',
        'parent_flag' => 'files.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'files.edit',
        'parent_flag' => 'files.index',
    ],
    [
        'name' => 'Thùng rác',
        'flag' => 'files.trash',
        'parent_flag' => 'files.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'files.destroy',
        'parent_flag' => 'files.index',
    ],

    [
        'name' => 'Thư mục',
        'flag' => 'folders.index',
        'parent_flag' => 'media.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'folders.create',
        'parent_flag' => 'folders.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'folders.edit',
        'parent_flag' => 'folders.index',
    ],
    [
        'name' => 'Thùng rác',
        'flag' => 'folders.trash',
        'parent_flag' => 'folders.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'folders.destroy',
        'parent_flag' => 'folders.index',
    ],
];
