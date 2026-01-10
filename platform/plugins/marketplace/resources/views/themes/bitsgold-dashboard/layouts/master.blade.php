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

  <title>{{ucfirst(Request::segment(2))}} | Marketing</title>

  <style>
    :root {
      --primary-font: '{{ theme_option('primary_font', 'Muli') }}', sans-serif;
      --primary-color:
        {{ theme_option('primary_color', '#fab528') }}
      ;
    }

    /* Ensure the section takes full width */
    .refferal-link {
      padding: 15px;
    }

    /* Style the input group for better mobile display */
    .input-group {
      flex-wrap: nowrap;
    }

    .input-group .form-control {
      font-size: 14px;
    }

    .input-group .btn {
      font-size: 14px;
      white-space: nowrap;
    }

    /* Style the table for mobile */
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table {
      width: 100%;
      min-width: 600px;
      /* Ensure the table is wide enough to scroll on mobile */
    }

    .table th,
    .table td {
      font-size: 14px;
      padding: 8px;
    }

    /* Style the dropdown for mobile */
    .form-select {
      width: 100%;
      font-size: 16px;
    }

    /* Hide vertical tabs on mobile and show dropdown */
    @media (max-width: 767.98px) {
      .d-none.d-md-block {
        display: none !important;
      }

      .d-block.d-md-none {
        display: block !important;
      }

      /* Adjust table font size for smaller screens */
      .table th,
      .table td {
        font-size: 12px;
        padding: 6px;
      }

      /* Ensure the input group button doesn't shrink too much */
      .input-group .btn {
        font-size: 12px;
        padding: 6px 10px;
      }
    }

    /* Ensure the table header stands out */
    .table thead th {
      position: sticky;
      top: 0;
      z-index: 1;
    }

    @import url(https://fonts.googleapis.com/css?family=Roboto:300,400,700&display=swap);


    .phone-vr>.phone-vr-img-circle>img {
      display: none !important;
    }

    body {
      font-family: "Roboto", sans-serif;
      background: #EFF1F3;
      min-height: 100vh;
      position: relative;
    }

    .section-50 {
      padding: 50px 0;
    }

    .m-b-50 {
      margin-bottom: 50px;
    }

    .dark-link {
      color: #333;
    }

    .heading-line {
      position: relative;
      padding-bottom: 5px;
    }

    .heading-line:after {
      content: "";
      height: 4px;
      width: 75px;
      background-color: #29B6F6;
      position: absolute;
      bottom: 0;
      left: 0;
    }

    .notification-ui_dd-content {
      margin-bottom: 30px;
    }

    .notification-list {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      -webkit-box-pack: justify;
      -ms-flex-pack: justify;
      justify-content: space-between;
      padding: 20px;
      margin-bottom: 7px;
      background: #fff;
      -webkit-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
    }

    .notification-list--unread {
      border-left: 2px solid #29B6F6;
    }

    .notification-list .notification-list_content {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
    }

    .notification-list .notification-list_content .notification-list_img img {
      height: 48px;
      width: 48px;
      border-radius: 50px;
      margin-right: 20px;
    }

    .notification-list .notification-list_content .notification-list_detail p {
      margin-bottom: 5px;
      line-height: 1.2;
    }

    .notification-list .notification-list_feature-img img {
      height: 48px;
      width: 48px;
      border-radius: 5px;
      margin-left: 20px;
    }

    /* duong */
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

      .tp-header-action-item a[href*="compare"] {
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

    }

    /* endduong */


    #tp-bottom-menu-sticky {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    background-color: #fff;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
    padding: 8px 0;
    border-radius: 16px 16px 0 0;
    border-top: 1px solid #eee;
}

.tp-mobile-menu .tp-mobile-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.tp-mobile-item-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    cursor: pointer;
    color: #555;
    font-size: var(--bottom-bar-menu-text-font-size, 12px);
    transition: color 0.2s ease;
}

.tp-mobile-item-btn:focus {
    outline: none;
    box-shadow: none;
}

.tp-mobile-item-btn svg {
    width: 24px;
    height: 24px;
    margin-bottom: 4px;
    stroke-width: 1.8;
}

.tp-mobile-item-btn:hover {
    color: #007bff;
}

  </style>



  @yield('header', view(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.header')))

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

  @stack('header')
</head>

<body @if (session('locale_direction', 'ltr') == 'rtl') dir="rtl" @endif>

  {{-- duong --}}
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
  {{-- endduong --}}

  @yield('body', view(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.body')))

{{-- ahuy --}}
@if (theme_option('enabled_bottom_menu_bar_on_mobile', true))
  <div id="tp-bottom-menu-sticky" @class(['tp-mobile-menu d-lg-none', 'menu--footer__hide_text' => theme_option('bottom_bar_menu_show_text', 'yes') != 'yes'])
    style="--bottom-bar-menu-text-font-size: {{ theme_option('bottom_bar_menu_text_font_size', 13) }}px;">
    <div class="container">
    <div @class(['row', 'row-cols-4' => is_plugin_active('ecommerce'), 'row-cols-2' => !is_plugin_active('ecommerce')])>
      @if (is_plugin_active('ecommerce'))
      <div class="col">
      <div class="text-center tp-mobile-item">
      <a href="{{ route('public.products') }}" class="tp-mobile-item-btn">
      <x-core::icon name="ti ti-shopping-bag" />
      <!--<span>{{ __('Store') }}</span>-->
      </a>
      </div>
      </div>
    @endif
      <div class="col">
      <div class="text-center tp-mobile-item">
        <button class="tp-mobile-item-btn tp-search-open-btn">
        <x-core::icon name="ti ti-search" />
        <!--<span>{{ __('Search') }}</span>-->
        </button>
      </div>
      </div>
      @if (is_plugin_active('ecommerce'))
      @if (EcommerceHelper::isWishlistEnabled())
      <div class="col">
      <div class="text-center tp-mobile-item">
      <a href="{{ route('public.wishlist') }}" class="tp-mobile-item-btn">
      <x-core::icon name="ti ti-heart" />
      <!--<span>{{ __('Wishlist') }}</span>-->
      </a>
      </div>
      </div>
    @endif
      <div class="col">
      <div class="text-center tp-mobile-item">
      <a href="{{ auth('customer')->check() ? route('customer.overview') : route('customer.login') }}"
      class="tp-mobile-item-btn" @auth('customer') title="{{ auth('customer')->user()->name }}" @endauth>
      <x-core::icon name="ti ti-user" />
      <!--<span>{{ __('Account') }}</span>-->
      </a>
      </div>
      </div>
    @endif

    </div>
    </div>
  </div>
@endif
{{-- endahuy --}}

  @stack('pre-footer')

  @if (
  session()->has('status') ||
  session()->has('success_msg') ||
  session()->has('error_msg') ||
  (isset($errors) && $errors->count() > 0) ||
  isset($error_msg)
)
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
  @yield('footer', view(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.footer')))

  @stack('scripts')
  @stack('footer')
  {{-- {!! apply_filters(THEME_FRONT_FOOTER, null) !!} --}}

  <script>
    document.addEventListener("DOMContentLoaded", function () {
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
              notifyDiv.style.display = "none"; // Ẩn hẳn sau khi hiệu ứng kết thúc
            }, 1000); // Phù hợp với thời gian `fadeOut`
          }, 5000);
        }

        notifyDiv.addEventListener('click', function () {
          notifyDiv.classList.add('hidden-important');
        });
      }

      setInterval(checkNewNotifications, 10000);
    });

  </script>


@yield('ahuy')
</body>

</html>
