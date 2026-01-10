<?php

return [
    [
        'name' => 'Lời chứng thực',
        'flag' => 'testimonial.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'testimonial.create',
        'parent_flag' => 'testimonial.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'testimonial.edit',
        'parent_flag' => 'testimonial.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'testimonial.destroy',
        'parent_flag' => 'testimonial.index',
    ],
];
