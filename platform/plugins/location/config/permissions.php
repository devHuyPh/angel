<?php

return [
    [
        'name' => 'Vị trí',
        'flag' => 'plugin.location',
    ],
    [
        'name' => 'Quốc gia',
        'flag' => 'country.index',
        'parent_flag' => 'plugin.location',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'country.create',
        'parent_flag' => 'country.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'country.edit',
        'parent_flag' => 'country.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'country.destroy',
        'parent_flag' => 'country.index',
    ],

    [
        'name' => 'Tiểu bang',
        'flag' => 'state.index',
        'parent_flag' => 'plugin.location',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'state.create',
        'parent_flag' => 'state.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'state.edit',
        'parent_flag' => 'state.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'state.destroy',
        'parent_flag' => 'state.index',
    ],

    [
        'name' => 'Thành phố',
        'flag' => 'city.index',
        'parent_flag' => 'plugin.location',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'city.create',
        'parent_flag' => 'city.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'city.edit',
        'parent_flag' => 'city.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'city.destroy',
        'parent_flag' => 'city.index',
    ],
];
