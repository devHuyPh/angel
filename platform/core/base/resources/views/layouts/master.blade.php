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
        
        
        @push('footer')
      <script>
      document.addEventListener("DOMContentLoaded", function () {
        document.addEventListener("change", function (event) {
        if (event.target.matches('[data-type="city"]')) {
          let districtId = event.target.value; // Lấy giá trị quận/huyện đã chọn
          let wardSelect = document.querySelector('[data-type="ward"]'); // Chọn dropdown ward

          if (!districtId) {
          wardSelect.innerHTML = `<option value="">Chọn phường/xã...</option>`;
          wardSelect.setAttribute("disabled", "true");
          return;
          }

          let apiUrl = `https://online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id=${districtId}`;
          let token = "2c2e62dc-ee72-11ef-a3aa-e2c95c1f5bee"; // Token GHN của bạn

          // Gọi API lấy danh sách phường/xã
          fetch(apiUrl, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            "Token": token
          }
          })
          .then(response => response.json())
          .then(data => {
            wardSelect.innerHTML = `<option value="">Chọn phường/xã...</option>`; // Reset danh sách

            if (data.code === 200 && Array.isArray(data.data)) {
            data.data.forEach(ward => {
              let option = document.createElement("option");
              option.value = ward.WardCode;
              option.textContent = ward.WardName;
              wardSelect.appendChild(option);
            });
            wardSelect.removeAttribute("disabled"); // Bật dropdown khi có dữ liệu
            } else {
            wardSelect.innerHTML = `<option value="">Không có dữ liệu...</option>`;
            }
          })
          .catch(error => {
            console.error("Lỗi khi tải danh sách phường/xã:", error);
            wardSelect.innerHTML = `<option value="">Lỗi tải dữ liệu...</option>`;
            wardSelect.setAttribute("disabled", "true");
          });
        }
        });
      });
      </script>
    @endpush
        
    </x-slot:footer>
</x-core::layouts.base>
