<?php

return [
    [
        'name' => 'Sliders đơn giản',
        'flag' => 'simple-slider.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'simple-slider.create',
        'parent_flag' => 'simple-slider.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'simple-slider.edit',
        'parent_flag' => 'simple-slider.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'simple-slider.destroy',
        'parent_flag' => 'simple-slider.index',
    ],

    [
        'name' => 'Mục Slider',
        'flag' => 'simple-slider-item.index',
        'parent_flag' => 'simple-slider.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'simple-slider-item.create',
        'parent_flag' => 'simple-slider-item.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'simple-slider-item.edit',
        'parent_flag' => 'simple-slider-item.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'simple-slider-item.destroy',
        'parent_flag' => 'simple-slider-item.index',
    ],
    [
        'name' => 'Cài đặt Slider đơn giản',
        'flag' => 'simple-slider.settings',
        'parent_flag' => 'simple-slider-item.index',
    ],
];
