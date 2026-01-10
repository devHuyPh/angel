<?php

return [
    [
        'name' => 'Nhật ký yêu cầu',
        'flag' => 'request-log.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'request-log.destroy',
        'parent_flag' => 'request-log.index',
    ],
];
