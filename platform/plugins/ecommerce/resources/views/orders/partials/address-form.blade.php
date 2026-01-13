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

    {!! apply_filters('ecommerce_checkout_address_form_after', null, $sessionCheckoutData) !!}

    @if (!empty($storesForDisplay) && $storesForDisplay->count())
        <div class="form-group mt-3">
            <h6 class="mb-2">{{ __('Cửa hàng xử lý đơn') }}</h6>
            <ul class="list-unstyled mb-0">
                @foreach ($storesForDisplay as $store)
                    <li class="mb-2">
                        <strong>{{ $store['name'] ?? __('Cửa hàng') }}</strong>
                        @if (!empty($store['address']))
                            <div class="text-muted small">{{ $store['address'] }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stateSelect = document.getElementById('address_state');
        const citySelect = document.getElementById('address_city');
        const wardSelect = document.getElementById('address_ward');
        const selectedWard = "{{ old('address.ward', Arr::get($sessionCheckoutData, 'ward') ?? '') }}";
        const selectedCity = "{{ old('address.city', Arr::get($sessionCheckoutData, 'city') ?? '') }}";
        const selectedState = "{{ old('address.state', Arr::get($sessionCheckoutData, 'state') ?? '') }}";
        const apiUrl = "https://online-gateway.ghn.vn/shiip/public-api/master-data/ward";
        const token = "2c2e62dc-ee72-11ef-a3aa-e2c95c1f5bee";
        let isApplyingAddressSelection = false;

        const updateFullAddress = () => {
            const addressDetail = $("#address_address_detail").val()?.trim() || "";
            const addressWard = $("#address_ward option:selected").text()?.trim() || "";
            const addressCity = $("#address_city option:selected").text()?.trim() || "";
            const addressState = $("#address_state option:selected").text()?.trim() || "";
            const fullAddress = [addressDetail, addressWard, addressCity, addressState].filter(Boolean).join(", ");
            $("#address_address").val(fullAddress);
        };

        const fetchCities = (stateId, cityIdToSelect) => {
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
                    data: { state_id: stateId, country_id: countryId || null },
                    headers: { "Accept": "application/json" },
                    success: function(response) {
                        const list = Array.isArray(response?.data) ? response.data : Array.isArray(response?.data?.data) ? response.data.data : [];
                        const placeholder = citySelect.options[0]?.text || "";
                        let options = `<option value="">${placeholder}</option>`;
                        list.forEach((city) => {
                            if (!city || city.id === 0) {
                                return;
                            }
                            options += `<option value="${city.id}">${city.name}</option>`;
                        });
                        $(citySelect).html(options).trigger('change.select2');
                        if (cityIdToSelect) {
                            $(citySelect).val(cityIdToSelect).trigger('change.select2');
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
        };

        const fetchWards = (districtId, wardCode = "") => {
            if (!wardSelect) {
                return Promise.resolve();
            }

            return new Promise((resolve) => {
                const placeholder = wardSelect.options[0]?.text || "Select ward...";
                if (!districtId) {
                    $(wardSelect).html(`<option value="">${placeholder}</option>`).trigger("change.select2");
                    resolve();
                    return;
                }

                const codeToMatch = wardCode ? String(wardCode).split('.')[0] : "";

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
                                const isSelected = value === selectedWard || (codeToMatch && String(ward.WardCode) === codeToMatch);
                                options += `<option value="${value}" ${isSelected ? "selected" : ""}>${ward.WardName}</option>`;
                            });
                        } else {
                            options += `<option value="">No wards found...</option>`;
                        }
                        $(wardSelect).html(options).trigger('change.select2');
                        resolve();
                    })
                    .catch(error => {
                        console.error("Failed to load wards:", error);
                        $(wardSelect).html(`<option value="">Load failed...</option>`).trigger('change.select2');
                        resolve();
                    });
            });
        };

        document.getElementById('address_id')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const dataCode = selectedOption.getAttribute('data-code');

            if (!dataCode) {
                fetchWards('');
                updateFullAddress();
                return;
            }

            const [stateVal, cityVal, wardVal] = dataCode.split(',').map(item => item.trim());

            isApplyingAddressSelection = true;

            if (stateSelect) {
                $(stateSelect).val(stateVal || '').trigger('change.select2');
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
                })
                .finally(() => {
                    isApplyingAddressSelection = false;
                });
        });

        $(document).ready(function() {
            if (citySelect) {
                localStorage.setItem('to_city', citySelect.value || '');
            }

            $('#address_state').select2({
                placeholder: "Chon tinh thanh...",
                width: '100%',
                dropdownParent: $('.customer-address-payment-form')
            });

            $('#address_city').select2({
                placeholder: "Chon quan huyen...",
                width: "100%",
                dropdownParent: $('.customer-address-payment-form')
            });

            $('#address_ward').select2({
                placeholder: "Chon phuong xa...",
                width: "100%",
                dropdownParent: $('.customer-address-payment-form')
            });

            if (citySelect && citySelect.value) {
                fetchWards(citySelect.value, selectedWard);
            } else if (selectedState) {
                fetchCities(selectedState, selectedCity).then(() => fetchWards(selectedCity, selectedWard));
            }

            $(stateSelect).on('select2:select', function() {
                if (isApplyingAddressSelection) {
                    return;
                }
                const stateId = $(this).val();
                fetchCities(stateId, '');
                fetchWards('');
                updateFullAddress();
            });

            $(citySelect).on('select2:select', function() {
                if (isApplyingAddressSelection) {
                    return;
                }
                const cityId = $(this).val();
                fetchWards(cityId);
                updateFullAddress();
                localStorage.setItem('to_city', cityId || '');
            });

            $(wardSelect).on('select2:select', function() {
                if (isApplyingAddressSelection) {
                    return;
                }
                updateFullAddress();
                const wardValue = $(this).val();
                if (wardValue) {
                    const wardCode = wardValue.split('.')[0];
                    localStorage.setItem('to_wc', wardCode);
                    $(document).trigger('calculateShippingFee');
                } else {
                    localStorage.removeItem('to_wc');
                }
            });

            $("#address_address_detail").on("input change", updateFullAddress);
            updateFullAddress();
        });
    });
</script>

