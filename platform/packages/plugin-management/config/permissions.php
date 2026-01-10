<?php

return [
    [
        'name' => 'Plugins',
        'flag' => 'plugins.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Kích hoạt/Hủy kích hoạt',
        'flag' => 'plugins.edit',
        'parent_flag' => 'plugins.index',
    ],
    [
        'name' => 'Di chuyển',
        'flag' => 'plugins.remove',
        'parent_flag' => 'plugins.index',
    ],
    [
        'name' => 'Thêm mới Plugins',
        'flag' => 'plugins.marketplace',
        'parent_flag' => 'plugins.index',
    ],
];
