<?php

return [
    [
        'name' => 'Bản địa hóa',
        'flag' => 'plugins.translation',
        'parent_flag' => 'settings.index',
    ],
    [
        'name' => 'Người dân địa phương',
        'flag' => 'translations.locales',
        'parent_flag' => 'plugins.translation',
    ],
    [
        'name' => 'Bản dịch chủ đề',
        'flag' => 'translations.theme-translations',
        'parent_flag' => 'plugins.translation',
    ],
    [
        'name' => 'Các bản dịch khác',
        'flag' => 'translations.index',
        'parent_flag' => 'plugins.translation',
    ],
    [
        'name' => 'Xuất bản bản dịch chủ đề',
        'flag' => 'theme-translations.export',
        'parent_flag' => 'tools.data-synchronize',
    ],
    [
        'name' => 'Xuất bản các bản dịch khác',
        'flag' => 'other-translations.export',
        'parent_flag' => 'tools.data-synchronize',
    ],
    [
        'name' => 'Nhập bản bản dịch chủ đề',
        'flag' => 'theme-translations.import',
        'parent_flag' => 'tools.data-synchronize',
    ],
    [
        'name' => 'Nhập bản các bản dịch khác',
        'flag' => 'other-translations.import',
        'parent_flag' => 'tools.data-synchronize',
    ],
];
