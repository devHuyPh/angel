<?php

namespace Botble\Ecommerce\Services;
use App\Models\Order;
use Botble\Ecommerce\Models\WareHouseReferral;
use Botble\Ecommerce\Models\Customer;
use App\Models\CustomerNotification;
use Botble\Marketplace\Models\Store;

class WarehouseReferralService
{
    public $warehouse;

    public function __construct()
    {
        $this->warehouse = setting('warehouse-referral-commission');
    }

    //index
    public function warehouseReferral ($order_id){

        $order = Order::findOrFail($order_id);
        $subTotal  = (float) ($order->sub_total ?? 0);
        $warehouse = (float) ($this->warehouse ?? 0);
        $referral = round($subTotal * $warehouse / 100, 2);
        $recipient_id = $this->referralId($order->store_id);
        // dd($recipient_id);
        if($recipient_id !== null){
            $this->referralHistory($order_id,$referral,$recipient_id);
            $receiver = Customer::findOrFail($recipient_id);
            $receiver->increment('walet_2', $referral);
            $receiver->increment('total_warehouse_referral', $referral);
            // $customer = Customer::findOrFail($order->user_id);
            $store = Store::findOrFail($order->store_id);
            $this->customerNotification($referral,$store->name ,$recipient_id);
        }
    }

    // lưu lịch sử hoa hồng kho
    public function referralHistory($order_id,$referral,$recipient_id){
        wareHouseReferral::create(
            [   
                'order_id' =>$order_id,
                'amount' => $referral,
                'customer_id' => $recipient_id
            ]
        );
    }

    // lấy id người giới thiệu
    public function referralId ($store_id){
        $customer_id = Store::where('id', $store_id)->value('customer_id');
        $referral_id = Customer::where('id', $customer_id)->value('referral_ids');
        return $referral_id;
    }

    // gửi thông báo cho người nhận
    public function customerNotification($amount, $name_warehouse, $customer_id){
        CustomerNotification::create([
            'title' => 'core/base::layouts.your-ware-house-referral',
            'dessription' => 'ware_house_referral_point_wallet',
            'variables' => json_encode([
                'amount' => $amount,
                'text_name' => $name_warehouse,
            ]),
            'customer_id' => $customer_id,
            'url' => '/marketing/dashboard'
        ]);
    }
}