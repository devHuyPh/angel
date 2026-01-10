<ul {!! $options !!}>
    @foreach ($menu_nodes->loadMissing('metadata') as $key => $row)
        <li @class(['has-dropdown' => $row->has_child])>
            <a href="{{ url($row->url) }}"
               title="{{ $row->title }} "
               @if ($row->target !== '_self') target="{{ $row->target }}" @endif
            >
                {!! $row->icon_html !!}

                {!! BaseHelper::clean($row->title) !!}

                @if ($row->has_child)
                    <x-core::icon name="ti ti-chevron-down" />
                @endif
            </a>

            @if ($row->has_child)
                {!!
                    Menu::generateMenu([
                        'menu' => $menu,
                        'menu_nodes' => $row->child,
                        'view' => 'main-menu',
                         'options' => [
                             'class' => 'tp-submenu text-white',
                         ],
                     ])
                 !!}
            @endif
        </li>
    @endforeach
<!--  <li class="mobile-only">-->
<!--    <a href="{{ auth('customer')->check() ? route('customer.overview') : route('customer.login') }}" -->
<!--       title="Giới thiệu"  -->
<!--       @auth('customer')-->
<!--            title="{{ auth('customer')->user()->name }}"-->
<!--       @endauth>-->
<!--        <x-core::icon name="ti ti-user" />-->
<!--        <span>{{ __('Account') }}</span>-->
<!--    </a>-->
<!--</li>-->

</ul>
