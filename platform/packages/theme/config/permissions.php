<?php

return [
    [
        'name' => 'Bề ngoài',
        'flag' => 'core.appearance',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Chủ đề',
        'flag' => 'theme.index',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Kích hoạt',
        'flag' => 'theme.activate',
        'parent_flag' => 'theme.index',
    ],
    [
        'name' => 'Di chuyển',
        'flag' => 'theme.remove',
        'parent_flag' => 'theme.index',
    ],
    [
        'name' => 'Lựa chọn chủ đề',
        'flag' => 'theme.options',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Chỉnh sửa CSS',
        'flag' => 'theme.custom-css',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Chỉnh sửa JS',
        'flag' => 'theme.custom-js',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Chỉnh sửa HTML',
        'flag' => 'theme.custom-html',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Trình biên tập Robots.txt',
        'flag' => 'theme.robots-txt',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Theo dõi trang web',
        'flag' => 'settings.website-tracking',
        'parent_flag' => 'settings.common',
    ],
];
