<?php

return [
    [
        'name' => 'Backup',
        'flag' => 'backups.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'backups.create',
        'parent_flag' => 'backups.index',
    ],
    [
        'name' => 'Khôi phục',
        'flag' => 'backups.restore',
        'parent_flag' => 'backups.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'backups.destroy',
        'parent_flag' => 'backups.index',
    ],
];
