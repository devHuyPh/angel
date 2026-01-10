<?php

return [
    [
        'name' => 'Thương mại',
        'flag' => 'marketplace.index',
    ],

    [
        'name' => 'Cửa hàng',
        'flag' => 'marketplace.store.index',
        'parent_flag' => 'marketplace.index',
    ],
    [
        'name' => 'Tạo mới',
        'flag' => 'marketplace.store.create',
        'parent_flag' => 'marketplace.store.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'marketplace.store.edit',
        'parent_flag' => 'marketplace.store.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'marketplace.store.destroy',
        'parent_flag' => 'marketplace.store.index',
    ],
    [
        'name' => 'Xem',
        'flag' => 'marketplace.store.view',
        'parent_flag' => 'marketplace.store.index',
    ],
    [
        'name' => 'Cập nhật số dư',
        'flag' => 'marketplace.store.revenue.create',
        'parent_flag' => 'marketplace.store.index',
    ],

    [
        'name' => 'Rút tiền',
        'flag' => 'marketplace.withdrawal.index',
        'parent_flag' => 'marketplace.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'marketplace.withdrawal.edit',
        'parent_flag' => 'marketplace.withdrawal.index',
    ],
    [
        'name' => 'Xóa',
        'flag' => 'marketplace.withdrawal.destroy',
        'parent_flag' => 'marketplace.withdrawal.index',
    ],
    [
        'name' => 'Xem hóa đơn',
        'flag' => 'marketplace.withdrawal.invoice',
        'parent_flag' => 'marketplace.withdrawal.index',
    ],

    [
        'name' => 'Nhà cung cấp',
        'flag' => 'marketplace.vendors.index',
        'parent_flag' => 'marketplace.index',
    ],
    [
        'name' => 'Nhà cung cấp chưa được xác minh',
        'flag' => 'marketplace.unverified-vendors.index',
        'parent_flag' => 'marketplace.index',
    ],
    [
        'name' => 'Chặn/Bỏ chặn',
        'flag' => 'marketplace.vendors.control',
        'parent_flag' => 'marketplace.vendors.index',
    ],
    [
        'name' => 'Chỉnh sửa',
        'flag' => 'marketplace.unverified-vendors.edit',
        'parent_flag' => 'marketplace.unverified-vendors.index',
    ],

    [
        'name' => 'Báo cáo',
        'flag' => 'marketplace.reports',
        'parent_flag' => 'marketplace.index',
    ],

    [
        'name' => 'Cài đặt',
        'flag' => 'marketplace.settings',
        'parent_flag' => 'ecommerce.settings',
    ],
];
