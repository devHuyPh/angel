<?php

return [
    [
        'name' => 'Câu hỏi thường gặp',
        'flag' => 'plugin.faq',
    ],
    [
        'name' => 'Câu hỏi thường gặp',
        'flag' => 'faq.index',
        'parent_flag' => 'plugin.faq',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'faq.create',
        'parent_flag' => 'faq.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'faq.edit',
        'parent_flag' => 'faq.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'faq.destroy',
        'parent_flag' => 'faq.index',
    ],
    [
        'name' => 'Danh mục câu hỏi thường gặp',
        'flag' => 'faq_category.index',
        'parent_flag' => 'plugin.faq',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'faq_category.create',
        'parent_flag' => 'faq_category.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'faq_category.edit',
        'parent_flag' => 'faq_category.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'faq_category.destroy',
        'parent_flag' => 'faq_category.index',
    ],
    [
        'name' => 'Câu hỏi thường gặp',
        'flag' => 'faqs.settings',
        'parent_flag' => 'settings.others',
    ],
];
