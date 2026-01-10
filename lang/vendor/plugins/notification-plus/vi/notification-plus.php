<?php

return [
    'name' => 'Thông báo Plus',
    'description' => 'Gửi thông báo tới Telegram, Slack, WhatsApp, Vonage, Twilio.',
    'settings' => [
        'enable' => 'Cho phép?',
    ],
    'telegram' => [
        'settings' => [
            'title' => 'Cài đặt Telegram',
            'description' => 'Bot Telegram, gửi thông báo đến kênh Telegram. Bạn có thể tạo bot bằng cách theo liên kết :link.',
            'bot_token' => 'Bot Token',
            'chat_id' => 'Chat ID',
            'bot_token_instruction' => 'Trò chuyện với :link để nhận mã thông báo bot.',
            'chat_id_instruction' => 'Thêm bot vào nhóm của bạn để lấy ID trò chuyện. Sau đó, bạn có thể nhấp vào "Lấy ID trò chuyện" để chọn nhóm nào sẽ gửi thông báo. Nếu bạn không lấy được ID trò chuyện, hãy thử gửi tin nhắn đến nhóm của bạn.',
            'get_chat_ids' => 'Get Chat IDs',
            'cannot_get_chat_ids' => 'Không thể lấy được ID trò chuyện. Vui lòng thêm bot vào nhóm của bạn và gửi ít nhất một tin nhắn đến nhóm.',
        ],
    ],
    'slack' => [
        'settings' => [
            'title' => 'Cài đặt Slack',
            'description' => 'Bằng cách làm theo :link này, bạn có thể tạo ứng dụng mới trong Slack và thêm webhook vào kênh của mình.',
            'webhook_url' => 'Webhook URL',
            'webhook_url_instruction' => 'Tạo một ứng dụng mới trong Slack và thêm webhook vào kênh của bạn.',
        ],
    ],
    'whatsapp' => [
        'settings' => [
            'title' => 'WhatsApp cài đặt',
            'description' => 'Để gửi tin nhắn WhatsApp, bạn cần tạo tài khoản WhatsApp Business tại :link và nhận mã thông báo truy cập.',
            'access_token' => 'Mã thông báo truy cập',
            'phone_number_id' => 'ID số điện thoại',
            'to_phone_number' => 'Đến số điện thoại',
            'to_phone_number_instruction' => 'Số điện thoại của người nhận, bao gồm mã quốc gia và không có bất kỳ định dạng nào.',
        ],
    ],
    'vonage' => [
        'settings' => [
            'title' => 'Cài đặt Vonage',
            'description' => 'Để gửi tin nhắn SMS, bạn cần tạo tài khoản Vonage tại :link và lấy khóa API và bí mật.',
            'api_key' => 'API Key',
            'api_key_instruction' => 'Bạn có thể lấy khóa và bí mật API từ bảng điều khiển API của Vonage.',
            'api_secret' => 'API Secret',
            'from' => 'Từ',
            'from_instruction' => 'Tên hoặc số điện thoại để gửi tin nhắn.',
            'to' => 'Đến',
            'to_instruction' => 'Số mà tin nhắn sẽ được gửi đến. Các số được chỉ định theo định dạng E.164.',
        ],
    ],
    'twilio' => [
        'settings' => [
            'title' => 'Cài đặt Twilio',
            'description' => 'Để gửi tin nhắn SMS, bạn cần tạo tài khoản Twilio tại :link và lấy tên tài khoản và mã thông báo xác thực.',
            'account_sid' => 'SID tài khoản',
            'account_sid_instruction' => 'Bạn có thể lấy SID tài khoản và Mã thông báo xác thực từ Bảng điều khiển Twilio.',
            'auth_token' => 'Mã thông báo xác thực',
            'from' => 'Gửi từ',
            'to' => 'Gửi tới',
            'from_instruction' => 'Số điện thoại Twilio của bạn.',
            'to_instruction' => 'Số điện thoại mà tin nhắn sẽ được gửi tới.',
        ],
    ],
    'send_test_message' => [
        'button_text' => 'Gửi tin nhắn kiểm tra',
        'success_message' => 'Đã gửi tin nhắn thử nghiệm thành công!',
        'modal_title' => 'Gửi tin nhắn kiểm tra',
        'modal_message_label' => 'Tin nhắn',
        'modal_button_text' => 'Gửi',
    ],
];
