<?php

return [
    [
        'name' => 'Liên hệ',
        'flag' => 'contacts.index',
        'parent_flag' => 'core.cms',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'contacts.edit',
        'parent_flag' => 'contacts.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'contacts.destroy',
        'parent_flag' => 'contacts.index',
    ],
    [
        'name' => 'Trường tùy chỉnh',
        'flag' => 'contact.custom-fields',
        'parent_flag' => 'contacts.index',
    ],
    [
        'name' => 'Liên hệ',
        'flag' => 'contact.settings',
        'parent_flag' => 'settings.others',
    ],
];
