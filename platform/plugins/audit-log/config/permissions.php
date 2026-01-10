<?php

return [
    [
        'name' => 'Nhật ký hoạt động',
        'flag' => 'audit-log.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'audit-log.destroy',
        'parent_flag' => 'audit-log.index',
    ],
];
