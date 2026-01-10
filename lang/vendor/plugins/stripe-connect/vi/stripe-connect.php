<?php

return [
    'stripe_account_id' => 'ID tài khoản Stripe',
    'go_to_dashboard' => 'Đi đến Bảng điều khiển Express',
    'connect' => [
        'label' => 'Kết nối với Stripe',
        'description' => 'Kết nối tài khoản Stripe của bạn để thu tiền.',
    ],
    'disconnect' => [
        'label' => 'Ngắt kết nối Stripe',
        'confirm' => 'Bạn có chắc chắn muốn ngắt kết nối tài khoản Stripe của mình không?',
    ],
    'notifications' => [
        'connected' => 'Tài khoản Stripe của bạn đã được kết nối.',
        'disconnected' => 'Tài khoản Stripe của bạn đã bị ngắt kết nối.',
        'now_active' => 'Your Stripe account is now active.',
    ],
    'withdrawal' => [
        'payout_info' => 'Khoản thanh toán của bạn sẽ tự động được chuyển vào tài khoản Stripe của bạn với ID: :stripe_account_id.',
    ],
];
