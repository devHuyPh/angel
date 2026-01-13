<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use App\Models\CustomerNotification;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Rules\EmailRule;
use Botble\Ecommerce\AdsTracking\FacebookPixel;
use Botble\Ecommerce\AdsTracking\GoogleTagManager;
use Botble\Ecommerce\Enums\DiscountTypeEnum;
use Botble\Ecommerce\Enums\OrderHistoryActionEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingCodStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Events\OrderProductCreatedEvent;
use Botble\Ecommerce\Facades\Cart;
use Botble\Ecommerce\Facades\Discount;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Forms\Fronts\CheckoutForm;
use Botble\Ecommerce\Http\Requests\ApplyCouponRequest;
use Botble\Ecommerce\Http\Requests\CheckoutRequest;
use Botble\Ecommerce\Http\Requests\SaveCheckoutInformationRequest;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount as DiscountModel;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\Shipment;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleCheckoutOrderData;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Ecommerce\Services\HandleTaxService;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Theme\Facades\Theme;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;



class PublicCheckoutController extends BaseController
{
    public function __construct()
    {
        if (class_exists(OptimizerHelper::class)) {
            OptimizerHelper::disable();
        }
    }

    protected function getWardNameFromGHN($wardCode, $districtId)
    {
        if (! $wardCode || ! $districtId) {
            return null;
        }

        $cacheKey = 'ghn_ward_name_' . $districtId . '_' . $wardCode;

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($wardCode, $districtId) {
            try {
                $response = Http::timeout(3)
                    ->retry(1, 200)
                    ->withHeaders([
                        'Token' => '2c2e62dc-ee72-11ef-a3aa-e2c95c1f5bee',
                    ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                        'district_id' => $districtId,
                    ]);
            } catch (Throwable $exception) {
                Log::warning('GHN ward lookup failed: ' . $exception->getMessage());

                return null;
            }

            if ($response->ok() && isset($response['data'])) {
                foreach ($response['data'] as $ward) {
                    if ($ward['WardCode'] == $wardCode) {
                        return $ward['WardName'];
                    }
                }
            }

            return null;
        });
    }
    public function getCheckout(
        string $token,
        Request $request,
        HandleTaxService $handleTaxService,
        HandleCheckoutOrderData $handleCheckoutOrderData,
    ) {
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        if (! EcommerceHelper::isEnabledGuestCheckout() && ! auth('customer')->check()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'));
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = Order::query()->where(['token' => $token, 'is_finished' => false])->first();

            if (! $order) {
                return $this
                    ->httpResponse()
                    ->setNextUrl(BaseHelper::getHomepageUrl());
            }
        }
        // dd($order->user);
        if (
            ! $request->session()->has('error_msg') &&
            $request->input('error') == 1 &&
            $request->input('error_type') == 'payment'
        ) {
            $message = $request->input('error_message') ?: __('Payment failed! Something wrong with your payment. Please try again.');

            $request->session()->flash('error_msg', $message);

            return redirect()->to(route('public.checkout.information', $token))->with('error_msg', $message);
        }

        $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        if (isset($sessionCheckoutData['address_id'])) {
            $sessionCheckoutData['ward_id'] = Address::query()->find($sessionCheckoutData['address_id']);
            $sessionCheckoutData['ward_id'] = $sessionCheckoutData['ward_id']->ward_id ?? '';
        }
        /**
         * @var \Illuminate\Database\Eloquent\Collection $products
         */
        $products = Cart::instance('cart')->products();
        if ($products->isEmpty()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('public.cart'));
        }

        foreach ($products as $product) {
            /**
             * @var Product $product
             */
            if ($product->isOutOfStock()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setNextUrl(route('public.cart'))
                    ->setMessage(
                        __('Product :product is out of stock!', ['product' => $product->original_product->name])
                    );
            }
        }

        if (
            EcommerceHelper::isEnabledSupportDigitalProducts()
            && ! EcommerceHelper::canCheckoutForDigitalProducts($products)
        ) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
        }

        $handleTaxService->execute($products, $sessionCheckoutData);

        $sessionCheckoutData = $this->processOrderData($token, $sessionCheckoutData, $request);

        $isShowAddressForm = EcommerceHelper::isSaveOrderShippingAddress($products);

        $checkoutOrderData = $handleCheckoutOrderData->execute(
            $request,
            $products,
            $token,
            $sessionCheckoutData
        );

        $shipping = $checkoutOrderData->shipping;
        $defaultShippingMethod = $checkoutOrderData->defaultShippingMethod;
        $defaultShippingOption = $checkoutOrderData->defaultShippingOption;
        $promotionDiscountAmount = $checkoutOrderData->promotionDiscountAmount;
        $couponDiscountAmount = $checkoutOrderData->couponDiscountAmount;
        $shippingAmount = $checkoutOrderData->shippingAmount;

        $sessionCheckoutData['marketplace'][3]['shipping_amount'] = $shippingAmount;
        $sessionCheckoutData['shipping_amount'] = $shippingAmount;

        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);
        $order = Order::query()->where('token', $token)->first();
        $user = $order->user;

        // $discountPercentageDowline = $user->getTotalRevenue->percentage ?? 0;
        session(['order_token' => $token]);
        session(['percent_' . session('order_token') => $user->getTotalRevenue->percentage ?? 0]);
        // dd($user->getTotalRevenue->percentage ?? 0);
        // dd(session('percent_'.session('order_token'))); // Đúng
        $data = compact(
            'token',
            'shipping',
            'defaultShippingMethod',
            'defaultShippingOption',
            'shippingAmount',
            'promotionDiscountAmount',
            'couponDiscountAmount',
            'sessionCheckoutData',
            'products',
            'isShowAddressForm',
            // 'discountPercentageDowline'
        );

        if (auth('customer')->check()) {
            $addresses = auth('customer')->user()->addresses;
            $isAvailableAddress = ! $addresses->isEmpty();

            session(['cus_wallet_2' => auth('customer')->user()->walet_2]);

            if (Arr::get($sessionCheckoutData, 'is_new_address')) {
                $sessionAddressId = 'new';
            } else {
                $sessionAddressId = Arr::get(
                    $sessionCheckoutData,
                    'address_id',
                    $isAvailableAddress ? $addresses->first()->id : null
                );
                if (! $sessionAddressId && $isAvailableAddress) {
                    $address = $addresses->firstWhere('is_default') ?: $addresses->first();
                    $sessionAddressId = $address->id;
                }
            }

            $data = array_merge($data, compact('addresses', 'isAvailableAddress', 'sessionAddressId'));
        }
        if ($request->has('address')) {
            $sessionCheckoutData['state'] = $request->input('address.state');
            $sessionCheckoutData['city'] = $request->input('address.city');
            $sessionCheckoutData['ward'] = $request->input('address.ward');
            session(['checkout_data' => $sessionCheckoutData]);
        }

        $storesForDisplay = collect();
        if ($products instanceof \Illuminate\Database\Eloquent\Collection) {
            $products->loadMissing('store');

            $storesForDisplay = $products
                ->map(function ($product) {
                    return [
                        'id' => $product->store_id,
                        'name' => $product->store->name ?? null,
                        'address' => $product->store->full_address ?? $product->store->address ?? null,
                    ];
                })
                ->filter(fn ($store) => $store['id'])
                ->unique('id')
                ->values();
        }

        // @phpstan-ignore-next-line
        $discountsQuery = DiscountModel::query()
            ->where('type', DiscountTypeEnum::COUPON)
            ->where('display_at_checkout', true)
            ->active()
            ->available();

        $discounts = apply_filters('ecommerce_checkout_discounts_query', $discountsQuery, $products)->get();

        $rawTotal = Cart::instance('cart')->rawTotal();
        $orderAmount = max($rawTotal - $promotionDiscountAmount - $couponDiscountAmount, 0);
        $orderAmount += (float) $shippingAmount;

        $data = [
            ...$data,
            'discounts' => $discounts,
            'rawTotal' => $rawTotal,
            'orderAmount' => $orderAmount,
            'storesForDisplay' => $storesForDisplay,
        ];

        app(GoogleTagManager::class)->beginCheckout($products->all(), $orderAmount);
        app(FacebookPixel::class)->checkout($products->all(), $orderAmount);

        // dd($data);
        $checkoutView = Theme::getThemeNamespace('views.ecommerce.orders.checkout');
        // dd($checkoutView);
        if (view()->exists($checkoutView)) {
            return view($checkoutView, $data);
        }
        return view(
            'plugins/ecommerce::orders.checkout',
            ['orderAmount' => $orderAmount, 'checkoutForm' => CheckoutForm::createFromArray($data)]
        );
    }

    public function processOrderData(
        string $token,
        array $sessionData,
        Request $request,
        bool $finished = false
    ): array {
        $addressDetail = Arr::get($sessionData, 'address_detail', '');
        $ward = Arr::get($sessionData, 'ward', '');

        // Tách wardId từ chuỗi trước dấu .
        preg_match('/^([^\.]+)\./', $ward, $matches);
        $wardId = $matches[1] ?? Arr::get($sessionData, 'ward_id', '');

        $wardName = preg_replace('/^[^\.]+\./', '', $ward);
        $fullAddress = trim($addressDetail . ', ' . $wardName, ', ');

        $sessionData['ward_id'] = $wardId;
        $sessionData['ward_name'] = $wardName;
        $sessionData['address_detail'] = $addressDetail;
        $sessionData['address'] = $fullAddress;
        //---------
        // dd($sessionData);
        if ($request->has('billing_address_same_as_shipping_address')) {
            $sessionData['billing_address_same_as_shipping_address'] = $request->boolean(
                'billing_address_same_as_shipping_address'
            );
        }

        if ($request->has('billing_address')) {
            $sessionData['billing_address'] = $request->input('billing_address');
        }

        if ($request->has('address.address_id')) {
            $sessionData['is_new_address'] = $request->input('address.address_id') == 'new';
        }

        if ($request->input('address', [])) {
            if (! isset($sessionData['created_account']) && $request->input('create_account') == 1) {
                $validator = Validator::make($request->input(), [
                    'password' => ['required', 'min:6'],
                    'password_confirmation' => ['required', 'same:password'],
                    'address.email' => ['required', new EmailRule(), Rule::unique((new Customer())->getTable(), 'email')],
                    'address.name' => ['required', 'min:3', 'max:120'],
                ]);

                if ($validator->passes()) {
                    $customerId = null;

                    try {
                        /**
                         * @var Customer $customer
                         */
                        $customer = Customer::query()->create([
                            'name' => $request->input('address.name'),
                            'email' => $request->input('address.email'),
                            'phone' => $request->input('address.phone'),
                            'password' => Hash::make($request->input('password')),
                        ]);

                        $customerId = $customer->getKey();

                        auth('customer')->loginUsingId($customer->getKey(), true);

                        event(new Registered($customer));

                        $sessionData['created_account'] = true;
                    } catch (Throwable $exception) {
                        BaseHelper::logError($exception);
                    }

                    if (! $customerId && auth('customer')->check()) {
                        $customerId = auth('customer')->id();
                    }

                    if ($customerId) {
                        $address = Address::query()
                            ->create(
                                array_merge($request->input('address'), [
                                    'customer_id' => $customerId,
                                    'is_default' => true,

                                ])
                            );

                        $request->merge(['address.address_id' => $address->getKey()]);
                        $sessionData['address_id'] = $address->getKey();
                    }
                }
            }

            if ($finished && auth('customer')->check()) {
                $customer = auth('customer')->user();
                if ($customer->addresses->count() == 0 || $request->input('address.address_id') == 'new') {
                    $address = Address::query()
                        ->create(
                            array_merge($request->input('address', []), [
                                'customer_id' => auth('customer')->id(),
                                'is_default' => $customer->addresses->count() == 0,
                                'ward_id' => $wardId ?? '',
                                'ward_name' => $wardName ?? '',
                                'address_detail' => $addressDetail ?? '',
                                'address' => $fullAddress ?? '',
                            ])
                        );

                    $request->merge(['address.address_id' => $address->id]);
                    $sessionData['address_id'] = $address->id;
                }
            }
        }

        $address = null;

        if (($addressId = $request->input('address.address_id')) && $addressId !== 'new') {
            $address = Address::query()->find($addressId);

            if ($address) {
                $sessionData['address_id'] = $address->getKey();
                $sessionData['ward_id'] = $address->ward_id;
                $sessionData['ward_name'] = $address->ward_name;
                $sessionData['address_detail'] = $address->address_detail;
                $sessionData['address'] = $address->address;
            }
        } elseif (auth('customer')->check() && ! Arr::get($sessionData, 'address_id')) {
            $address = Address::query()->where([
                'customer_id' => auth('customer')->id(),
                'is_default' => true,
            ])->first();

            if ($address) {
                $sessionData['address_id'] = $address->id;
                $sessionData['ward_id'] = $address->ward_id;
                $sessionData['ward_name'] = $address->ward_name;
                $sessionData['address_detail'] = $address->address_detail;
                $sessionData['address'] = $address->address;
            }
        }

        $addressData = [
            'billing_address_same_as_shipping_address' => Arr::get(
                $sessionData,
                'billing_address_same_as_shipping_address',
                true
            ),
            'billing_address' => Arr::get($sessionData, 'billing_address', []),
        ];

        if (!empty($address)) {
            $addressData = [
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'country' => $address->country,
                'state' => $address->state,
                'city' => $address->city,
                'ward_id' => $wardId,
                'ward_name' => $wardName,
                'address_detail' => $addressDetail,
                'address' => $fullAddress,
                'zip_code' => $address->zip_code,
                'address_id' => $address->id,
            ];
        } elseif ($addressFromInput = (array) $request->input('address', [])) {
            $addressFromInput['address'] = $fullAddress;
            $addressData = $addressFromInput;
        }

        $addressData = OrderHelper::cleanData($addressData);
        $sessionData = array_merge($sessionData, $addressData);
        // dd($sessionData);
        Cart::instance('cart')->refresh();
        $products = Cart::instance('cart')->products();

        if (is_plugin_active('marketplace')) {
            $sessionData = apply_filters(
                HANDLE_PROCESS_ORDER_DATA_ECOMMERCE,
                $products,
                $token,
                $sessionData,
                $request
            );

            OrderHelper::setOrderSessionData($token, $sessionData);

            return $sessionData;
        }
        if (! isset($sessionData['created_order'])) {
            $currentUserId = 0;
            if (auth('customer')->check()) {
                $currentUserId = auth('customer')->id();
            }

            $request->merge([
                'amount' => Cart::instance('cart')->rawTotal(),
                'user_id' => $currentUserId,
                'shipping_method' => $request->input('shipping_method', ShippingMethodEnum::DEFAULT),
                'shipping_option' => $request->input('shipping_option'),
                'shipping_amount' => Arr::get($sessionData, 'shipping_amount', 0),
                'tax_amount' => Cart::instance('cart')->rawTax(),
                'sub_total' => Cart::instance('cart')->rawSubTotal(),
                'coupon_code' => session('applied_coupon_code'),
                'discount_amount' => 0,
                'status' => OrderStatusEnum::PENDING,
                'is_finished' => false,
                'token' => $token,
            ]);

            /**
             * @var Order $order
             */
            $order = Order::query()->where(compact('token'))->first();

            $order = $this->createOrderFromData($request->input(), $order);
            // dd($order->user);
            $sessionData['user'] = $order->user;

            $sessionData['created_order'] = true;
            $sessionData['created_order_id'] = $order->getKey();
        }


        if (! empty($address)) {
            $addressData['order_id'] = $sessionData['created_order_id'];
        } elseif ((array) $request->input('address', [])) {
            $addressData = array_merge(
                ['order_id' => $sessionData['created_order_id']],
                (array) $request->input('address', [])
            );
        }

        $sessionData['is_save_order_shipping_address'] = EcommerceHelper::isSaveOrderShippingAddress($products);

        $sessionData = OrderHelper::checkAndCreateOrderAddress($addressData, $sessionData);


        if (! isset($sessionData['created_order_product'])) {
            $weight = Cart::instance('cart')->weight();

            OrderProduct::query()->where(['order_id' => $sessionData['created_order_id']])->delete();

            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = Product::query()->find($cartItem->id);

                if (! $product) {
                    continue;
                }

                $data = [
                    'order_id' => $sessionData['created_order_id'],
                    'product_id' => $cartItem->id,
                    'product_name' => $cartItem->name,
                    'product_image' => $cartItem->options['image'],
                    'qty' => $cartItem->qty,
                    'weight' => $weight,
                    'price' => $cartItem->price,
                    'tax_amount' => $cartItem->tax,
                    'options' => $cartItem->options,
                    'product_type' => $product->product_type,
                ];

                if (isset($cartItem->options['options'])) {
                    $data['product_options'] = $cartItem->options['options'];
                }

                OrderProduct::query()->create($data);
            }

            $sessionData['created_order_product'] = Cart::instance('cart')->getLastUpdatedAt();
        }
        OrderHelper::setOrderSessionData($token, $sessionData);

        return $sessionData;
    }

    public function postSaveInformation(
        string $token,
        SaveCheckoutInformationRequest $request,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService
    ) {
        // dd($request->get('shipping_amount_inp'));
        $products = Cart::instance('cart')->products();
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        if ($token !== session('tracked_start_checkout')) {
            $order = Order::query()->where(['token' => $token, 'is_finished' => false])->first();

            if (! $order) {
                return $this
                    ->httpResponse()
                    ->setNextUrl(BaseHelper::getHomepageUrl());
            }
        }

        if ($paymentMethod = $request->input('payment_method')) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        if (is_plugin_active('marketplace')) {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));

            $sessionData = apply_filters(
                PROCESS_POST_SAVE_INFORMATION_CHECKOUT_ECOMMERCE,
                $sessionData,
                $request,
                $token
            );
            foreach ($sessionData['marketplace'] as $storeData) {
                if (! empty($storeData['created_order_id'])) {
                    $order = Order::query()
                        ->where('id', $storeData['created_order_id'])
                        ->first();

                    if ($order && $order->shipping_amount != Arr::get($storeData, 'shipping_amount', 0)) {
                        $order->update(['shipping_amount' => Arr::get($storeData, 'shipping_amount', 0)]);
                    }
                }
            }
        } else {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));
            OrderHelper::setOrderSessionData($token, $sessionData);
            if (session()->has('applied_coupon_code')) {
                $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
                if (! $discount) {
                    $removeCouponService->execute();
                }
            }

            if (! empty($sessionData['created_order_id'])) {
                $order = Order::query()
                    ->where('id', $sessionData['created_order_id'])
                    ->first();

                if ($order) {

                    $sessionShippingAmount = Arr::get($sessionData, 'shipping_amount', 0);
                    if ($sessionShippingAmount == 0) {
                        $sessionData['shipping_amount'] = $sessionShippingAmount;
                        OrderHelper::setOrderSessionData($token, $sessionData);
                    }

                    // Cập nhật lại phí vận chuyển
                    if ($order->shipping_amount != $sessionShippingAmount) {
                        $order->update(['shipping_amount' => $sessionShippingAmount]);
                    }
                }
            }
        }

        $sessionData = $this->processOrderData($token, $sessionData, $request);
        // dd($sessionData);
        return $this
            ->httpResponse()
            ->setData($sessionData);
    }

    public function postCheckout(
        string $token,
        CheckoutRequest $request,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $handleApplyPromotionsService
    ) {
        abort_unless(EcommerceHelper::isCartEnabled(), 404);
        // dd('chưa');
        if (! EcommerceHelper::isEnabledGuestCheckout() && ! auth('customer')->check()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'));
        }
        // dd($request->all());
        if ($request->address['address_id'] != 'new') {
            $address = Address::find($request->address['address_id']);

            if ($address) {
                $fieldsToCheck = ['state', 'city', 'address', 'ward_id', 'ward_name', 'address_detail'];
                foreach ($fieldsToCheck as $field) {
                    if (empty($address->$field)) {
                        return $this
                            ->httpResponse()
                            ->setError()
                            ->setMessage(trans('core/base::layouts.required_info_adress') . ' ' .
                                trans('core/base::layouts.' . $field) . ' ' .
                                trans('core/base::layouts.null'));
                    }
                }
            } else {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage('Địa chỉ không tồn tại');
            }
        } else {
            $request->validate([
                'address.state' => ['required', 'exists:states,id'],
                'address.city' => ['required', 'exists:cities,id'],
                'address.ward' => ['required'],
                'address.address_detail' => ['required'],
            ], [
                'address.state.required' => trans('core/base::layouts.pls-select-state'),
                'address.state.exists'   => trans('core/base::layouts.invalid-state'),

                'address.city.required'  => trans('core/base::layouts.pls-select-city'),
                'address.city.exists'    => trans('core/base::layouts.invalid-city'),

                'address.ward.required'  => trans('core/base::layouts.pls-select-ward'),
                'address.address_detail.required' => trans('core/base::layouts.pls-enter-address-detail'),
            ]);
        }
        session()->forget('store_id');

        if (Cart::instance('cart')->isEmpty()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('No products in cart'));
        }

        $products = Cart::instance('cart')->products();

        if (
            EcommerceHelper::isEnabledSupportDigitalProducts() &&
            ! EcommerceHelper::canCheckoutForDigitalProducts($products)
        ) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
        }

        $totalQuality = Cart::instance('cart')->rawTotalQuantity();

        if (($minimumQuantity = EcommerceHelper::getMinimumOrderQuantity()) > 0
            && $totalQuality < $minimumQuantity
        ) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __('Minimum order quantity is :qty, you need to buy more :more to place an order!', [
                        'qty' => $totalQuality,
                        'more' => $minimumQuantity - $totalQuality,
                    ])
                );
        }

        if (
            ($maximumQuantity = EcommerceHelper::getMaximumOrderQuantity()) > 0
            && $totalQuality > $maximumQuantity
        ) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __('Maximum order quantity is :qty, please check your cart and retry again!', [
                        'qty' => $maximumQuantity,
                    ])
                );
        }

        if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(
                    __('Minimum order amount is :amount, you need to buy more :more to place an order!', [
                        'amount' => format_price(EcommerceHelper::getMinimumOrderAmount()),
                        'more' => format_price(
                            EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')->rawSubTotal()
                        ),
                    ])
                );
        }

        $sessionData = OrderHelper::getOrderSessionData($token);

        $sessionData = $this->processOrderData($token, $sessionData, $request, true);
        // dd($sessionData);

        foreach ($products as $product) {
            if ($product->isOutOfStock()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        __('Product :product is out of stock!', ['product' => $product->original_product->name])
                    );
            }

            $quantityOfProduct = Cart::instance('cart')->rawQuantityByItemId($product->id);

            if ($product->minimum_order_quantity > 0 && $quantityOfProduct < $product->minimum_order_quantity) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        __('Minimum order quantity of product :product is :quantity, you need to buy more :more to place an order! ', [
                            'product' => BaseHelper::clean($product->original_product->name),
                            'quantity' => $product->minimum_order_quantity,
                            'more' => $product->minimum_order_quantity - $quantityOfProduct,
                        ])
                    );
            }

            if ($product->maximum_order_quantity > 0 && $quantityOfProduct > $product->maximum_order_quantity) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(
                        __('Maximum order quantity of product :product is :quantity! ', [
                            'product' => $product->original_product->name,
                            'quantity' => $product->maximum_order_quantity,
                        ])
                    );
            }
        }

        $paymentMethod = $request->input('payment_method', session('selected_payment_method'));

        if ($paymentMethod) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        try {
            do_action('ecommerce_post_checkout', $products, $request, $token, $sessionData);
        } catch (Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage());
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(
                HANDLE_PROCESS_POST_CHECKOUT_ORDER_DATA_ECOMMERCE,
                $products,
                $request,
                $token,
                $sessionData,
                $this->httpResponse()
            );
        }

        $promotionDiscountAmount = $handleApplyPromotionsService->execute($token);
        $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount');
        $rawTotal = Cart::instance('cart')->rawTotal();
        $orderAmount = max($rawTotal - $promotionDiscountAmount - $couponDiscountAmount, 0);

        $isAvailableShipping = EcommerceHelper::isAvailableShipping($products);
        $shippingMethodInput = $request->input('shipping_method', ShippingMethodEnum::DEFAULT);

        $shippingData = [];
        if ($isAvailableShipping) {
            $origin = EcommerceHelper::getOriginAddress();

            $shippingData = EcommerceHelper::getShippingData(
                $products,
                array_merge($sessionData, ['session_shipping_amount' => Arr::get($sessionData, 'shipping_amount')]),
                $origin,
                $orderAmount,
                $paymentMethod
            );

            $shippingMethodData = $shippingFeeService->execute(
                $shippingData,
                $shippingMethodInput,
                $request->input('shipping_option')
            );

            $shippingMethod = Arr::first($shippingMethodData);
            if (! $shippingMethod) {
                throw ValidationException::withMessages([
                    'shipping_method' => trans(
                        'validation.exists',
                        ['attribute' => trans('plugins/ecommerce::shipping.shipping_method')]
                    ),
                ]);
            }

            if (get_shipping_setting('free_ship', $shippingMethodInput)) {
                $shippingAmount = 0;
            }
        }

        if (session()->has('applied_coupon_code')) {
            $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
            if (empty($discount)) {
                $removeCouponService->execute();
            } else {
                if (!isset($sessionData['shipping_amount']) || $sessionData['shipping_amount'] == 0) {
                    $shippingAmount = Arr::get($sessionData, 'is_free_shipping') ? 0 : $shippingAmount;
                }
            }
            // dd($sessionData);
        }

        $currentUserId = 0;
        if (auth('customer')->check()) {
            $currentUserId = auth('customer')->id();
        }
        $orderAmount += (float) $shippingAmount;

        $request->merge([
            'amount' => $orderAmount ?: 0,
            'currency' => $request->input('currency', strtoupper(get_application_currency()->title)),
            'user_id' => $currentUserId,
            'shipping_method' => $isAvailableShipping ? $shippingMethodInput : '',
            'shipping_option' => $isAvailableShipping ? $request->input('shipping_option') : null,
            'shipping_amount' => (float) $shippingAmount,
            'tax_amount' => Cart::instance('cart')->rawTax(),
            'sub_total' => Cart::instance('cart')->rawSubTotal(),
            'coupon_code' => session('applied_coupon_code'),
            'discount_amount' => $promotionDiscountAmount + $couponDiscountAmount,
            'status' => OrderStatusEnum::PENDING,
            'token' => $token,
        ]);

        /**
         * @var Order $order
         */
        $order = Order::query()->where(compact('token'))->first();
        $order = $this->createOrderFromData($request->input(), $order);

        OrderHistory::query()->create([
            'action' => OrderHistoryActionEnum::CREATE_ORDER_FROM_PAYMENT_PAGE,
            'description' => __('Order was created from checkout page'),
            'order_id' => $order->getKey(),
        ]);

        if ($isAvailableShipping) {
            Shipment::query()->create([
                'order_id' => $order->getKey(),
                'user_id' => 0,
                'weight' => $shippingData ? Arr::get($shippingData, 'weight') : 0,
                'cod_amount' => (is_plugin_active(
                    'payment'
                ) && $order->payment->id && $order->payment->status != PaymentStatusEnum::COMPLETED) ? $order->amount : 0,
                'cod_status' => ShippingCodStatusEnum::PENDING,
                'type' => $order->shipping_method,
                'status' => ShippingStatusEnum::PENDING,
                'price' => $order->shipping_amount,
                'rate_id' => $shippingData ? Arr::get($shippingMethod, 'id', '') : '',
                'shipment_id' => $shippingData ? Arr::get($shippingMethod, 'shipment_id', '') : '',
                'shipping_company_name' => $shippingData ? Arr::get($shippingMethod, 'company_name', '') : '',
            ]);
        }

        if (
            EcommerceHelper::isDisplayTaxFieldsAtCheckoutPage() &&
            $request->boolean('with_tax_information')
        ) {
            $order->taxInformation()->create($request->input('tax_information'));
        }

        if ($appliedCouponCode = session('applied_coupon_code')) {
            Discount::getFacadeRoot()->afterOrderPlaced($appliedCouponCode);
        }

        OrderProduct::query()->where(['order_id' => $order->getKey()])->delete();

        foreach (Cart::instance('cart')->content() as $cartItem) {
            $product = Product::query()->find($cartItem->id);

            if (! $product) {
                continue;
            }

            $data = [
                'order_id' => $order->getKey(),
                'product_id' => $cartItem->id,
                'product_name' => $cartItem->name,
                'product_image' => $cartItem->options['image'],
                'qty' => $cartItem->qty,
                'weight' => Arr::get($cartItem->options, 'weight', 0),
                'price' => $cartItem->price,
                'tax_amount' => $cartItem->tax,
                'options' => $cartItem->options,
                'product_type' => $product->product_type,
            ];

            if (isset($cartItem->options['options'])) {
                $data['product_options'] = $cartItem->options['options'];
            }

            /**
             * @var OrderProduct $orderProduct
             */
            $orderProduct = OrderProduct::query()->create($data);

            OrderProductCreatedEvent::dispatch($orderProduct);
            do_action('ecommerce_after_each_order_product_created', $orderProduct);
        }

        $request->merge([
            'order_id' => $order->getKey(),
        ]);

        do_action('ecommerce_before_processing_payment', $products, $request, $token, $sessionData);

        $walletMethod = defined('WALLET_PAYMENT_METHOD_NAME') ? constant('WALLET_PAYMENT_METHOD_NAME') : 'wallet_1';

        if ($request->input('payment_method') === $walletMethod) {
            if (! auth('customer')->check()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(__('Vui lòng đăng nhập để thanh toán bằng ví.'));
            }

            $customer = Customer::query()->find(auth('customer')->id());

            if (! $customer) {
                Log::error('Wallet checkout: customer not found', ['customer_id' => auth('customer')->id()]);

                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(__('Không tìm thấy thông tin khách hàng.'));
            }

            $walletPassword = (string) $request->input('wallet_password');

            if (! $walletPassword || ! Hash::check($walletPassword, $customer->password)) {
                Log::warning('Wallet checkout: invalid password', ['customer_id' => $customer->getKey()]);

                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(__('Mật khẩu ví không đúng, vui lòng thử lại.'));
            }

            $chargeId = Str::upper(Str::random(12));
            $walletUsed = 0;
            $remaining = 0;
            $walletBalanceAfter = (float) $customer->walet_1;

            try {
                Log::info('Wallet checkout: start', [
                    'customer_id' => $customer->getKey(),
                    'order_id' => $order->id,
                    'order_amount' => $order->amount,
                    'wallet_balance' => (float) $customer->walet_1,
                ]);
                DB::transaction(function () use (
                    $order,
                    $customer,
                    $chargeId,
                    &$walletUsed,
                    &$remaining,
                    &$walletBalanceAfter
                ): void {
                    $locked = Customer::query()->lockForUpdate()->find($customer->getKey());

                    $currentWallet = max((float) $locked->walet_1, 0);
                    $walletUsed = min($currentWallet, (float) $order->amount);

                    if ($walletUsed <= 0) {
                        $message = __('Số dư ví không đủ. Ví hiện có :wallet, cần :amount. Vui lòng nạp thêm hoặc chọn chuyển khoản.', [
                            'wallet' => format_price($currentWallet),
                            'amount' => format_price($order->amount),
                        ]);

                        throw new \RuntimeException($message);
                    }

                    $locked->walet_1 = (float) $locked->walet_1 - $walletUsed;
                    $locked->save();

                    $remaining = max((float) $order->amount - $walletUsed, 0);
                    $isPartial = $remaining > 0;
                    $walletBalanceAfter = (float) $locked->walet_1;

                    $metadata = [
                        'wallet_payment' => true,
                        'wallet_used' => $walletUsed,
                        'wallet_balance_after' => $walletBalanceAfter,
                        'remaining_amount' => $remaining,
                    ];

                    do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                        'amount' => $order->amount,
                        'currency' => strtoupper(get_application_currency()->title),
                        'charge_id' => $chargeId,
                        'order_id' => $order->id,
                        'customer_id' => $customer->getKey(),
                        'customer_type' => Customer::class,
                        'payment_channel' => PaymentMethodEnum::BANK_TRANSFER,
                        'status' => $isPartial ? PaymentStatusEnum::PENDING : PaymentStatusEnum::COMPLETED,
                        'metadata' => $metadata,
                        'wallet_payment' => [
                            'allocations' => [
                                $order->id => [
                                    'wallet_used' => $walletUsed,
                                    'remaining' => $remaining,
                                ],
                            ],
                            'wallet_used' => $walletUsed,
                            'remaining' => $remaining,
                        ],
                    ]);

                    Log::warning('Wallet checkout success', [
                        'order_id' => $order->id,
                        'wallet_used' => $walletUsed,
                        'remaining' => $remaining,
                        'wallet_balance_after' => $walletBalanceAfter,
                        'charge_id' => $chargeId,
                    ]);
                });
            } catch (Throwable $exception) {
                Log::error('Wallet checkout error', [
                    'order_id' => $order->id,
                    'customer_id' => $customer->getKey(),
                    'message' => $exception->getMessage(),
                    'wallet_balance' => (float) $customer->walet_1,
                    'order_amount' => (float) $order->amount,
                ]);

                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($exception->getMessage() ?: __('Lỗi thanh toán bằng ví. Vui lòng thử lại hoặc chọn chuyển khoản.'));
            }

            $successMessage = $remaining > 0
                ? __('Ví đã trừ :wallet, vui lòng chuyển khoản thêm :remaining để hoàn tất đơn hàng.', [
                    'wallet' => format_price($walletUsed),
                    'remaining' => format_price($remaining),
                ])
                : __('Đã thanh toán toàn bộ bằng ví.');

            return $this
                ->httpResponse()
                ->setNextUrl(PaymentHelper::getRedirectURL($token))
                ->setMessage($successMessage);
        }

        if (! is_plugin_active('payment') || ! $orderAmount) {
            OrderHelper::processOrder($order->getKey());

            return redirect()->to(route('public.checkout.success', OrderHelper::getOrderSessionToken()));
        }

        $paymentData = [
            'error' => false,
            'message' => false,
            'amount' => (float) format_price($order->amount, null, true),
            'currency' => strtoupper(get_application_currency()->title),
            'type' => $request->input('payment_method'),
            'charge_id' => null,
        ];

        $paymentData = apply_filters(FILTER_ECOMMERCE_PROCESS_PAYMENT, $paymentData, $request);

        \Log::warning('Checkout payment data', [
            'payment_method' => $request->input('payment_method'),
            'order_id' => $order->getKey(),
            'amount' => $order->amount,
            'paymentData' => $paymentData,
        ]);

        if ($checkoutUrl = Arr::get($paymentData, 'checkoutUrl')) {
            return $this
                ->httpResponse()
                ->setError($paymentData['error'])
                ->setNextUrl($checkoutUrl)
                ->setData(['checkoutUrl' => $checkoutUrl])
                ->withInput()
                ->setMessage($paymentData['message']);
        }

        if ($paymentData['error'] || ! $paymentData['charge_id']) {
            \Log::error('Checkout payment failed', [
                'paymentData' => $paymentData,
                'request_payment_method' => $request->input('payment_method'),
                'order_id' => $order->getKey(),
                'order_amount' => $order->amount,
                'metadata' => Arr::get($paymentData, 'metadata'),
            ]);

            $cancelUrl = PaymentHelper::getCancelURL($token);
            $walletMethod = defined('WALLET_PAYMENT_METHOD_NAME') ? constant('WALLET_PAYMENT_METHOD_NAME') : 'wallet_1';
            $fallbackMessage = $request->input('payment_method') === $walletMethod
                ? __('Thanh toán ví thất bại. Vui lòng kiểm tra mật khẩu/số dư hoặc chọn chuyển khoản cho phần còn lại.')
                : __('Thanh toán thất bại. Vui lòng thử lại hoặc chọn phương thức khác.');
            $errorMessage = $paymentData['message'] ?: $fallbackMessage;

            if ($errorMessage) {
                $cancelUrl .= (str_contains($cancelUrl, '?') ? '&' : '?') . 'error_message=' . urlencode($errorMessage);
            }

            \Log::warning('Checkout payment redirect with error', [
                'payment_method' => $request->input('payment_method'),
                'error_message' => $errorMessage,
                'cancel_url' => $cancelUrl,
            ]);

            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl($cancelUrl)
                ->withInput()
                ->setMessage($errorMessage);
        }

        $shippingAmountFromRequest = $request->get('shipping_amount_inp');
        $totalAmountFromRequest = $request->get('total_amount_ipn');

        $updates = [];

        if ($shippingAmountFromRequest !== null && $shippingAmountFromRequest !== '') {
            $updates['shipping_amount'] = $shippingAmountFromRequest;
        }

        if ($totalAmountFromRequest !== null && $totalAmountFromRequest !== '') {
            $updates['amount'] = $totalAmountFromRequest;
        }

        if ($updates) {
            Order::query()->where(compact('token'))->update($updates);
        }


        return $this
            ->httpResponse()
            ->setNextUrl(PaymentHelper::getRedirectURL($token))
            ->setMessage(__('Checkout successfully!'));
    }


    public function getCheckoutSuccess(string $token)
    {
        abort_unless(EcommerceHelper::isCartEnabled(), 404);
        // if (session()->has('amount') && session()->has('shipping_amount')) {
        //     $order_update = Order::query()->where('token', $token)->get();
        //     if ($order_update->isEmpty()) {
        //         return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
        //     }

        //     $amount = session('amount', 0);
        //     $shipping_amount = session('shipping_amount', 0);
        //     $discount_amount = session('discount_amount', 0);
        //     // dd(session('shipping_amount'));
        //     $updatedRows = Order::query()->where('token', $token)->update([
        //         'shipping_amount' => (float) $shipping_amount,
        //         'discount_amount' => DB::raw('discount_amount + ' . (float) $discount_amount),
        //     ]);

        //     // dd($updatedRows);

        //     $updatedOrders = Order::query()->where('token', $token)->get();

        //     $updatedOrders->each(function ($order) {
        //         $order->update([
        //             'amount' => ($order->sub_total + $order->shipping_amount + $order->tax_amount - $order->discount_amount)
        //         ]);
        //     });
        //     // dd($updatedOrders);

        //     $shipments_update = Shipment::query()
        //         ->orderByDesc('created_at') // Sắp xếp theo ID giảm dần (hoặc dùng created_at nếu phù hợp hơn)
        //         ->limit($updatedRows)
        //         ->get();

        //     $payment_update = Payment::query()
        //         ->orderByDesc('created_at') // Sắp xếp theo ID giảm dần (hoặc dùng created_at nếu phù hợp hơn)
        //         ->limit($updatedRows)
        //         ->get();

        //     $shipments_update->each(function ($shipment) use ($shipping_amount) {
        //         $shipment->update([
        //             'price' => (float) $shipping_amount
        //         ]);
        //     });

        //     $payment_update->each(function ($payment) use ($shipping_amount, $discount_amount) {
        //         $payment->update([
        //             'amount' => ($payment->amount + (float) $shipping_amount - $discount_amount)
        //         ]);
        //     });

        //     // dd($shipments_update);

        // }
        /**
         * @var Order $order
         */


        $orders = Order::query()
            ->where('token', $token)
            ->with(['address', 'products', 'taxInformation'])
            ->latest('id')
            ->get();

        abort_if($orders->isEmpty(), 404);

        $order = $orders
            ->where('is_finished', true)
            ->first() ?? $orders->first();

        // $newNotification = new CustomerNotification();
        $order->refresh();
        $orderAmount = (float) $order->amount;
        // dd($orderAmount);
        // if ($newStatus == 'delivered' && $order_confirm == 1 && $order_status == 'processing' ) {
        // if ($newStatus == 'delivered' && $order_status == 'processing' ) {
        $user = $order->user;

        // $user->update([
        //     'walet_2' => 0
        // ]);

        if (!empty($user->getAttributes())) {

            $commission = (float) setting('direct-referral-commission') * $orderAmount / 100; // 10%
            $referrer = $user->referrer;
            $level = 1;

            while ($referrer) {
                // CustomerNotification::create([
                //     'title' => 'Bạn sẽ nhận được % hoa hồng từ ' . $user->name,
                //     'dessription' => 'F' . $level . ' của bạn ' . $user->name . ' đã thực hiện mua đơn hàng trị giá ' . format_price($orderAmount) . '. Hoa hồng dự kiến nhận được là: ' . format_price($commission),
                //     'customer_id' => $referrer->id,
                //     'url' => '/bitsgold/dashboard'
                // ]);

                CustomerNotification::create([
                    'title' => 'core/base::layouts.you_will_comission',
                    'dessription' => 'referrer_checkout_success_notification',
                    'variables' => json_encode([
                        'amount' => $commission,
                        'text_level' => $level,
                    ]),
                    'customer_id' => $referrer->id,
                    'url' => '/marketing/dashboard'
                ]);
                $commission = $commission * (float) setting('indirect-referral-commission') / 100; // 50%
                $referrer = $referrer->referrer;
                $level++;
            }
        }
        session()->forget(['shipping_amount', 'amount', 'discount_amount', 'cus_wallet_2', 'store_id']);
        session()->forget(['percent_' . session('order_token'), 'order_token']);

        if (session('tracked_start_checkout')) {
            app(GoogleTagManager::class)->purchase($order);
            app(FacebookPixel::class)->purchase($order);
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_SUCCESS_IN_ORDER, $token, $this->httpResponse());
        }

        $products = $order->getOrderProducts();

        OrderHelper::clearSessions($token);

        return view('plugins/ecommerce::orders.thank-you', [
            'order' => $order,
            'orders' => $orders,
            'products' => $products,
        ]);
    }


    public function deductProductQuantityFromOrderToken(string $token)
    {
        DB::beginTransaction();

        try {
            $orderUpdate = Order::where('token', $token)->firstOrFail();
            // dd($orderUpdate->products[1]->product);
            foreach ($orderUpdate->products as $orderProduct) {
                $product = $orderProduct->product;

                if (!$product) {
                    $orderUpdate->delete();

                    return $this
                        ->httpResponse()
                        ->setError()
                        ->setNextUrl(route('public.cart'))
                        ->setMessage(__('Sản phẩm của kho đã được bán hết hoặc ít hơn số lượng trước khi bạn đặt, vui lòng đặt lại đơn mới'));
                }

                $currentQty = $product->quantity;
                $deductQty = $orderProduct->qty;

                if ($currentQty < $deductQty) {
                    $orderUpdate->delete();

                    return $this
                        ->httpResponse()
                        ->setError()
                        ->setNextUrl(route('public.cart'))
                        ->setMessage(__('Sản phẩm của kho đã được bán hết hoặc ít hơn số lượng trước khi bạn đặt, vui lòng đặt lại đơn mới'));
                }

                $product->update([
                    'quantity' => $currentQty - $deductQty,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            // Ghi log nếu cần
            Log::error("Failed to deduct product quantity for order token {$token}: " . $e->getMessage());

            // Tuỳ trường hợp, có thể throw lại để controller xử lý
            throw $e;
        }
    }


    public function postApplyCoupon(ApplyCouponRequest $request, HandleApplyCouponService $handleApplyCouponService)
    {
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        $result = [
            'error' => false,
            'message' => '',
        ];

        if (is_plugin_active('marketplace')) {
            $result = apply_filters(HANDLE_POST_APPLY_COUPON_CODE_ECOMMERCE, $result, $request);
        } else {
            $result = $handleApplyCouponService->execute($request->input('coupon_code'));
        }

        if ($result['error']) {
            return $this
                ->httpResponse()
                ->setError()
                ->withInput()
                ->setMessage($result['message']);
        }

        $couponCode = $request->input('coupon_code');

        return $this
            ->httpResponse()
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function postRemoveCoupon(Request $request, HandleRemoveCouponService $removeCouponService)
    {
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        if (is_plugin_active('marketplace')) {
            $products = Cart::instance('cart')->products();
            $result = apply_filters(HANDLE_POST_REMOVE_COUPON_CODE_ECOMMERCE, $products, $request);
        } else {
            $result = $removeCouponService->execute();
        }

        if ($result['error']) {
            if ($request->ajax()) {
                return $result;
            }

            return $this
                ->httpResponse()
                ->setError()
                ->setData($result)
                ->setMessage($result['message']);
        }

        return $this
            ->httpResponse()
            ->setMessage(__('Removed coupon :code successfully!', ['code' => session('applied_coupon_code')]));
    }

    public function getCheckoutRecover(string $token, Request $request)
    {

        // dd('Abcdsđ');
        abort_unless(EcommerceHelper::isCartEnabled(), 404);

        if (! EcommerceHelper::isEnabledGuestCheckout() && ! auth('customer')->check()) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'));
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_RECOVER_ECOMMERCE, $token, $request);
        }

        $order = Order::query()
            ->where([
                'token' => $token,
                'is_finished' => false,
            ])
            ->with(['products', 'address'])
            ->firstOrFail();

        if (session()->has('tracked_start_checkout') && session('tracked_start_checkout') == $token) {
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        } else {
            session(['tracked_start_checkout' => $token]);
            $sessionCheckoutData = [
                'name' => $order->address->name,
                'email' => $order->address->email,
                'phone' => $order->address->phone,
                'address' => $order->address->address,
                'country' => $order->address->country,
                'state' => $order->address->state,
                'city' => $order->address->city,
                'zip_code' => $order->address->zip_code,
                'shipping_method' => $order->shipping_method,
                'shipping_option' => $order->shipping_option,
                'shipping_amount' => $order->shipping_amount,
            ];
        }

        Cart::instance('cart')->destroy();
        foreach ($order->products as $orderProduct) {
            $request->merge(['qty' => $orderProduct->qty]);

            /**
             * @var Product $product
             */
            $product = Product::query()->find($orderProduct->product_id);

            if ($product) {
                OrderHelper::handleAddCart($product, $request);
            }
        }

        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);

        return $this
            ->httpResponse()
            ->setNextUrl(route('public.checkout.information', $token))
            ->setMessage(__('You have recovered from previous orders!'));
    }

    protected function createOrderFromData(array $data, ?Order $order): Order|null|false
    {
        return OrderHelper::createOrUpdateIncompleteOrder($data, $order);
    }
}
