<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">

    @if ($favicon = theme_option('favicon'))
        {{ Html::favicon(RvMedia::getImageUrl($favicon)) }}
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ page_title()->getTitle(false) }}</title>

    <style>
        :root {
            --primary-font: '{{ theme_option('primary_font', 'Muli') }}', sans-serif;
            --primary-color: {{ theme_option('primary_color', '#fab528') }};
        }

        .level-col {
            background: linear-gradient(to bottom, #f5c518, #d4a017);
            color: black;
            font-weight: bold;
            writing-mode: vertical-lr;
            transform: rotate(180deg);
            width: 50px;
        }
    </style>

    @yield('header', view(MarketplaceHelper::viewPath('vendor-dashboard.layouts.header')))

    <script>
        window.siteUrl = "{{ BaseHelper::getHomepageUrl() }}";
    </script>

    <script type="text/javascript">
        'use strict';
        window.trans = Object.assign(window.trans || {}, JSON.parse('{!! addslashes(json_encode(trans('plugins/marketplace::marketplace'))) !!}'));

        var BotbleVariables = BotbleVariables || {};
        BotbleVariables.languages = {
            tables: {!! json_encode(trans('core/base::tables'), JSON_HEX_APOS) !!},
            notices_msg: {!! json_encode(trans('core/base::notices'), JSON_HEX_APOS) !!},
            pagination: {!! json_encode(trans('pagination'), JSON_HEX_APOS) !!},
            system: {
                character_remain: '{{ trans('core/base::forms.character_remain') }}'
            }
        };

        var RV_MEDIA_URL = {
            'media_upload_from_editor': '{{ route('marketplace.vendor.upload-from-editor') }}'
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('header')
</head>

<body class="bg-light" @if (session('locale_direction', 'ltr') == 'rtl') dir="rtl" @endif>
    <!-- Âm thanh "ting" -->
    <audio id="ting-sound" src="/vendor/core/plugins/marketplace/sound/ting.mp3" preload="auto"></audio>

    @yield('body', view(MarketplaceHelper::viewPath('vendor-dashboard.layouts.body')))

    @stack('pre-footer')

    @if (session()->has('status') ||
            session()->has('success_msg') ||
            session()->has('error_msg') ||
            (isset($errors) && $errors->count() > 0) ||
            isset($error_msg))
        <script type="text/javascript">
            'use strict';
            window.noticeMessages = [];
            @if (session()->has('success_msg'))
                noticeMessages.push({
                    'type': 'success',
                    'message': "{!! addslashes(session('success_msg')) !!}"
                });
            @endif
            @if (session()->has('status'))
                noticeMessages.push({
                    'type': 'success',
                    'message': "{!! addslashes(session('status')) !!}"
                });
            @endif
            @if (session()->has('error_msg'))
                noticeMessages.push({
                    'type': 'error',
                    'message': "{!! addslashes(session('error_msg')) !!}"
                });
            @endif
            @if (isset($error_msg))
                noticeMessages.push({
                    'type': 'error',
                    'message': "{!! addslashes($error_msg) !!}"
                });
            @endif
            @if (isset($errors))
                @foreach ($errors->all() as $error)
                    noticeMessages.push({
                        'type': 'error',
                        'message': "{!! addslashes($error) !!}"
                    });
                @endforeach
            @endif
        </script>
    @endif

    {!! Assets::renderFooter() !!}
    @yield('footer', view(MarketplaceHelper::viewPath('vendor-dashboard.layouts.footer')))

    @stack('scripts')
    @stack('footer')
    {!! apply_filters(THEME_FRONT_FOOTER, null) !!}

    {{-- script active Store Manager --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;

            const links = document.querySelectorAll('a[href]');
            links.forEach(function(link) {
                const linkPath = new URL(link.href).pathname;

                if (linkPath === '/vendor/storemanager/index') {
                    if (currentPath.startsWith('/vendor/storemanager/')) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                }
            });
        });
    </script>
    {{-- End script active Store Manager --}}

    {{-- scrip notification --}}
    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-app.js";
        import {
            getMessaging,
            getToken,
            isSupported
        } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: "AIzaSyCkT8S9nyIlIYk-Iul_OFx_v95BTeIOHuQ",
            authDomain: "test-notification-chichi.firebaseapp.com",
            projectId: "test-notification-chichi",
            storageBucket: "test-notification-chichi.firebasestorage.app",
            messagingSenderId: "781620930129",
            appId: "1:781620930129:web:a501087352209cb36a7a70",
            measurementId: "G-2XX8ZVN5GE",
        };

        // Kiểm tra trình duyệt có hỗ trợ Notification và FCM không
        if (!("Notification" in window)) {
            // alert("⚠️ Trình duyệt không hỗ trợ Notification API!");
        } else if (!(await isSupported())) {
            // alert("⚠️ Trình duyệt không hỗ trợ Firebase Cloud Messaging!");
        } else {
            console.log("✅ Notification API và FCM được hỗ trợ");

            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);

            async function registerAndGetToken() {
                try {
                    const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                    console.log('✅ Service Worker registered:', registration);

                    const currentToken = await getToken(messaging, {
                        serviceWorkerRegistration: registration,
                        vapidKey: "BL928HjG7vLAEqXbiJpQfMsW4b8a8aladaols2uQN08DSmBOYvJqKpieURetdFD2g6TtoFDt3BOmJziFOqawfos",
                    });

                    if (currentToken) {
                        console.log('🎯 FCM Token:', currentToken);
                        // alert('🎯 Token đã lấy:\n' + currentToken);

                        // Gửi token lên server
                        const res = await fetch('/save-fcm-token', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                token: currentToken
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            console.log('✅ Token đã lưu thành công!');
                        } else {
                            console.warn('❌ Lưu token thất bại!');
                        }
                    } else {
                        // alert("⚠️ Không thể lấy token. Có thể người dùng chưa cấp quyền.");
                    }
                } catch (error) {
                    // alert("❌ Lỗi trong quá trình đăng ký hoặc lấy token");
                    console.error('❌ Lỗi:', error);
                }
            }

            // Yêu cầu quyền từ người dùng nếu chưa có
            if (Notification.permission === 'granted') {
                console.log("📌 Permission đã cấp sẵn");
                registerAndGetToken();
            } else {
                Notification.requestPermission().then(permission => {
                    console.log("🟢 Người dùng phản hồi:", permission);
                    if (permission === 'granted') {
                        registerAndGetToken();
                    } else {
                        // alert("⚠️ Bạn cần cấp quyền thông báo để nhận FCM");
                    }
                });
            }
        }
    </script>

    {{-- End scrip notification --}}
</body>

</html>
