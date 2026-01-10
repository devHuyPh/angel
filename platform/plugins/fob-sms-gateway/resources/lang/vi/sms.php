<?php
return [
    'name' => 'Cổng SMS',

    'settings' => [
        'title' => 'SMS',
        'description' => 'Cấu hình các cài đặt để gửi tin nhắn SMS.',
        'form' => [
            'default_sms_provider' => 'Nhà cung cấp SMS mặc định',
            'default_sms_provider_help' => 'Đây là nhà cung cấp SMS mặc định sẽ được sử dụng để gửi tin nhắn SMS.',
        ],
    ],

    'configure_button' => 'Cấu hình',
    'save_button' => 'Lưu',
    'activate_button' => 'Kích hoạt',
    'deactivate_button' => 'Hủy kích hoạt',
    'test_button' => 'Gửi SMS thử nghiệm',
    'test_modal' => [
        'title' => 'Gửi SMS thử nghiệm',
        'description' => 'Nhập chi tiết tin nhắn để gửi một tin nhắn SMS thử nghiệm.',
        'to' => 'Gửi đến',
        'to_placeholder' => 'Nhập số điện thoại để gửi tin nhắn SMS thử nghiệm.',
        'message' => 'Tin nhắn',
    ],
    'gateway_description' => 'Gửi tin nhắn SMS bằng :name.',
    'send_sms_failed' => 'Đã xảy ra lỗi khi gửi tin nhắn SMS. Hãy kiểm tra phản hồi trong phần Nhật ký SMS.',
    'sms_sent' => 'Tin nhắn SMS đã được gửi thành công.',

    'enums' => [
        'log_statuses' => [
            'pending' => 'Đang chờ',
            'success' => 'Thành công',
            'failed' => 'Thất bại',
        ],
    ],

    'logs' => [
        'title' => 'Nhật ký SMS',
        'detail_title' => 'Nhật ký SMS #:id',
        'id' => 'ID',
        'message_id' => 'ID Tin nhắn',
        'provider' => 'Nhà cung cấp',
        'from' => 'Từ',
        'to' => 'Đến',
        'message' => 'Tin nhắn',
        'status' => 'Trạng thái',
        'sent_at' => 'Gửi lúc',
        'response' => 'Phản hồi',
    ],
];