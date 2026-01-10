<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\ConfirmVendorShipedToUser;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Marketplace\Models\Store;
use Illuminate\Http\Request;
use Botble\Theme\Facades\Theme;

class VendorConfirmVendorShipedToUserController extends BaseController
{

    public function __construct()
    {
        $version = get_cms_version();

        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);

        Theme::asset()
            ->container('footer')
            ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'], version: $version)
            ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'], version: $version)
            ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery'], version: $version);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer = auth('customer')->user();
        $confirmVendorShipedToUsers = ConfirmVendorShipedToUser::where('customer_id', $customer->id)->orderByDesc('id')->paginate(10);

        return view('vendor.confirm-vendor-shiped-to-user.index', compact('confirmVendorShipedToUsers'));
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
        return view('vendor.confirm-vendor-shiped-to-user.edit', compact('confirmVendorShiped', 'store', 'shipment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
