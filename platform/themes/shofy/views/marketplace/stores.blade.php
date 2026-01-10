@php
  Theme::layout('full-width');
  Theme::set('pageTitle', __('Stores'));
@endphp
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="tp-page-area pt-30 pb-120">
  <div class="container">
    <div class="tp-shop-top mb-45">
      <div class="tp-shop-top-left d-flex flex-wrap gap-3 justify-content-between align-items-center">
        <div class="tp-shop-top-result">
          <p>
            {{ __('Showing :from-:to of :total stores', ['from' => $stores->firstItem(), 'to' => $stores->lastItem(), 'total' => $stores->total()]) }}
          </p>
        </div>

        <x-core::form :url="route('public.stores')" method="get">
          <div class="tp-sidebar-search-input">
            <input type="search" name="q" placeholder="{{ __('Search...') }}"
              value="{{ BaseHelper::stringify(old('q', request()->query('q'))) }}">
            <button type="submit" title="Search">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M8.11111 15.2222C12.0385 15.2222 15.2222 12.0385 15.2222 8.11111C15.2222 4.18375 12.0385 1 8.11111 1C4.18375 1 1 4.18375 1 8.11111C1 12.0385 4.18375 15.2222 8.11111 15.2222Z"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M16.9995 17L13.1328 13.1333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round"></path>
              </svg>
            </button>
          </div>
        </x-core::form>
      </div>
    </div>

    {!! apply_filters('ads_render', null, 'listing_page_before') !!}

    <div class="row g-4 mb-40">
      @foreach ($stores as $store)
        @php
          $coverImage = $store->getMetaData('background', true);
        @endphp

        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
          @include('plugins/marketplace::themes.includes.store-item')
        </div>
      @endforeach
    </div>

    {!! apply_filters('ads_render', null, 'listing_page_after') !!}

    {{ $stores->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) }}
<div id="store-map" style="height: 400px;;margin-bottom: 0!important"></div>
  </div>
  
</div>

@php
  use App\Helpers\GeoHelper;

  GeoHelper::setToken(env('GHN_TOKEN'));

  $storeAddresses = $stores->getCollection()->map(function ($store) {
      $ward = GeoHelper::getNameFromGHN('ward', 'WardCode', $store->ward);
      $district = GeoHelper::getNameFromGHN('district', 'DistrictID', $store->city);
      $province = GeoHelper::getNameFromGHN('province', 'ProvinceID', $store->state);
      $addressParts = [
          $store->address, // VD: 1919 Hùng Vương
          $ward, // VD: Cam Phú
          $district, // VD: Cam Ranh
          $province, // VD: Khánh Hòa
          $store->zip_code, // VD: 57806
          'Việt Nam',
      ];
      return [
          'name' => $store->name ?? '',
          'address' => implode(', ', array_filter($addressParts)),
          'lat' => $store->latitude,
          'lon' => $store->longitude,
          'level' => $store->store_level_id ?? 0,
      ];
  });
@endphp

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
 #store-map {
    margin-bottom: 0 !important;
    border-radius: 10px;
    border: 1px solid #ccc;
        position: sticky!important;
  }
</style>
<script>
  const storeAddresses = @json($storeAddresses);
  const map = L.map('store-map').setView([16.047079, 108.206230], 5); // Default center (TP.HCM)
  const bounds = [];
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
  function getMarkerIcon(levelId) {
    const colorMap = {
  1: 'red',
  2: 'green',
  3: 'blue',
  4: 'orange',
  5: 'purple',
  6: 'yellow',
  7: 'pink',
  8: 'brown',
  9: 'black',
  10: 'gray'
};

    const color = colorMap[levelId] || 'green';
    return L.icon({
      iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    });
  }
  storeAddresses.forEach(store => {
    if (store.lat && store.lon) {
      console.log(store)
      const icon = getMarkerIcon(store.level);
      const marker = L.marker([store.lat, store.lon], {
        icon
      }).addTo(map);
      marker.bindPopup(`<strong>${store.name}</strong><br>${store.address}`);
      bounds.push([store.lat, store.lon]);
    }
  });
  if (bounds.length > 1) {
    map.fitBounds(bounds);
  }
</script>
