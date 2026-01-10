<?php

return [
    [
        'name' => 'Thanh toán',
        'flag' => 'payment.index',
    ],
    [
        'name' => 'Cài đặt',
        'flag' => 'payments.settings',
        'parent_flag' => 'payment.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'payment.destroy',
        'parent_flag' => 'payment.index',
    ],
    [
        'name' => 'Nhật ký thanh toán',
        'flag' => 'payments.logs',
        'parent_flag' => 'payment.index',
    ],
    [
        'name' => 'Xem',
        'flag' => 'payments.logs.show',
        'parent_flag' => 'payments.logs',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'payments.logs.destroy',
        'parent_flag' => 'payments.logs',
    ],
];
