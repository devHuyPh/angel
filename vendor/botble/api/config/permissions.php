<?php

return [
    [
        'name' => 'API',
        'flag' => 'api.settings',
        'parent_flag' => 'settings.index',
    ],
    [
        'name' => 'Sanctum Token',
        'flag' => 'api.sanctum-token.index',
        'parent_flag' => 'api.settings',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'api.sanctum-token.create',
        'parent_flag' => 'api.sanctum-token.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'api.sanctum-token.destroy',
        'parent_flag' => 'api.sanctum-token.index',
    ],
];
