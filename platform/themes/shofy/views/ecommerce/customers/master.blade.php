@php
    Theme::set('breadcrumbStyle', 'none');
    Theme::layout('full-width');

    Theme::set(
        'add_head',
        '
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
        ',
    );

    Theme::set(
        'add_footer',
        '
            <script>
                document.addEventListener(\'DOMContentLoaded\', function() {
                        const adminBar = document.getElementById(\'admin_bar\');
                                const header = document.querySelector(\'.header\');
                                    const content = document.getElementById(\'custom-content-js\');

                                        if (adminBar && header) {
                                            const adminBarHeight = adminBar.offsetHeight;
                                            header.style.top = adminBarHeight + \'px\';
                                        }

                                        if (header && content) {
                                            const adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
                                            const headerHeight = header.offsetHeight;

                                            const totalHeight = headerHeight;

                                            console.log(totalHeight);

                                            let roundedHeight = 0;

                                            if (totalHeight < 55) {
                                                roundedHeight = 5 + (Math.ceil(totalHeight / 5) * 5);
                                            } else {
                                                roundedHeight = 10 + (Math.ceil(totalHeight / 5) * 5);
                                            }

                                            const mtClass = \'mt-\' + roundedHeight;

                                            content.classList.forEach(cls => {
                                                if (/^mt-\d+$/.test(cls)) {
                                                    content.classList.remove(cls);
                                                }
                                            });

                                            content.classList.add(mtClass);
                                        }
                                    });
            </script>
        ',
    );
@endphp
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
    .page-item {
        a {
            color: #111 !important;
        }
    }

    .active {
        span {
            background: #228822 !important;
        }
    }

    @media (max-width: 767.98px) {

        .container-mobile {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        .position-mobile {
            position: absolute;
            z-index: 99998;
            left: 0;
            padding-right: 0 !important;
            padding-left: 0 !important;

            display: none !important;
        }

        .menu-mobile {
            display: flex !important;
            flex-direction: row;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            /* gap: 0.5rem; */
        }

        .menu-mobile li {
            flex: 0 0 auto;

            /* position: relative; */
            /* ngăn li co lại hoặc giãn ra */
            a {
                border-bottom: 1px solid gray !important;
            }
        }

        .span-swicht {
            display: flex !important;
            flex-direction: column !important;
            /* /* width: 80px !important; */
            text-align: center !important;
            color: #111;

            svg {
                color: #111;
            }
        }

        .size-icon-mobile {
            width: 25px !important;
            height: 25px !important;
        }

        .profile__tab {
            margin-bottom: 20px !important;
        }

        /* .head-show{
            position: relative;
        } */

        .show-mobile {
            background-color: rgba(var(--bs-white-rgb), var(--bs-bg-opacity)) !important;
            box-shadow: var(--bs-box-shadow-sm) !important;
            z-index: 999999999;
            /* position: absolute; */
            /* display: block; hoặc dùng collapse nếu bạn cần Bootstrap toggle */
        }

        .bg-custom-moblie {
            background-color: transparent !important;
            box-shadow: none !important;
        }

        .rounded {
            border-radius: 0 !important;
            /* box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important; */
            background-color: #fff;
        }

        .alert-title {
            line-height: 15px;
            /* color: red; */
        }

        .text-success {
            .span-swicht {
                color: rgba(var(--bs-success-rgb), var(--bs-text-opacity)) !important;

                .size-icon-mobile {
                    color: rgba(var(--bs-success-rgb), var(--bs-text-opacity)) !important;
                }
            }
        }


        .form-control {
            border-radius: 0.375rem !important;
        }



        #header-sticky,
        .tp-subscribe-area,
        footer {
            display: none !important;
        }

        .header {
            background-color: white !important;
            padding: 1rem !important;
            border-bottom: 1px solid #e9ecef !important;
        }

        .header-title {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            /* color: #212529 !important; */
            margin: 0 !important;
        }

        .back-btn {
            background: none !important;
            border: none !important;
            font-size: 1.2rem !important;
            /* color: #6c757d !important; */
            padding: 0 !important;
            margin-right: 1rem !important;
        }

    }
</style>


<section class="profile__area pt-0 pt-md-5 pb-120">
    <div class="container container-mobile">
        <div class="profile__inner p-relative">
            <div class="row">
                @php
                    $menuItems = DashboardMenu::getAll('customer');
                    // dd($menuItems);
                    // $locale = app()->getLocale();
                    // // dd($locale);
                    // $defaultLocale = 'vi';
                    // dd($defaultLocale);
                @endphp

                <div class="col-xxl-4 col-lg-4 position-mobile">
                    <div class="profile__tab me-40">
                        <div class="bg-white p-0 p-md-4 rounded shadow-sm bg-custom-moblie">
                            <ul class="list-unstyled m-0 menu-mobile" style="position: relative">
                                @foreach ($menuItems as $item)
                                    @continue(!$item['name'])

                                    @php
                                        $hasChildren = isset($item['children']) && $item['children']->isNotEmpty();
                                        $locale = app()->getLocale();
                                        $defaultLocale = 'vi';

                                        // Active nếu bất kỳ child nào khớp URL hiện tại
                                        $isParentActive = false;

                                        if ($hasChildren) {
                                            foreach ($item['children'] as $child) {
                                                $childUrl = $child['url'];

                                                if (
                                                    $locale !== $defaultLocale &&
                                                    !str_starts_with(parse_url($childUrl, PHP_URL_PATH), '/' . $locale)
                                                ) {
                                                    $childUrl = url(
                                                        $locale . '/' . ltrim(parse_url($childUrl, PHP_URL_PATH), '/'),
                                                    );
                                                }

                                                $requestPath = '/' . ltrim(request()->path(), '/');
                                                $targetPath = parse_url($childUrl, PHP_URL_PATH);

                                                if (str_starts_with($requestPath, $targetPath)) {
                                                    $isParentActive = true;
                                                    break;
                                                }
                                            }
                                        }

                                        $isActive = $isParentActive || $item['active'];
                                        $iconColor = $isActive ? '#228822' : '#6c757d';
                                    @endphp

                                    <li class="mb-2">
                                        <a href="{{ $hasChildren ? '#submenu-' . $item['id'] : $item['url'] }}"
                                            class="d-flex align-items-center justify-content-between px-3 py-2 rounded {{ $isActive ? ' bg-light border-start border-end border-4 border-success text-success fw-semibold' : 'text-muted' }}"
                                            style="text-decoration: none; transition: 0.2s;"
                                            @if ($hasChildren) data-bs-toggle="collapse"
                                                aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                                                aria-controls="submenu-{{ $item['id'] }}" @endif>

                                            <span class="d-flex align-items-center span-swicht">
                                                <x-core::icon :name="$item['icon']" class="me-2 size-icon-mobile"
                                                    style="width: 18px; height: 18px; color: {{ $iconColor }};" />
                                                {{ $item['name'] }}
                                            </span>
                                            @if ($hasChildren)
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    fill="currentColor" class="ms-2"
                                                    style="transition: 0.3s; transform: rotate({{ $item['active'] ? '180deg' : '0deg' }});"
                                                    viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd"
                                                        d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                                                </svg>
                                            @endif
                                        </a>

                                        @if ($hasChildren)
                                            <div class="show-mobile collapse {{ $isActive ? 'show' : '' }} ps-md-4"
                                                {{-- collapse --}} id="submenu-{{ $item['id'] }}">

                                                {{-- id="submenu-{{ $item['id'] }}"> --}}
                                                @foreach ($item['children'] as $child)
                                                    @php
                                                        $locale = app()->getLocale();
                                                        $defaultLocale = 'vi'; // hoặc lấy từ config
                                                        $url = $child['url'];

                                                        if (
                                                            $locale !== $defaultLocale &&
                                                            !str_starts_with(
                                                                parse_url($url, PHP_URL_PATH),
                                                                '/' . $locale,
                                                            )
                                                        ) {
                                                            $url = url(
                                                                $locale .
                                                                    '/' .
                                                                    ltrim(parse_url($url, PHP_URL_PATH), '/'),
                                                            );
                                                        }

                                                        // So sánh path hiện tại với path từ URL đã xử lý
                                                        $requestPath = '/' . ltrim(request()->path(), '/');
                                                        $targetPath = parse_url($url, PHP_URL_PATH);
                                                        $isChildActive = str_starts_with($requestPath, $targetPath);
                                                    @endphp


                                                    <a href="{{ $url }}"
                                                        class="d-flex align-items-center px-3 py-2 rounded mt-1 {{ $isChildActive ? 'text-success fw-semibold bg-light' : 'text-muted' }}"
                                                        style="text-decoration: none;">

                                                        <x-core::icon :name="$child['icon']" class="me-2"
                                                            style="width: 16px; height: 16px; color: {{ $child['active'] ? '#228822' : '#6c757d' }};" />
                                                        {{ $child['name'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-8 col-lg-8 mt-60 mt-md-0" id="custom-content-js">
                    <div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.matchMedia('(max-width: 767.98px)').matches) {
            const submenu = document.getElementById('submenu-cms-customer-bitsgold');
            const activeLink = document.querySelector('.menu-mobile a.bg-light.border-success');

            if (submenu) {
                const activeLink = submenu.querySelector('a.text-success.fw-semibold.bg-light');

                if (activeLink) {
                    submenu.className = 'show-mobile ps-md-4 collapse';
                }
            }

            if (activeLink) {
                const container = document.querySelector('.menu-mobile');
                const containerRect = container.getBoundingClientRect();
                const linkRect = activeLink.getBoundingClientRect();

                const offset = linkRect.left - containerRect.left - (containerRect.width / 2) + (linkRect
                    .width / 2);
                container.scrollLeft += offset;
            }
        }
    });
</script>
