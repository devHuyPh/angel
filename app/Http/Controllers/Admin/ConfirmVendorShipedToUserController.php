<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfirmVendorShipedToUser;
use App\Models\CustomerNotification;
use App\Models\VendorNotifications;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Customer;
use Botble\Marketplace\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfirmVendorShipedToUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $confirmVendorShipedToUsers = ConfirmVendorShipedToUser::orderByDesc('id')->with(['admin'])->paginate(10);

        return view('admin.confirm-vendor-shiped-to-user.index', compact('confirmVendorShipedToUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $confirmVendorShiped = ConfirmVendorShipedToUser::findOrFail($id);
        $order = $confirmVendorShiped->order;
        $store = Store::findOrFail($order->store_id);
        $shipment = $order->shipment;
        // dd($shipment);
        return view('admin.confirm-vendor-shiped-to-user.edit', compact('confirmVendorShiped', 'store', 'shipment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd(Auth::user());
        $admin = Auth::user();
        $confirmVendorShiped = ConfirmVendorShipedToUser::findOrFail($id);
        $currentStatus = $confirmVendorShiped->status;
        if ($currentStatus == false) {
            $currentStatus = 0;
        }
        $newStatus = $request->status;
        // dd($currentStatus);

        $validTransitions = [
            0 => [1, 2],
            1 => [],
            2 => []
        ];

        if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('core/base::layouts.no_change_status_from') . ' ' .
                    trans('core/base::layouts.' . $currentStatus) . ' ' .
                    trans('core/base::layouts.to') . ' ' .
                    trans('core/base::layouts.' . $newStatus));
        }

        if ($request->submit_cancel || $request->status == 2) {
            $request->validate([
                'description' => 'required|string|max:255'
            ]);

            $confirmVendorShiped->update([
                'status' => 2,
                'note' => $request->description,
                'admin_id' => $admin->id,
                'updated_at' => Carbon::now()
            ]);

            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::layouts.confirm') . ' ' . trans('core/base::layouts.success'));
        }

        $confirmVendorShiped->update([
            'admin_id' => $admin->id,
            'status' => $newStatus,
            'note' => $request->description,
            'updated_at' => Carbon::now()
        ]);

        if ($newStatus == 1) {
            $customer = Customer::findOrFail($confirmVendorShiped->customer_id);

            $customer->update([
                'walet_1' => $customer->walet_1 + $confirmVendorShiped->shipping_fee
            ]);

            VendorNotifications::create([
                'title' => 'core/base::layouts.shipping-user_notification',
                'description' => 'delivery_status_admin_updated',
                'variables' => json_encode([
                    'text_order_id' => $confirmVendorShiped->order->code,
                    'shipping_fee' => $confirmVendorShiped->shipping_fee,
                ]),
                'vendor_id' => $customer->id,
                'url' => route('marketplace.vendor.store-orders.index')
            ]);

            CustomerNotification::create([
                'title' => 'core/base::layouts.shipping-user_notification',
                'dessription' => 'delivery_status_admin_updated',
                'variables' => json_encode([
                    'text_order_id' => $confirmVendorShiped->order->code,
                    'shipping_fee' => $confirmVendorShiped->shipping_fee,
                ]),
                'customer_id' => $customer->id,
                'url' => '/marketing/dashboard'
            ]);
        }

        if ($request->submitter_exit) {
            return $this
                ->httpResponse()
                ->setNextRoute('store-to-user.index')
                ->setMessage(trans('core/base::layouts.confirm') . ' ' . trans('core/base::layouts.success'));
        } else {
            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::layouts.confirm') . ' ' . trans('core/base::layouts.success'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
