<?php

return [
    [
        'name' => 'Blog',
        'flag' => 'plugins.blog',
        'parent_flag' => 'core.cms',
    ],
    [
        'name' => 'Bài viết',
        'flag' => 'posts.index',
        'parent_flag' => 'plugins.blog',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'posts.create',
        'parent_flag' => 'posts.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'posts.edit',
        'parent_flag' => 'posts.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'posts.destroy',
        'parent_flag' => 'posts.index',
    ],

    [
        'name' => 'Danh mục',
        'flag' => 'categories.index',
        'parent_flag' => 'plugins.blog',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'categories.create',
        'parent_flag' => 'categories.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'categories.edit',
        'parent_flag' => 'categories.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'categories.destroy',
        'parent_flag' => 'categories.index',
    ],

    [
        'name' => 'Thẻ',
        'flag' => 'tags.index',
        'parent_flag' => 'plugins.blog',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'tags.create',
        'parent_flag' => 'tags.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'tags.edit',
        'parent_flag' => 'tags.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'tags.destroy',
        'parent_flag' => 'tags.index',
    ],
    [
        'name' => 'Blog',
        'flag' => 'blog.settings',
        'parent_flag' => 'settings.others',
    ],
    [
        'name' => 'Xuất bài viết',
        'flag' => 'posts.export',
        'parent_flag' => 'tools.data-synchronize',
    ],
    [
        'name' => 'Nhập bài viết',
        'flag' => 'posts.import',
        'parent_flag' => 'tools.data-synchronize',
    ],
];
