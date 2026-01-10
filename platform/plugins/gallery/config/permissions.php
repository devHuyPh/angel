<?php

return [
    [
        'name' => 'Thư viện ảnh',
        'flag' => 'galleries.index',
        'parent_flag' => 'core.cms',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'galleries.create',
        'parent_flag' => 'galleries.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'galleries.edit',
        'parent_flag' => 'galleries.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'galleries.destroy',
        'parent_flag' => 'galleries.index',
    ],
];
