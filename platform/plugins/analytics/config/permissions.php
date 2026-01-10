<?php

return [
    [
        'name' => 'Phân tích',
        'flag' => 'analytics.general',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Trang hàng đầu',
        'flag' => 'analytics.page',
        'parent_flag' => 'analytics.general',
    ],
    [
        'name' => 'Trình duyệt hàng đầu',
        'flag' => 'analytics.browser',
        'parent_flag' => 'analytics.general',
    ],
    [
        'name' => 'Người giới thiệu hàng đầu',
        'flag' => 'analytics.referrer',
        'parent_flag' => 'analytics.general',
    ],
    [
        'name' => 'Phân tích',
        'flag' => 'analytics.settings',
        'parent_flag' => 'settings.others',
    ],
];
