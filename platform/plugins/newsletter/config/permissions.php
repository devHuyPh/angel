<?php

return [
    [
        'name' => 'Bản tin',
        'flag' => 'newsletter.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'newsletter.destroy',
        'parent_flag' => 'newsletter.index',
    ],
    [
        'name' => 'Bản tin',
        'flag' => 'newsletter.settings',
        'parent_flag' => 'settings.others',
    ],
];
