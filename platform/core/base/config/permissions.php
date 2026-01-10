<?php

return [
    [
        'name' => 'Hệ thống',
        'flag' => 'core.system',
    ],
    [
        'name' => 'CMS',
        'flag' => 'core.cms',
    ],
    [
        'name' => 'Quản lí giấy phép',
        'flag' => 'core.manage.license',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Cronjob',
        'flag' => 'systems.cronjob',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Công cụ',
        'flag' => 'core.tools',
    ],
    [
        'name' => 'Nhập/Xuất dữ liệu',
        'flag' => 'tools.data-synchronize',
        'parent_flag' => 'core.tools',
    ],
];
