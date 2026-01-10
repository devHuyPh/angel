<x-core::layouts.base>
    @include('core/base::layouts.' . AdminAppearance::getCurrentLayout() . '.partials.before-content')
    @if (setting('shipping_ghn_status') == 1)
        @include('GhnOrder.alert')
    @endif

    <div @class([
        'page-wrapper',
        'rv-media-integrate-wrapper' => Route::currentRouteName() === 'media.index',
    ])>
        @include('core/base::layouts.partials.page-header')

        <div class="page-body page-content">
            <div class="{{ AdminAppearance::getContainerWidth() }}">
                {!! apply_filters('core_layout_before_content', null) !!}

                @yield('content')

                {!! apply_filters('core_layout_after_content', null) !!}
            </div>
        </div>

        @include('core/base::layouts.partials.footer')
    </div>

    @include('core/base::layouts.' . AdminAppearance::getCurrentLayout() . '.partials.after-content')

    <x-slot:header-layout>
        @if (\Botble\Base\Supports\Core::make()->isSkippedLicenseReminder())
            @include('core/base::system.license-invalid', ['hidden' => false])
        @endif
    </x-slot:header-layout>

    <x-slot:footer>
        @stack('style-lib')
        @stack('js')
        @include('core/base::global-search.form')
        @include('core/media::partials.media')

        {!! rescue(fn() => app(Tighten\Ziggy\BladeRouteGenerator::class)->generate(), report: false) !!}

        @if (App::hasDebugModeEnabled())
            <x-core::debug-badge />
        @endif
    </x-slot:footer>
</x-core::layouts.base>
