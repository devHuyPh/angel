<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        if ($order->status === 'completed' && $order->is_finished == 1) {
            $this->updateUplineRanks($order->user_id);
        }
    }

    public function updated(Order $order)
    {
        if ($order->isDirty('status') && $order->status === 'completed' && $order->is_finished == 1) {
            $this->updateUplineRanks($order->user_id);
        }
    }

    private function updateUplineRanks($userId)
    {
        $customer = Customer::find($userId);
        if (!$customer)
            return;

        $upline = $customer->upline;
        while ($upline) {
            $upline->updateRank();
            $upline = $upline->upline;
        }
    }
}
