<?php

namespace ArchiElite\EcommerceNotification\Listeners;

use ArchiElite\EcommerceNotification\Supports\EcommerceNotification;
use Botble\Ecommerce\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        EcommerceNotification::make()
            ->sendNotifyToDriversUsing('order', trans('core/base::layouts.order_{{ order_id }}_has_been_created_on_{{ site_name }}.'), [
                'order_id' => get_order_code($order->id),
                'order_url' => route('customer.orders.view', $order->getKey()),
                'order' => $order,
                'status' => $order->status->label(),
                'customer' => $order->address,
            ]);
    }
}
