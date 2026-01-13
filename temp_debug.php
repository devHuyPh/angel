<?php
require "vendor/autoload.php";
$app = require __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Models\Payment;
$payments = Payment::orderByDesc('id')->take(5)->get();
foreach($payments as $p){
    echo "pay {$p->id} order {$p->order_id} channel {$p->payment_channel} status {$p->status} amount {$p->amount} charge {$p->charge_id}\n";
    echo "  meta: ".json_encode($p->metadata)."\n";
}
$orders = Order::orderByDesc('id')->take(5)->get();
foreach($orders as $o){
    echo "order {$o->id} code {$o->code} token {$o->token} amount {$o->amount} payment {$o->payment_id}\n";
}
