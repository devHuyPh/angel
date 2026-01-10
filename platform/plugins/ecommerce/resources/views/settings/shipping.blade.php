@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core-setting::section
        :title="trans('plugins/ecommerce::setting.shipping.shipping_rule')"
        :description="trans('plugins/ecommerce::setting.shipping.shipping_rule_description')"
        class="wrapper-content"
    >
        <x-slot:extra-description>
            <x-core::button
                type="button"
                class="btn-select-country"
            >
                {{ trans('plugins/ecommerce::shipping.select_country') }}
            </x-core::button>
        </x-slot:extra-description>

        @if(! EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
            <div class="px-3">
                <x-core::alert
                    type="info">
                    {{ trans('plugins/ecommerce::shipping.shipping_based_on_location_instruction', ['link_text' => trans('plugins/ecommerce::setting.checkout.form.load_countries_states_cities_from_location_plugin') ]) }}
                </x-core::alert>
            </div>
        @endif

        @if(! EcommerceHelper::isZipCodeEnabled())
            <div class="px-3">
                <x-core::alert
                    type="info">
                    {{ trans('plugins/ecommerce::shipping.shipping_based_on_zip_code_instruction', ['link_text' => trans('plugins/ecommerce::setting.checkout.form.zip_code_enabled') ]) }}
                </x-core::alert>
            </div>
        @endif

        @if (! empty($shipping) && $shipping->isNotEmpty())
            <div class="shipping-options-wrapper">
                @foreach ($shipping as $shippingItem)
                    <div class="p-3 shipping-option-item wrap-table-shipping-{{ $shippingItem->id }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <x-core::form.label>
                                {{ trans('plugins/ecommerce::shipping.country') }}
                                <strong>{{ $countryName = EcommerceHelper::getCountryNameById($shippingItem->title) }}</strong>
                            </x-core::form.label>

                            <div class="btn-list">
                                <a
                                    href="javascript:void(0);"
                                    data-shipping-id="{{ $shippingItem->id }}"
                                    data-country="{{ $shippingItem->country}}"
                                    class="btn-add-shipping-rule-trigger"
                                >
                                    {{ trans('plugins/ecommerce::shipping.add_shipping_rule') }}
                                </a>
                                <a
                                    href="javascript:void(0);"
                                    data-id="{{ $shippingItem->id }}"
                                    data-name="{{ $countryName }}"
                                    class="btn-confirm-delete-region-item-modal-trigger text-danger"
                                >
                                    {{ trans('plugins/ecommerce::shipping.delete') }}
                                </a>
                            </div>
                        </div>
                        <div>
                            @foreach ($shippingItem->rules as $rule)
                                @include('plugins/ecommerce::shipping.rules.item', compact('rule'))
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @php
            $extraShippingOptions = apply_filters(SHIPPING_METHODS_SETTINGS_PAGE, null);
        @endphp

        @if ($extraShippingOptions)
            <div class="p-3">
                {!! apply_filters(SHIPPING_METHODS_SETTINGS_PAGE, null) !!}
            </div>
            {{--SETUP GIAO HANG NHANH --}}
            <div class="p-3">
                @php
                    $status = setting('shipping_ghn_status', 0);
                    $shopId = setting('shipping_ghn_shop_id') ?: '';
                    $clientId = setting('shipping_ghn_client_id') ?: '';
                    $token = setting('shipping_ghn_token', 1) ?: 0;
                    $sanbox = setting('shipping_ghn_sandbox', 1) ?: 0;

                    // dd($status, $shopId, $clientId, $token, $sanbox)
                @endphp
                <x-core::card>
                    <x-core::table :striped="false" :hover="false">
                        <x-core::table.body>
                            <x-core::table.body.cell class="border-end" style="width: 5%">
                                <x-core::icon name="ti ti-truck-delivery" />
                            </x-core::table.body.cell>
                            <x-core::table.body.cell style="width: 20%">
                                <img
                                    class="filter-black"
                                    src="{{ url('storage/ads/Logo-GHN-Slogan-En.jpg') }}"
                                    alt="Shippo"
                                >
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                <a href="https://sso.ghn.vn/v2/ssoLogin?app=import&returnUrl=http://khachhang.ghn.vn/sso-login?token=" target="_blank" class="fw-semibold">Giao Hang Nhanh</a>
                                {{-- <p class="mb-0">{{ trans('plugins/ghn::ghn.description') }}</p> --}}
                                <p class="mb-0">{{ trans('Đơn vị vận chuyển Việt Nam') }}</p>
                            </x-core::table.body.cell>
                            <x-core::table.body.row class="bg-white">
                                <x-core::table.body.cell colspan="3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div @class(['payment-name-label-group', 'd-none' => ! $status])>
                                                <span class="payment-note v-a-t">{{ trans('plugins/payment::payment.use') }}:</span>
                                                <label class="ws-nm inline-display method-name-label">GHN</label>
                                            </div>
                                        </div>
                
                                        <x-core::button
                                            data-bs-toggle="collapse"
                                            href="#collapse-shipping-method-ghn"
                                            aria-expanded="false"
                                            aria-controls="collapse-shipping-method-ghn"
                                        >
                                            @if ($status)
                                                {{ trans('core/base::layouts.settings') }}
                                                
                                            @else
                                                {{ trans('core/base::forms.edit') }}
                                                
                                            @endif
                                        </x-core::button>
                                    </div>
                                </x-core::table.body.cell>
                            </x-core::table.body.row>
                            <x-core::table.body.row class="collapse" id="collapse-shipping-method-ghn">
                                <x-core::table.body.cell class="border-left" colspan="3">
                                    <x-core::form :url="route('ghn.update')" method="POST">
                                    @csrf
                                    @method('PUT')
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <x-core::alert type="warning">
                                                    <x-slot:title>
                                                        {{ trans('plugins/shippo::shippo.note_0') }}
                                                    </x-slot:title>
                
                                                    <ul class="ps-3">
                                                        <li style="list-style-type: circle;">
                                                            <span>{!! BaseHelper::clean(
                                                                trans('Cần sử dụng thành phố, tiểu bang, quốc gia từ Quản trị viên -> Vị trí'),
                                                            ) !!}</span>
                                                        </li>
                                                        <li style="list-style-type: circle;">
                                                            <span>{{ trans('Đã bật "Tải quốc gia, tiểu bang, thành phố từ vị trí plugin?" và "Kích hoạt mã Zip?" để GHN hoạt động chính xác') }}</span>
                                                        </li>
                                                        <li style="list-style-type: circle;">
                                                            <span>{!! BaseHelper::clean(trans('Cập nhật thành phố, tiểu bang, quốc gia bằng dữ liệu plugin vị trí, nhập và kiểm tra địa chỉ cũng như mã zip trong <a href=\\"#\\" target=\\"_blank\\">Thông tin cơ bản</a>')) !!}</span>
                                                        </li>
                                                        @if (is_plugin_active('marketplace'))
                                                            <li style="list-style-type: circle;">
                                                                <span>{{ trans('Nhà cung cấp cần cập nhật Công ty & Mã Zip. Cập nhật thành phố, tiểu bang, quốc gia với dữ liệu plugin vị trí') }}</span>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </x-core::alert>
                
                                                <x-core::form.label>
                                                    {{ trans('Hướng dẫn cấu hình cho GHN') }}
                                                </x-core::form.label>
                
                                                <div>
                                                    <p>{{ trans('Để sử dụng Giao hàng nhanh, bạn cần') }}:</p>
                
                                                    <ol>
                                                        <li>
                                                            <p>
                                                                <a href="https://sso.ghn.vn/v2/ssoLogin?app=import&returnUrl=http://khachhang.ghn.vn/sso-login?token=" target="_blank">
                                                                    {{ trans('Đăng kí với GHN') }}
                                                                </a>
                                                            </p>
                                                        </li>
                                                        <li>
                                                            <p>{{ trans('Sau khi đăng kí sẽ có token api, client_id, shop_id') }}</p>
                                                        </li>
                                                        <li>
                                                            <p>{{ trans('Nhập các thông tin vào ô bên phải') }}</p>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="text-muted">
                                                    {{ trans('Vui lòng cung cấp thông tin ') }}
                                                    <a href="https://sso.ghn.vn/v2/ssoLogin?app=import&returnUrl=http://khachhang.ghn.vn/sso-login?token=" target="_blank">GHN</a>:
                                                </p>
                
                                                <x-core::form.text-input
                                                    name="shipping_ghn_shop_id"
                                                    :label="trans('Shop Id')"
                                                    placeholder="<Shop Id>"
                                                    :disabled="BaseHelper::hasDemoModeEnabled()"
                                                    :value="BaseHelper::hasDemoModeEnabled() ? Str::mask($shopId, '*', 10) : $shopId"
                                                />
                
                                                <x-core::form.text-input
                                                    name="shipping_ghn_client_id"
                                                    :label="trans('Client ID')"
                                                    placeholder="<Client Id>"
                                                    :disabled="BaseHelper::hasDemoModeEnabled()"
                                                    :value="BaseHelper::hasDemoModeEnabled() ? Str::mask($clientId, '*', 10) : $clientId"
                                                />

                                                <x-core::form.text-input
                                                    name="shipping_ghn_token"
                                                    :label="trans('Token')"
                                                    placeholder="<Token>"
                                                    :disabled="BaseHelper::hasDemoModeEnabled()"
                                                    :value="BaseHelper::hasDemoModeEnabled() ? Str::mask($token, '*', 10) : $token"
                                                />
                
                                                <x-core::form-group>
                                                    <x-core::form.toggle
                                                        name="shipping_ghn_sandbox"
                                                        :checked="$sanbox"
                                                        :label="trans('Chế độ test')"
                                                    />
                                                </x-core::form-group>
                
                                                <x-core::form-group>
                                                    <x-core::form.toggle
                                                        name="shipping_ghn_status"
                                                        :checked="$status"
                                                        :label="trans('Kích hoạt')"
                                                    />
                                                </x-core::form-group>
                
                                                {{-- <x-core::form-group>
                                                    <x-core::form.toggle
                                                        name="shipping_shippo_webhooks"
                                                        :checked="$webhook"
                                                        :label="trans('plugins/shippo::shippo.webhooks')"
                                                    />
                
                                                    <x-core::form.helper-text>
                                                        <a
                                                            class="text-warning fw-bold"
                                                            href="https://goshippo.com/docs/webhooks"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                        >
                                                            <span>Webhooks</span>
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                        <div>URL: <i>{{ route('shippo.webhooks', ['_token' => '__API_TOKEN__']) }}</i>
                                                        </div>
                                                    </x-core::form.helper-text>
                                                </x-core::form-group> --}}
                
                                                {{-- <x-core::form.on-off.checkbox
                                                    name="shipping_shippo_validate"
                                                    :label="trans('plugins/shippo::shippo.check_validate_token')"
                                                    :checked="setting('shipping_shippo_validate')"
                                                /> --}}
                
                                                {{-- @if (! empty($logFiles))
                                                    <div class="form-group mb-3">
                                                        <p class="mb-0">{{ __('Log files') }}: </p>
                                                        <ul class="list-unstyled">
                                                            @foreach ($logFiles as $logFile)
                                                                <li><a
                                                                        href="{{ route('ecommerce.shipments.shippo.view-log', $logFile) }}"
                                                                        target="_blank"
                                                                    ><strong>- {{ $logFile }} <i
                                                                                class="fa fa-external-link"></i></strong></a></li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif --}}
                
                                                {{-- <x-core::alert type="warning">
                                                    {{ trans('Tùy chọn GHN sẽ không được hiển thị nếu phương thức thanh toán đã chọn là COD!') }}
                                                </x-core::alert> --}}
                
                                                @env('demo')
                                                    <x-core::alert type="danger">
                                                        {{ trans('Bạn không thể cập nhật cài đặt GHN ở chế độ demo!') }}
                                                    </x-core::alert>
                                                @else
                                                    <div class="text-end">
                                                        <x-core::button type="submit" color="primary">
                                                            {{ trans('core/base::forms.update') }}
                                                        </x-core::button>
                                                    </div>
                                                @endenv
                                            </div>
                                        </div>
                                    </x-core::form>
                                </x-core::table.body.cell>
                            </x-core::table.body.row>
                        </x-core::table.body>
                    </x-core::table>
                </x-core::card>
            </div>
            {{-- EDN GHN --}}
        @elseif ($shipping->isEmpty())
            <x-core::empty-state
                :title="trans('plugins/ecommerce::shipping.empty_shipping_options.title')"
                :subtitle="trans('plugins/ecommerce::shipping.empty_shipping_options.subtitle')"
            />
        @endif

    </x-core-setting::section>

    {!! $form->renderForm() !!}
@endsection

@push('footer')
    <x-core::modal.action
        id="confirm-delete-price-item-modal"
        type="danger"
        :title="trans('plugins/ecommerce::shipping.delete_shipping_rate')"
        :description="trans('plugins/ecommerce::shipping.delete_shipping_rate_confirmation')"
        :submit-button-attrs="['id' => 'confirm-delete-price-item-button']"
        :submit-button-label="trans('plugins/ecommerce::shipping.confirm')"
    />

    <x-core::modal.action
        id="confirm-delete-region-item-modal"
        type="danger"
        :title="trans('plugins/ecommerce::shipping.delete_shipping_area')"
        :description="trans('plugins/ecommerce::shipping.delete_shipping_area_confirmation')"
        :submit-button-attrs="['id' => 'confirm-delete-region-item-button']"
        :submit-button-label="trans('plugins/ecommerce::shipping.confirm')"
    />

    <x-core::modal
        id="add-shipping-rule-item-modal"
        :title="trans('plugins/ecommerce::shipping.add_shipping_fee_for_area')"
        button-id="add-shipping-rule-item-button"
        :button-label="trans('plugins/ecommerce::shipping.save')"
    >
        @include('plugins/ecommerce::shipping.rules.form', ['rule' => null])
    </x-core::modal>

    <div data-delete-region-item-url="{{ route('shipping_methods.region.destroy') }}"></div>
    <div data-delete-rule-item-url="{{ route('shipping_methods.region.rule.destroy') }}"></div>

    <x-core::modal
        id="select-country-modal"
        :title="trans('plugins/ecommerce::shipping.add_shipping_region')"
        button-id="add-shipping-region-button"
        :button-label="trans('plugins/ecommerce::shipping.save')"
    >
        {!! Botble\Ecommerce\Forms\AddShippingRegionForm::create()->renderForm() !!}
    </x-core::modal>

    <x-core::modal
        id="form-shipping-rule-item-detail-modal"
        :title="trans('plugins/ecommerce::shipping.add_shipping_region')"
        button-id="save-shipping-rule-item-detail-button"
        :button-label="trans('plugins/ecommerce::shipping.save')"
    >
        Loading...
    </x-core::modal>

    <x-core::modal.action
        id="confirm-delete-shipping-rule-item-modal"
        :title="trans('plugins/ecommerce::shipping.rule.item.delete')"
        :description="trans('plugins/ecommerce::shipping.rule.item.confirmation')"
        :submit-button-attrs="['id' => 'confirm-delete-shipping-rule-item-button']"
        :submit-button-label="trans('plugins/ecommerce::shipping.confirm')"
    />
@endpush
