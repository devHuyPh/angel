<link media="all" type="text/css" rel="stylesheet"
    href="{{ url('/') }}/vendor/core/core/base/css/libraries/select2.css?v=..." />
<style>
    .select2-dropdown--below,
    .select2-dropdown--above {
        background: #fff !important;
        border: 1px solid #ced4da !important;
    }

    .select2-dropdown--above {
        border-color: #ced4da !important;
    }

    .select2-search__field {
        border: 1px solid #dcdcdc !important;
        height: 45px !important;
        border-radius: 0.375rem !important;
    }

    .select2-search__field:focus,
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        background-color: #fff !important;
        border: 2px solid rgb(75, 162, 19) !important;
        box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25) !important;
        color: #212529 !important;
        outline: 0 !important;
        border-radius: 0.375rem !important;
    }

    .select2-container--default .select2-dropdown {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;
        border: 1px solid gray !important;
        border-bottom: none !important;
    }

    .select2-selection--single {
        border: 1px solid #dcdcdc !important;
        height: 45px !important;
        border-radius: 0.375rem !important;
    }

    .select2-results__option--selected,
    .select2-results__option:hover {
        background-color: #1967D2 !important;
        color: #fff !important;
    }

    .select2-container--default {
        font-size: 14px !important;
    }
</style>

<div class="customer-address-payment-form">
    <input type="hidden" name="update-tax-url" id="update-checkout-tax-url"
        value="{{ route('public.ajax.checkout.update-tax') }}">
    <div class="mb-3 form-group">
        @if (auth('customer')->check())
            <p>{{ __('Account') }}: <strong>{{ auth('customer')->user()->name }}</strong> - {!! Html::email(auth('customer')->user()->email) !!} (<a
                    href="{{ route('customer.logout') }}">{{ __('Logout') }})</a></p>
        @else
            <p>{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __('Login') }}</a></p>
        @endif
    </div>

    {!! apply_filters('ecommerce_checkout_address_form_before') !!}

    @auth('customer')
        <div class="mb-3 form-group">
            @if ($isAvailableAddress)
                <label class="mb-2 form-label" for="address_id">{{ __('Select available addresses') }}:</label>
            @endif
            @php
                $oldSessionAddressId = old('address.address_id', $sessionAddressId);
            @endphp
            <div class="list-customer-address @if (!$isAvailableAddress) d-none @endif">
                <div class="select--arrow">
                    <select class="form-control" id="address_id" name="address[address_id]" @required($isAvailableAddress)>
                        <option value="new" @selected($oldSessionAddressId == 'new')>{{ __('Add new address...') }}</option>
                        @if ($isAvailableAddress)
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}"
                                    data-code="{{ $address->state }}, {{ $address->city }}, {{ $address->ward_id }}"
                                    @selected($oldSessionAddressId == $address->id)>{{ $address->full_address }}</option>
                            @endforeach
                        @endif
                    </select>
                    <x-core::icon name="ti ti-chevron-down" />
                </div>
                <br>
                <div class="address-item-selected @if (!$sessionAddressId) d-none @endif">
                    @if ($isAvailableAddress && $oldSessionAddressId != 'new')
                        @if ($oldSessionAddressId && $addresses->contains('id', $oldSessionAddressId))
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => $addresses->firstWhere('id', $oldSessionAddressId),
                            ])
                        @elseif ($defaultAddress = get_default_customer_address())
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => $defaultAddress,
                            ])
                        @else
                            @include('plugins/ecommerce::orders.partials.address-item', [
                                'address' => Arr::first($addresses),
                            ])
                        @endif
                    @endif
                </div>
                <div class="list-available-address d-none">
                    @if ($isAvailableAddress)
                        @foreach ($addresses as $address)
                            <div class="address-item-wrapper" data-id="{{ $address->id }}">
                                @include(
                                    'plugins/ecommerce::orders.partials.address-item',
                                    compact('address'))
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endauth

    <div class="address-form-wrapper @if (auth('customer')->check() && $oldSessionAddressId !== 'new' && $isAvailableAddress) d-none @endif">
        <div class="form-group mb-3 @error('address.name') has-error @enderror">
            <div class="form-input-wrapper">
                <input class="form-control" id="address_name" name="address[name]" autocomplete="family-name"
                    type="text"
                    value="{{ old('address.name', Arr::get($sessionCheckoutData, 'name')) ?: (auth('customer')->check() ? auth('customer')->user()->name : null) }}"
                    required>
                <label for="address_name">{{ __('Full Name') }}</label>
            </div>
            {!! Form::error('address.name', $errors) !!}
        </div>

        <div class="row">
            @if (!in_array('email', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div @class([
                    'col-12',
                    'col-lg-8' => !in_array(
                        'phone',
                        EcommerceHelper::getHiddenFieldsAtCheckout()),
                ])>
                    <div class="form-group mb-3 @error('address.email') has-error @enderror">
                        <div class="form-input-wrapper">
                            <input class="form-control" id="address_email" name="address[email]" autocomplete="email"
                                type="email"
                                value="{{ old('address.email', Arr::get($sessionCheckoutData, 'email')) ?: (auth('customer')->check() ? auth('customer')->user()->email : null) }}"
                                required>
                            <label for="address_email">{{ __('Email') }}</label>
                        </div>
                        {!! Form::error('address.email', $errors) !!}
                    </div>
                </div>
            @endif
            @if (!in_array('phone', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div @class([
                    'col-12',
                    'col-lg-4' => !in_array(
                        'email',
                        EcommerceHelper::getHiddenFieldsAtCheckout()),
                ])>
                    <div class="form-group mb-3 @error('address.phone') has-error @enderror">
                        <div class="form-input-wrapper">
                            <input class="form-control" id="address_phone" name="address[phone]" autocomplete="phone"
                                type="tel"
                                value="{{ old('address.phone', Arr::get($sessionCheckoutData, 'phone')) ?: (auth('customer')->check() ? auth('customer')->user()->phone : null) }}">
                            <label for="address_phone">{{ __('Phone') }}</label>
                        </div>
                        {!! Form::error('address.phone', $errors) !!}
                    </div>
                </div>
            @endif
        </div>

        {!! apply_filters('ecommerce_checkout_address_form_inside', null) !!}

        @if (EcommerceHelper::isUsingInMultipleCountries() && !in_array('country', EcommerceHelper::getHiddenFieldsAtCheckout()))
            <div class="form-group mb-3 @error('address.country') has-error @enderror">
                <div class="select--arrow form-input-wrapper">
                    <select class="form-control" id="address_country" name="address[country]" autocomplete="country"
                        data-form-parent=".customer-address-payment-form" data-type="country" required>
                        @foreach (EcommerceHelper::getAvailableCountries() as $countryCode => $countryName)
                            <option value="{{ $countryCode }}" @selected(old('address.country', Arr::get($sessionCheckoutData, 'country', EcommerceHelper::getDefaultCountryId())) == $countryCode)>
                                {{ $countryName }}
                            </option>
                        @endforeach
                    </select>
                    <x-core::icon name="ti ti-chevron-down" />
                    <label for="address_country">{{ __('Country') }}</label>
                </div>
                {!! Form::error('address.country', $errors) !!}
            </div>
        @else
            <input id="address_country" name="address[country]" type="hidden"
                value="{{ EcommerceHelper::getFirstCountryId() }}">
        @endif

        <div class="row">
            @if (!in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div class="col-sm-6 col-12">
                    <div class="form-group mb-3 @error('address.state') has-error @enderror">
                        @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
                            <div class="select--arrow form-input-wrapper">
                                <select class="form-control select2" id="address_state" name="address[state]"
                                    autocomplete="state" data-form-parent=".customer-address-payment-form"
                                    data-type="state" data-url="{{ route('ajax.states-by-country') }}" required>
                                    <option value="">{{ __('Chọn tỉnh thành...') }}</option>
                                    @if (old('address.country', Arr::get($sessionCheckoutData, 'country') ?: EcommerceHelper::getDefaultCountryId()) ||
                                            !EcommerceHelper::isUsingInMultipleCountries())
                                        @foreach (EcommerceHelper::getAvailableStatesByCountry(old('address.country', Arr::get($sessionCheckoutData, 'country') ?: EcommerceHelper::getDefaultCountryId())) as $stateId => $stateName)
                                            <option value="{{ $stateId }}"
                                                @if (old('address.state', Arr::get($sessionCheckoutData, 'state')) == $stateId) selected @endif>{{ $stateName }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                {{-- <x-core::icon name="ti ti-chevron-down" /> --}}
                                <label style="z-index: 999;" for="address_state">{{ __('Tỉnh thành') }}</label>
                            </div>
                        @else
                            <div class="form-input-wrapper">
                                <input class="form-control" id="address_state" name="address[state]"
                                    autocomplete="state" type="text"
                                    value="{{ old('address.state', Arr::get($sessionCheckoutData, 'state')) }}"
                                    required>
                                <label for="address_state">{{ __('Tỉnh thành') }}</label>
                            </div>
                        @endif
                        {!! Form::error('address.state', $errors) !!}
                    </div>
                </div>
            @endif

            @if (!in_array('city', EcommerceHelper::getHiddenFieldsAtCheckout()))
                <div @class([
                    'col-sm-6 col-12' => !in_array(
                        'state',
                        EcommerceHelper::getHiddenFieldsAtCheckout()),
                    'col-12' => in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()),
                ])>
                    <div class="form-group mb-3 @error('address.city') has-error @enderror">
                        @if (EcommerceHelper::useCityFieldAsTextField())
                            <div class="form-input-wrapper">
                                <input class="form-control" id="address_city" name="address[city]" autocomplete="city"
                                    type="text"
                                    value="{{ old('address.city', Arr::get($sessionCheckoutData, 'city')) }}"
                                    required>
                                <label style="z-index: 999;" for="address_city">{{ __('Quận huyện') }}</label>
                            </div>
                        @else
                            <div class="select--arrow form-input-wrapper">
                                <select class="form-control select2" id="address_city" name="address[city]"
                                    autocomplete="city" data-type="city" data-using-select2="false"
                                    data-url="{{ route('ajax.cities-by-state') }}" required>
                                    <option value="">{{ __('Chọn quận huyện...') }}</option>
                                    @if (old('address.state', Arr::get($sessionCheckoutData, 'state')) ||
                                            in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()))
                                        @foreach (EcommerceHelper::getAvailableCitiesByState(old('address.state', Arr::get($sessionCheckoutData, 'state')), old('address.country', Arr::get($sessionCheckoutData, 'country'))) as $cityId => $cityName)
                                            <option value="{{ $cityId }}"
                                                @if (old('address.city', Arr::get($sessionCheckoutData, 'city')) == $cityId) selected @endif>{{ $cityName }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                {{-- <x-core::icon name="ti ti-chevron-down" /> --}}
                                <label style="z-index: 999;" for="address_city">{{ __('Quận huyện') }}</label>
                            </div>
                        @endif
                        {!! Form::error('address.city', $errors) !!}
                    </div>
                </div>
            @endif

            <div @class([
                'col-sm-6 col-12' => !in_array(
                    'state',
                    EcommerceHelper::getHiddenFieldsAtCheckout()),
                'col-12' => in_array('state', EcommerceHelper::getHiddenFieldsAtCheckout()),
            ])>
                <div class="form-group mb-3 @error('address.city') has-error @enderror">
                    @if (EcommerceHelper::useCityFieldAsTextField())
                        <div class="form-input-wrapper">
                            <input class="form-control" id="address_ward" name="address[ward]" autocomplete="ward"
                                type="text"
                                value="{{ old('address.ward', Arr::get($sessionCheckoutData, 'ward')) }}" required>
                            <label for="address_ward">{{ __('Phường xã') }}</label>
                        </div>
                    @else
                        @php
                            $selectedWard = old('address.ward') ?? ($address['ward'] ?? '');
                        @endphp

                        <div class="select--arrow form-input-wrapper">
                            <select class="form-control" id="address_ward" name="address[ward]" autocomplete="ward"
                                data-type="ward" data-using-select2="false" required>
                                <option value="">{{ __('Chọn quận để có phường xã....') }}</option>
                                {{-- @if (old('address.ward'))
                                    <option value="{{ old('address.ward') }}" selected>{{ explode('.', old('address.ward'))[1] }}</option>
                            @endif --}}
                            </select>
                            {{-- <x-core::icon name="ti ti-chevron-down" /> --}}
                            <label style="z-index: 999;" for="address_ward">{{ __('Phường xã') }}</label>
                        </div>
                    @endif
                    {!! Form::error('address.ward', $errors) !!}
                </div>
            </div>
        </div>


        {!! apply_filters('ecommerce_checkout_address_form_after_city_field', null, $sessionCheckoutData) !!}

        @if (!in_array('address', EcommerceHelper::getHiddenFieldsAtCheckout()))
            <div class="form-group mb-3 @error('address.address_detail') has-error @enderror">
                <div class="form-input-wrapper">
                    <input class="form-control" id="address_address_detail" name="address[address_detail]"
                        autocomplete="address_detail" type="text"
                        value="{{ old('address.address_detail', Arr::get($sessionCheckoutData, 'address_detail')) }}"
                        required>
                    <label for="address_address_detail">{{ __('Address') }}</label>
                </div>
                {!! Form::error('address.address_detail', $errors) !!}
            </div>
        @endif

        <input hidden class="form-control" id="address_address" name="address[address]" autocomplete="address"
            data-type="address" type="text"
            value="{{ old('address.address', Arr::get($sessionCheckoutData, 'address')) }}" required>

        @if (EcommerceHelper::isZipCodeEnabled())
            <div class="form-group mb-3 @error('address.zip_code') has-error @enderror">
                <div class="form-input-wrapper">
                    <input class="form-control" id="address_zip_code" name="address[zip_code]"
                        autocomplete="postal-code" type="text"
                        value="{{ old('address.zip_code', Arr::get($sessionCheckoutData, 'zip_code')) }}" required>
                    <label for="address_zip_code">{{ __('Zip code') }}</label>
                </div>
                {!! Form::error('address.zip_code', $errors) !!}
            </div>
        @endif
    </div>

    @if (!auth('customer')->check())
        <div id="register-an-account-wrapper">
            <div class="mb-3 form-group">
                <input id="create_account" name="create_account" type="checkbox" value="1"
                    @if (old('create_account') == 1) checked @endif>
                <label class="form-label"
                    for="create_account">{{ __('Register an account with above information?') }}</label>
            </div>

            <div class="password-group @if (!$errors->has('password') && !$errors->has('password_confirmation')) d-none @endif">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group  @error('password') has-error @enderror">
                            <div class="form-input-wrapper">
                                <input class="form-control" id="password" name="password" type="password"
                                    autocomplete="new-password">
                                <label for="password">{{ __('Password') }}</label>
                            </div>
                            {!! Form::error('password', $errors) !!}
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group @error('password_confirmation') has-error @enderror">
                            <div class="form-input-wrapper">
                                <input class="form-control" id="password-confirm" name="password_confirmation"
                                    type="password" autocomplete="password-confirmation">
                                <label for="password-confirm">{{ __('Password confirmation') }}</label>
                            </div>
                            {!! Form::error('password_confirmation', $errors) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Sau phần address_ward -->
    <div class="form-group mb-3 mt-3 @error('selected_store') has-error @enderror">
        <div class="select--arrow form-input-wrapper">
            <select class="form-control select2" id="selected_store" name="store_id"
                data-placeholder="{{ __('From Company') }}" required>
                @foreach ($stores as $store)
                    <option value="{{ $store->id }}" data-storeid="{{ $store->id }}"
                        @if ($selected_store == $store->id) selected @endif>
                        {{ $store->name }} - {{ $store->full_address }}
                    </option>
                @endforeach
                <option value="none">{{ __('From Company') }}</option>
            </select>
            <label style="z-index: 999;" for="selected_store">{{ __('Cửa hàng gần bạn') }}</label>
            <div class="store-near-you-loading" style="display: none;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Đang tải...') }}
            </div>
        </div>
        {!! Form::error('selected_store', $errors) !!}
    </div>

    {!! apply_filters('ecommerce_checkout_address_form_after', null, $sessionCheckoutData) !!}

    @php
        $productPairs = [];
        foreach ($products as $product) {
            $productPairs[] = $product->store_id . '-' . $product->name;
        }
    @endphp

    <input hidden type="text" id="products-order" name="products-order"
        value="{{ implode(', ', $productPairs) }}">

    <textarea hidden name="" id="list-store-render" cols="30" rows="10"></textarea>

</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const stateSelect = document.getElementById("address_state");
        const citySelect = document.getElementById("address_city");
        const wardSelect = document.getElementById("address_ward");
        const storeSelect = document.getElementById("selected_store");
        const loadingSpinner = document.querySelector(".store-near-you-loading");
        const selectedWard = "{{ old('address.ward', Arr::get($sessionCheckoutData, 'ward') ?? '') }}";
        const selectedCity = "{{ old('address.city', Arr::get($sessionCheckoutData, 'city') ?? '') }}";
        const selectedState = "{{ old('address.state', Arr::get($sessionCheckoutData, 'state') ?? '') }}";
        
        let productStoreId = null;
        let hasMultipleStores = false;
        let isApplyingAddressSelection = false;
        let isAutoSelectingStore = false;

        // Khởi tạo productStoreId và kiểm tra nhiều cửa hàng
        function initializeProductStoreId() {
            const storeSpans = document.querySelectorAll('span[data-storeid]');
            if (storeSpans.length) {
                const spanStoreIds = Array.from(storeSpans)
                    .map(span => parseInt(span.getAttribute('data-storeid')))
                    .filter(id => !isNaN(id) && id !== 0); // Loại bỏ storeId = 0
                const uniqueStoreIds = [...new Set(spanStoreIds)];
                if (uniqueStoreIds.length === 1) {
                    productStoreId = null; // Chỉ có một cửa hàng liên kết
                    hasMultipleStores = true;
                } else if (uniqueStoreIds.length > 1) {
                    productStoreId = null; // Nhiều cửa hàng, cho phép chọn
                    hasMultipleStores = true;
                }
            }
            console.log("Product Store ID:", productStoreId, "Has Multiple Stores:", hasMultipleStores);
        }

        // API GHN cho phường/xã
        const apiUrl = "https://online-gateway.ghn.vn/shiip/public-api/master-data/ward";
        const token = "2c2e62dc-ee72-11ef-a3aa-e2c95c1f5bee";

        // Lưu city vào localStorage
        if (citySelect) {
            localStorage.setItem('to_city', citySelect.value);
            console.log(`city ${citySelect.value}`);
        }

        document.getElementById("address_id")?.addEventListener("change", function() {
            console.log('address_id changed');

            const selectedOption = this.options[this.selectedIndex];
            const dataCode = selectedOption.getAttribute("data-code");

            sessionStorage.removeItem('selected_store');
            sessionStorage.removeItem('selected_store_manual');

            if (!dataCode) {
                fetchStores(true);
                return;
            }

            const [stateVal, cityVal, wardVal] = dataCode.split(',').map(item => item.trim());

            isApplyingAddressSelection = true;

            if (stateSelect) {
                $(stateSelect).val(stateVal || "").trigger('change.select2');
            }

            fetchCities(stateVal, cityVal)
                .then(() => fetchWards(cityVal, wardVal))
                .then(() => {
                    updateFullAddress();
                    if (cityVal) {
                        localStorage.setItem('to_city', cityVal);
                    }

                    if (wardVal) {
                        localStorage.setItem('to_wc', wardVal.split('.')[0]);
                        $(document).trigger('calculateShippingFee');
                    } else {
                        localStorage.removeItem('to_wc');
                    }
                    fetchStores(true);
                })
                .then(() => {
                    isApplyingAddressSelection = false;
                });
        });

        function fetchCities(stateId, selectedCityId) {
            if (!citySelect) {
                return Promise.resolve();
            }

            const url = citySelect.getAttribute('data-url');
            if (!url) {
                return Promise.resolve();
            }

            if (!stateId) {
                const placeholder = citySelect.options[0]?.text || "";
                $(citySelect).html(`<option value="">${placeholder}</option>`).trigger("change.select2");
                return Promise.resolve();
            }

            const countryId = document.getElementById('address_country')?.value || "";

            return new Promise((resolve) => {
                $.ajax({
                    url: url,
                    method: "GET",
                    data: {
                        state_id: stateId,
                        country_id: countryId || null,
                    },
                    headers: {
                        "Accept": "application/json",
                    },
                    success: function(response) {
                        const list = Array.isArray(response?.data) ?
                            response.data :
                            Array.isArray(response?.data?.data) ? response.data.data : [];
                        const placeholder = citySelect.options[0]?.text || "";
                        let options = `<option value="">${placeholder}</option>`;
                        list.forEach((city) => {
                            if (!city || city.id === 0) {
                                return;
                            }
                            options += `<option value="${city.id}">${city.name}</option>`;
                        });
                        $(citySelect).html(options).trigger('change.select2');
                        if (selectedCityId) {
                            $(citySelect).val(selectedCityId).trigger('change.select2');
                        }
                        resolve();
                    },
                    error: function() {
                        const placeholder = citySelect.options[0]?.text || "";
                        $(citySelect).html(`<option value="">${placeholder}</option>`).trigger("change.select2");
                        resolve();
                    }
                });
            });
        }

        function fetchWards(districtId, selectedWardCode = "") {
            return new Promise((resolve) => {
                if (!wardSelect) {
                    resolve();
                    return;
                }

                const placeholder = wardSelect.options[0]?.text || "Select ward...";
                if (!districtId) {
                    $(wardSelect).html(`<option value="">${placeholder}</option>`).trigger("change.select2");
                    resolve();
                    return;
                }

                const wardCode = selectedWardCode ? String(selectedWardCode).split('.')[0] : "";

                fetch(`${apiUrl}?district_id=${districtId}`, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "Token": token
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        let options = `<option value="">${placeholder}</option>`;
                        if (data.code === 200 && Array.isArray(data.data)) {
                            data.data.forEach(ward => {
                                const value = `${ward.WardCode}.${ward.WardName}`;
                                const isSelected = value === selectedWard ||
                                    (wardCode && String(ward.WardCode) === wardCode);
                                options +=
                                    `<option value="${value}" ${isSelected ? "selected" : ""}>${ward.WardName}</option>`;
                            });
                        } else {
                            options += `<option value="">No wards found...</option>`;
                        }
                        $(wardSelect).html(options);
                        $(wardSelect).trigger('change.select2');
                        resolve();
                    })
                    .catch(error => {
                        console.error("Failed to load wards:", error);
                        $(wardSelect).html(`<option value="">Load failed...</option>`).trigger(
                            'change.select2');
                        resolve();
                    });
            });
        }
        function fetchStores(resetSelected = false) {
            if (!storeSelect) return;
            loadingSpinner.style.display = "block";

            const productsOrderInput = document.getElementById("products-order");
            const productNames = productsOrderInput ? productsOrderInput.value : "";

            const requestData = hasMultipleStores ? {
                state: stateSelect ? stateSelect.value || "" : "",
                city: citySelect ? citySelect.value || "" : "",
                ward: wardSelect ? wardSelect.value.split('.')[0] || "" : "",
                product_names: productNames,
            } : {
                state: stateSelect ? stateSelect.value || "" : "",
                city: citySelect ? citySelect.value || "" : "",
                ward: wardSelect ? wardSelect.value.split('.')[0] || "" : "",
                product_names: productNames
            };
            console.log("Đang lấy danh sách cửa hàng với:", requestData);


            $.ajax({
                url: "{{ route('public.ajax.checkout.stores-near-you') }}",
                method: "POST",
                data: JSON.stringify(requestData),
                contentType: "application/json",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log("Stores response:", response);
                    const textarea = document.getElementById('list-store-render');
                    if (textarea) {
                        textarea.value = JSON.stringify(response, null, 4); // format đẹp
                    }
                    let options = '';
                    const manualSelected = sessionStorage.getItem('selected_store_manual') === '1';
                    const oldSelectedId = "{{ old('selected_store', $selected_store ?? '') }}";
                    let defaultSelectedId = '';
                    if (!resetSelected && manualSelected) {
                        defaultSelectedId = sessionStorage.getItem('selected_store') || oldSelectedId;
                    }
                    let firstStoreId = null;
                    let foundProductStore = false;

                    if (response.data && Array.isArray(response.data)) {
                        const storeIds = response.data.map((store) => String(store.id));
                        defaultSelectedId = defaultSelectedId ? String(defaultSelectedId) : '';
                        if (defaultSelectedId === 'none' && !manualSelected) {
                            defaultSelectedId = '';
                        }
                        const hasDefaultStore = defaultSelectedId &&
                            storeIds.includes(defaultSelectedId);
                        if (!hasDefaultStore && defaultSelectedId !== 'none') {
                            defaultSelectedId = '';
                        }
                        response.data.forEach((store, index) => {
                            const storeId = String(store.id);
                            if (index === 0) {
                                firstStoreId = store.id;
                            }
                            if (productStoreId && storeId === String(productStoreId)) {
                                foundProductStore = true;
                            }
                            const isSelected = (productStoreId && storeId ===
                                    String(productStoreId) && !hasMultipleStores) ||
                                (!productStoreId && storeId === defaultSelectedId) ?
                                "selected" : "";
                            options +=
                                `<option value="${store.id}" ${isSelected}>${store.name} - ${store.address}</option>`;
                        });

                        // ✅ Thêm "From Company" sau cùng
                        options += `<option value="none" ${manualSelected && defaultSelectedId === 'none' ? 'selected' : ''}>{{ __('From Company') }}</option>`;
                    } else {
                        options += `<option value="none" selected>{{ __('From Company') }}</option>`;
                        sessionStorage.setItem('selected_store', 'none');
                        sessionStorage.setItem('selected_store_manual', '0');
                    }

                    $(storeSelect).html(options);

                    if (productStoreId && !hasMultipleStores) {
                        if (foundProductStore) {
                            isAutoSelectingStore = true;
                            $(storeSelect).val(productStoreId).trigger('change.select2').prop(
                                'disabled', true);
                            isAutoSelectingStore = false;
                            sessionStorage.setItem('selected_store_manual', '0');
                            if (document.getElementById('selected_store_display')) {
                                document.getElementById('selected_store_display').textContent =
                                    `Cửa hàng: ${storeSelect.selectedOptions[0]?.text || ''}`;
                            }
                        } else {
                            alert(
                                "Cửa hàng liên kết với sản phẩm không khả dụng tại địa chỉ này. Vui lòng chọn địa chỉ khác hoặc liên hệ hỗ trợ."
                            );
                            $(storeSelect).html(
                                `<option value="${productStoreId}" selected>Cửa hàng không khả dụng</option>`
                            );
                            $(storeSelect).prop('disabled', true).trigger('change.select2');
                            sessionStorage.setItem('selected_store_manual', '0');
                            if (document.getElementById('selected_store_display')) {
                                document.getElementById('selected_store_display').textContent =
                                    `Cửa hàng không khả dụng, vui lòng chọn địa chỉ khác.`;
                            }
                        }
                    } else {
                        if (!defaultSelectedId && firstStoreId) {
                            isAutoSelectingStore = true;
                            $(storeSelect).val(firstStoreId).trigger('change.select2');
                            isAutoSelectingStore = false;
                            sessionStorage.setItem('selected_store', firstStoreId);
                            sessionStorage.setItem('selected_store_manual', '0');
                        }
                        $(storeSelect).prop('disabled', false);
                        if (document.getElementById('selected_store_display')) {
                            document.getElementById('selected_store_display').textContent =
                                `Vui lòng chọn một cửa hàng.`;
                        }
                    }

                    $(storeSelect).trigger('change.select2');
                },
                error: function(xhr, status, error) {
                    console.error("Lỗi khi tải cửa hàng:", xhr.responseText || error);
                    $(storeSelect).html(`<option value="">Lỗi tải dữ liệu...</option>`).trigger(
                        'change.select2');
                    if (productStoreId && !hasMultipleStores) {
                        $(storeSelect).html(
                            `<option value="${productStoreId}" selected>Cửa hàng không khả dụng</option>`
                        );
                        $(storeSelect).prop('disabled', true).trigger('change.select2');
                    }
                },
                complete: function() {
                    loadingSpinner.style.display = "none";
                }
            });
        }

        function updateFullAddress() {
            const addressDetail = $("#address_address_detail").val()?.trim() || "";
            const addressWard = $("#address_ward option:selected").text()?.trim() || "";
            const addressCity = $("#address_city option:selected").text()?.trim() || "";
            const addressState = $("#address_state option:selected").text()?.trim() || "";
            const fullAddress = [addressDetail, addressWard, addressCity, addressState].filter(Boolean).join(
                ", ");
            $("#address_address").val(fullAddress);
        }

        function updateStoreSelect() {
            const storeSelected = document.getElementById('selected_store');
            const display = document.getElementById('selected_store_display');

            if (!storeSelected) return;

            if (productStoreId && !hasMultipleStores) {
                const hasOption = Array.from(storeSelected.options).some(opt => String(opt.value) ===
                    String(productStoreId));
                if (hasOption) {
                    $(storeSelected)
                        .val(productStoreId)
                        .trigger('change')
                        .prop('disabled', true)
                        .trigger('select2:close');
                    if (display) {
                        display.textContent = `Cửa hàng: ${storeSelected.selectedOptions[0]?.text || ''}`;
                    }
                } else {
                    $(storeSelected)
                        .html(`<option value="${productStoreId}" selected>Cửa hàng không khả dụng</option>`)
                        .prop('disabled', true)
                        .trigger('change.select2');
                    if (display) {
                        display.textContent = `Cửa hàng không khả dụng, vui lòng chọn địa chỉ khác.`;
                    }
                }
            } else {
                $(storeSelected).prop('disabled', false).trigger('change');
                if (display) {
                    display.textContent = `Vui lòng chọn một cửa hàng.`;
                }
            }
        }

        function waitForStoreOptionsToLoadAndUpdate() {
            const storeSelect = document.getElementById('selected_store');
            if (!storeSelect) return;

            const observer = new MutationObserver((mutationsList, observer) => {
                const hasMoreThanOneOption = storeSelect.options.length > 1;
                if (hasMoreThanOneOption) {
                    updateStoreSelect();
                    observer.disconnect();
                }
            });

            observer.observe(storeSelect, {
                childList: true
            });
        }

        $(document).ready(function() {
            initializeProductStoreId(); // Khởi tạo productStoreId và hasMultipleStores

            $('#address_state').select2({
                placeholder: "Chọn tỉnh thành...",
                width: '100%',
                dropdownParent: $('.customer-address-payment-form')
            });

            $('#address_city').select2({
                placeholder: "Chọn quận huyện...",
                width: "100%",
                dropdownParent: $('.customer-address-payment-form')
            });

            $('#address_ward').select2({
                placeholder: "Chọn phường xã...",
                width: "100%",
                dropdownParent: $('.customer-address-payment-form')
            });

            $('#selected_store').select2({
                placeholder: "{{ __('From Company') }}",
                width: "100%",
                dropdownParent: $('.customer-address-payment-form')
            });

            if (citySelect && citySelect.value) {
                fetchWards(citySelect.value);
            }

            if (productStoreId && !hasMultipleStores) {
                // Nếu có productStoreId và không có nhiều cửa hàng, khởi tạo với cửa hàng liên kết
                $(storeSelect).html(
                    `<option value="${productStoreId}" selected>Đang tải cửa hàng...</option>`);
                $(storeSelect).prop('disabled', true).trigger('change.select2');
                fetchStores(); // Kiểm tra tính khả dụng của cửa hàng
            } else {
                fetchStores(); // Lấy tất cả cửa hàng hoặc cửa hàng gần nhất
            }

            updateStoreSelect();

            $(stateSelect).on('select2:select', function() {
                if (isApplyingAddressSelection) {
                    return;
                }
                sessionStorage.removeItem('selected_store');
                sessionStorage.removeItem('selected_store_manual');
                console.log("State thay đổi thành:", $(this).val());
                fetchWards('');
                fetchStores(true);
                updateFullAddress();
            });

            $(citySelect).on('select2:select', function() {
                if (isApplyingAddressSelection) {
                    return;
                }
                sessionStorage.removeItem('selected_store');
                sessionStorage.removeItem('selected_store_manual');
                console.log("City thay đổi thành:", $(this).val());
                fetchWards($(this).val());
                fetchStores(true);
                updateFullAddress();
                localStorage.setItem('to_city', $(this).val());
            });

            $(wardSelect).on('select2:select', function() {
                if (isApplyingAddressSelection) {
                    return;
                }
                sessionStorage.removeItem('selected_store');
                sessionStorage.removeItem('selected_store_manual');
                console.log("Ward thay đổi thành:", $(this).val());
                updateFullAddress();
                const wardValue = $(this).val();
                if (wardValue) {
                    const wardCode = wardValue.split('.')[0];
                    console.log("WardCode:", wardCode);
                    localStorage.setItem('to_wc', wardCode);
                    fetchStores(true);
                    $(document).trigger('calculateShippingFee');
                } else {
                    console.warn("Không có WardCode, không thể tính phí vận chuyển.");
                    localStorage.removeItem('to_wc');
                    fetchStores(true);
                }
            });

            $(storeSelect).on('select2:select', function() {
                if (isAutoSelectingStore) {
                    return;
                }
                console.log("Store thay đổi thành:", $(this).val());
                if (!productStoreId || hasMultipleStores) {
                    sessionStorage.setItem('selected_store', $(this).val());
                    sessionStorage.setItem('selected_store_manual', '1');
                }
            });

            $("#address_address_detail").on("input change", updateFullAddress);
            updateFullAddress();
        });

        waitForStoreOptionsToLoadAndUpdate();
    });
</script>
