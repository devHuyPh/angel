<?php

return [
    'login' => [
        'username' => 'Email/Tên đăng nhập',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'title' => 'Đăng nhập người dùng',
        'remember' => 'Ghi nhớ tôi?',
        'login' => 'Đăng nhập',
        'placeholder' => [
            'username' => 'Nhập tên đăng nhập hoặc địa chỉ email của bạn',
            'email' => 'Nhập địa chỉ email của bạn',
            'password' => 'Nhập mật khẩu của bạn',
        ],
        'success' => 'Đăng nhập thành công!',
        'fail' => 'Sai tên đăng nhập hoặc mật khẩu.',
        'not_active' => 'Tài khoản của bạn chưa được kích hoạt!',
        'banned' => 'Tài khoản này đã bị cấm.',
        'logout_success' => 'Đăng xuất thành công!',
        'dont_have_account' => 'Bạn không có tài khoản trên hệ thống này, vui lòng liên hệ quản trị viên để biết thêm thông tin!',
    ],
    'forgot_password' => [
        'title' => 'Quên mật khẩu',
        'message' => '<p>Bạn đã quên mật khẩu?</p><p>Vui lòng nhập tài khoản email của bạn. Hệ thống sẽ gửi một email kèm liên kết kích hoạt để đặt lại mật khẩu.</p>',
        'submit' => 'Gửi',
    ],
    'reset' => [
        'new_password' => 'Mật khẩu mới',
        'password_confirmation' => 'Xác nhận mật khẩu mới',
        'email' => 'Email',
        'title' => 'Đặt lại mật khẩu của bạn',
        'update' => 'Cập nhật',
        'wrong_token' => 'Liên kết này không hợp lệ hoặc đã hết hạn. Vui lòng thử lại với biểu mẫu đặt lại.',
        'user_not_found' => 'Tên đăng nhập này không tồn tại.',
        'success' => 'Đặt lại mật khẩu thành công!',
        'fail' => 'Mã thông báo không hợp lệ, liên kết đặt lại mật khẩu đã hết hạn!',
        'reset' => [
            'title' => 'Email đặt lại mật khẩu',
        ],
        'send' => [
            'success' => 'Một email đã được gửi đến tài khoản email của bạn. Vui lòng kiểm tra và hoàn thành hành động này.',
            'fail' => 'Không thể gửi email vào lúc này. Vui lòng thử lại sau.',
        ],
        'new-password' => 'Mật khẩu mới',
        'placeholder' => [
            'new_password' => 'Nhập mật khẩu mới của bạn',
            'new_password_confirmation' => 'Xác nhận mật khẩu mới của bạn',
        ],
    ],
    'email' => [
        'reminder' => [
            'title' => 'Email đặt lại mật khẩu',
        ],
    ],
    'password_confirmation' => 'Xác nhận mật khẩu',
    'failed' => 'Thất bại',
    'throttle' => 'Giới hạn',
    'not_member' => 'Chưa là thành viên?',
    'register_now' => 'Đăng ký ngay',
    'lost_your_password' => 'Mất mật khẩu của bạn?',
    'login_title' => 'Quản trị',
    'login_via_social' => 'Đăng nhập bằng mạng xã hội',
    'back_to_login' => 'Quay lại trang đăng nhập',
    'sign_in_below' => 'Đăng nhập bên dưới',
    'languages' => 'Ngôn ngữ',
    'reset_password' => 'Đặt lại mật khẩu',
    'settings' => [
        'email' => [
            'title' => 'ACL',
            'description' => 'Cấu hình email ACL',
            'templates' => [
                'password_reminder' => [
                    'title' => 'Đặt lại mật khẩu',
                    'description' => 'Gửi email đến người dùng khi yêu cầu đặt lại mật khẩu',
                    'subject' => 'Đặt lại mật khẩu',
                    'reset_link' => 'Liên kết đặt lại mật khẩu',
                ],
            ],
        ],
    ],
];