<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use App\Models\ConfirmVendorShipedToUser;
use App\Models\CustomerNotification;
use App\Models\VendorNotifications;
use Botble\Base\Models\AdminNotification;
use Botble\Ecommerce\Enums\OrderCancellationReasonEnum;
use Botble\Ecommerce\Enums\OrderHistoryActionEnum;
use Botble\Ecommerce\Facades\InvoiceHelper;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Forms\Fronts\CancelOrderForm;
use Botble\Ecommerce\Http\Controllers\BaseController;
use Botble\Ecommerce\Http\Requests\Fronts\CancelOrderRequest;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Marketplace\Models\Store;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends BaseController
{
    public function __construct()
    {
        $version = get_cms_version();

        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);
        Theme::asset()
            ->add('front-ecommerce-css', 'vendor/core/plugins/ecommerce/css/front-ecommerce.css', version: $version);
    }

    public function index()
    {
        SeoHelper::setTitle(__('Orders'));

        $orders = Order::query()
            ->where([
                'user_id' => auth('customer')->id(),
                'is_finished' => 1,
            ])
            ->withCount(['products'])
            ->orderByDesc('created_at')
            ->paginate(10);

        Theme::breadcrumb()
            ->add(__('Orders'), route('customer.orders'));

        return Theme::scope(
            'ecommerce.customers.orders.list',
            compact('orders'),
            'plugins/ecommerce::themes.customers.orders.list'
        )->render();
    }

    public function show(int|string $id)
    {
        $order = Order::query()
            ->where([
                'id' => $id,
                'user_id' => auth('customer')->id(),
            ])
            ->with(['address', 'products'])
            ->firstOrFail();

        $cancelOrderForm = CancelOrderForm::createFromModel($order);

        SeoHelper::setTitle(__('Order detail :id', ['id' => $order->code]));

        Theme::breadcrumb()
            ->add(
                __('Order detail :id', ['id' => $order->code]),
                route('customer.orders.view', $id)
            );

        return Theme::scope(
            'ecommerce.customers.orders.view',
            compact('order', 'cancelOrderForm'),
            'plugins/ecommerce::themes.customers.orders.view'
        )->render();
    }

    public function destroy(int|string $id, CancelOrderRequest $request)
    {
        return $this->handleCancelOrder($id, $request->input('cancellation_reason'), $request->input('cancellation_reason_description'));
    }

    public function print(int|string $id, Request $request)
    {
        /**
         * @var Order $order
         */
        $order = Order::query()
            ->where([
                'id' => $id,
                'user_id' => auth('customer')->id(),
            ])
            ->firstOrFail();

        abort_unless($order->isInvoiceAvailable(), 404);

        if ($request->input('type') == 'print') {
            return InvoiceHelper::streamInvoice($order->invoice);
        }

        return InvoiceHelper::downloadInvoice($order->invoice);
    }

    public function getCancelOrder(int|string $id)
    {
        return $this->handleCancelOrder($id);
    }

    public function confirmDelivery(int|string $id)
    {
        /** @var Order $order */
        $order = Order::query()
            ->where('user_id', auth('customer')->id())
            ->findOrFail($id);

        if (!$order->shipment->can_confirm_delivery) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('plugins/ecommerce::order.confirm_delivery_error'));
        }

        $order->shipment()->update([
            'customer_delivered_confirmed_at' => Carbon::now(),
        ]);

        OrderHistory::query()->create([
            'action' => OrderHistoryActionEnum::CONFIRM_DELIVERY,
            'description' => __('Order was confirmed delivery by customer :customer', ['customer' => $order->address->name ?: $order->user->name]),
            'order_id' => $order->getKey(),
        ]);
        $confirmVendor = ConfirmVendorShipedToUser::where('order_id', $order->id)->first();
        // fix
        if (!$confirmVendor) {
            return $this
        ->httpResponse()
        ->setMessage(__('plugins/ecommerce::order.confirm_delivery_success'));
        } 
        
        if ($confirmVendor->status == 1) {
            return $this
                ->httpResponse()
                ->setMessage(__('plugins/ecommerce::order.confirm_delivery_success'));
        } else {
            $store = Store::findOrFail($order->store_id);
            if ($store) {
                // $level = (int) $store->store_level_id;
                $amount =  $order->payment->amount;
                $fee_dis_ware_to_customer = setting('fee_distribution_warehouse_to_customer');
                // $shippingPercent = $level * 5;
                $shippingFee = ($fee_dis_ware_to_customer / 100) * $amount;
                // dd($shippingFee);
                $confirmVendorShiped = ConfirmVendorShipedToUser::where('order_id', $order->id)->first();

                $confirmVendorShiped->update([
                    'status' => 1,
                    'note' => (__('User Confirm')),
                    'updated_at' => Carbon::now()
                ]);

                $customer = $store->customer;

                $customer->update([
                    'walet_1' => $customer->walet_1 + $confirmVendorShiped->shipping_fee
                ]);

                VendorNotifications::create([
                    'title' => 'core/base::layouts.shipping-user_notification',
                    'description' => 'delivery_user_status_updated',
                    'variables' => json_encode([
                        'text_order_id' => $order->code,
                        'shipping_fee' => $shippingFee,
                    ]),
                    'vendor_id' => $store->customer->id,
                    'url' => route('marketplace.vendor.store-orders.index')
                ]);
                CustomerNotification::create([
                    'title' => 'core/base::layouts.shipping-user_notification',
                    'dessription' => 'delivery_user_status_updated',
                    'variables' => json_encode([
                        'text_order_id' => $order->code,
                        'shipping_fee' => $shippingFee,
                    ]),
                    'customer_id' => $store->customer->id,
                    'url' => route('marketplace.vendor.store-orders.index')
                ]);

                AdminNotification::create([
                    'title' => 'Xác nhận giao hàng từ kho',
                    'action_label' => 'Xem',
                    'action_url' => '/admin',
                    'description' => 'Người dùng ' . $store->name . ' đã xác nhận giao thành công đơn hàng ' . $order->code . ' từ kho ' . $store->name
                ]);
            }
        }
        return $this
            ->httpResponse()
            ->setMessage(__('plugins/ecommerce::order.confirm_delivery_success'));
    }

    protected function handleCancelOrder(int|string $id, ?string $reason = null, ?string $reasonDescription = null)
    {
        /** @var Order $order */
        $order = Order::query()
            ->where([
                'id' => $id,
                'user_id' => auth('customer')->id(),
            ])
            ->with(['address', 'products'])
            ->firstOrFail();

        if (! $order->canBeCanceled()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/ecommerce::order.cancel_error'));
        }

        OrderHelper::cancelOrder($order, $reason, $reasonDescription);

        $customerName = $order->address->name ?: $order->user->name;

        $description = match (true) {
            $reason != OrderCancellationReasonEnum::OTHER => __('Order was cancelled by customer :customer with reason :reason', [
                'customer' => $customerName,
                'reason' => OrderCancellationReasonEnum::getLabel($reason),
            ]),
            $reason == OrderCancellationReasonEnum::OTHER => __('Order was cancelled by customer :customer with reason :reason', [
                'customer' => $customerName,
                'reason' => $reasonDescription,
            ]),
            default => __('Order was cancelled by customer :customer', ['customer' => $customerName]),
        };

        OrderHistory::query()->create([
            'action' => OrderHistoryActionEnum::CANCEL_ORDER,
            'description' => $description,
            'order_id' => $order->getKey(),
        ]);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/ecommerce::order.cancel_success'));
    }
}
