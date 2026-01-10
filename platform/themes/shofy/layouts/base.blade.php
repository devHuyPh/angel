<!doctype html>
<html {!! Theme::htmlAttributes() !!}>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1"
        name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {!! Theme::partial('header-meta') !!}

    {!! Theme::header() !!}

    {!! Theme::get('add_head') !!}
</head>
<style>
    #ghn-error-alert,
    #notify-success-alert {
        /* display: none; Xóa !important */
        position: fixed;
        top: 20px;
        right: 20px;
        width: 400px;
        z-index: 9999;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }

    #ghn-error-alert,
    #notify-success-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 400px;
        z-index: 9999;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }

    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    .notify-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 400px;
        display: none;
        animation: slideDown 0.5s ease-out;
    }

    .hidden-important {
        animation: fadeOut 1s ease-out;
        opacity: 0;
        display: none !important;
    }

    .tp-main-menu-content>ul>li>a:hover {
        color: white !important;
    }

    .tp-main-menu-content>ul>li>a {
        color: white !important;
    }

    .tp-header-action-btn>svg {
        color: white !important;
    }

    .notifi_canvas {
        z-index: 999999;
        padding-left: 25px;
        padding-right: 25px;

        h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 0;
        }
    }

    .tp-mobile-item-btn>svg {
        stroke-width: 2px !important;
    }

    .tp-main-menu-content a svg {
        stroke-width: 2px !important;
    }

    @media (max-width: 767px) {
        .tp-header-transparent {
            top: 3.5rem;
        }

        .tp-header-action-item a[href*="so-sanh"] {
            display: none !important;
        }

        .logo__mobile {
            width: 42px;
        }

        #notify-success-alert {
            /* position: fixed; */
            width: 350px;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            */
        }

        .tp-submenu>li>a {
            color: #fff !important;
        }

    }

    .has-dropdown .dropdown-toggle-btn {
        color: #fff !important;
        border: none !important;
    }

    .tp-header-currency-toggle,
    .tp-header-setting-toggle,
    .tp-header-setting,
    .tp-header-lang-toggle {
        display: flex !important;
        white-space: nowrap;
        align-items: center;
        justify-content: center;
    }

    /* .tp-header-lang>a {
    pointer-events: none;
        } */

    /* .tp-submenu>li>a{
            color: #fff !important;
        } */

    .cartmini__area {
        z-index: 99999 !important;
    }

    .opened {
        z-index: 99999 !important;
    }

    .offcanvas__area {
        z-index: 999999 !important;
    }
</style>

<body {!! Theme::bodyAttributes() !!}>
    {!! apply_filters(THEME_FRONT_BODY, null) !!}

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>
    <!-- Thông báo thành công -->
    <div id="notify-success-alert" class="alert alert-success d-flex align-items-center notify-alert" role="alert"
        style="display: none !important">
        {{-- display: none !important --}}
        <div class="row">
            <div class="col-md-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-bell">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                    <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                </svg>
            </div>

            <div class="col-md-11" id="notifi-content"></div>
        </div>

    </div>

    {{-- modal --}}

    {{-- @php
        $customer = auth('customer')->user();
        @endphp
        @if ($customer && $customer->kyc_status == 0)
            <div class="modal fade newsletter-popup" id="newsletterModal" tabindex="-1" aria-labelledby="newsletterPopupModalLabel" aria-hidden="true">
                <link rel="stylesheet" href="https://mocthienan.vn/vendor/core/plugins/newsletter/css/newsletter.css?v=1.2.5">

                <div class="modal-dialog modal-lg" style="margin-top: 186px;">
                    <div class="modal-content border-0 d-flex flex-md-col flex-lg-row">
                        <div class="d-none d-md-block col-6 newsletter-popup-bg">
                            <img src="https://mocthienan.vn/storage/banner/kyc-la-gi-1030x618.png"
                                loading="eager" alt="Đăng ký ngay" class="entered loading">
                        </div>

                        <button type="button" class="btn-close position-absolute"
                            data-bs-dismiss="modal" aria-label="Close"></button>

                        <div class="newsletter-popup-content">
                            <div class="modal-header flex-column align-items-start border-0 p-0">
                                <span class="modal-subtitle">Định danh tài khoản</span>
                                <h5 class="modal-title fs-2" id="newsletterPopupModalLabel">Bạn chưa thực hiện định danh tài khoản</h5>
                                <p class="modal-text text-muted"></p>
                            </div>
                            <div class="modal-body p-0">
                                Để được nhận các quyền lợi về chương trình tiếp thị của chúng tôi như thưởng xếp hạng, thưởng giới thiệu mua hàng và thưởng cộng đồng. 
                                Vui lòng thực hiện định danh tài khoản <br> <a class="text-primary" href="{{route('kyc.index')}}">tại đây</a>. 
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(function () {
                        var myModal = new bootstrap.Modal(document.getElementById('newsletterModal'));
                        myModal.show();
                    }, 5000);
                });
            </script>
        @endif --}}

    @yield('content')

    {!! Theme::footer() !!}
    @if (auth('customer')->user())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                function checkNewNotifications() {
                    fetch('/notifications/latest')
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success" && data.notification) {
                                showNotification(data.notification);
                            }
                        })
                        .catch(error => console.error("Lỗi khi lấy thông báo:", error));
                }

                function showNotification(notification) {
                    const notifyDiv = document.getElementById("notify-success-alert");
                    const notifyContent = document.getElementById("notifi-content");

                    if (notifyDiv && notifyContent) {
                        notifyContent.innerHTML = `${notification.description}`;
                        notifyDiv.style.display = "flex"; // Hiển thị thông báo
                        notifyDiv.classList.remove("hidden-important"); // Xóa hiệu ứng ẩn nếu có

                        // Ẩn thông báo sau 5 giây
                        setTimeout(() => {
                            notifyDiv.classList.add("hidden-important");
                            setTimeout(() => {
                                notifyDiv.style.display = "none";
                            }, 1000);
                        }, 5000);
                    }

                    notifyDiv.addEventListener('click', function() {
                        notifyDiv.classList.add('hidden-important');
                    });
                }

                setInterval(checkNewNotifications, 10000);
            });
        </script>
    @endif
    {!! Theme::get('add_footer') !!}
</body>

</html>
