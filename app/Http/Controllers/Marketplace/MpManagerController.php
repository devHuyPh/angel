<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use Botble\Marketplace\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Botble\Theme\Facades\Theme;
use Botble\Marketplace\Models\Store;
use Botble\Ecommerce\Models\Order;

class MpManagerController extends BaseController
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
    public function index(Request $request)
    {
        
        $this->pageTitle(__('Store Manager'));

        $user = auth('customer')->user();
        // dd($user->store);
        $myStore = $user->store;
        // dd($myStore);
        $name = $request->input('name');
        // dd($name);
        $level = (int) $request->input('store_level_id', max(1, $myStore->store_level_id - 1));
        // dd($level);
        $perPage = (int) $request->input('per_page', 10);

        $query = Store::lowerLevelInArea($myStore)
            ->when($name, fn($q) => $q->where('name', 'like', '%' . $name . '%'))
            ->when($level, fn($q) => $q->where('store_level_id', $level))
            ->orderBy('id', 'desc');

        // Fix: always use paginate, even for "Tất cả"
        if ($perPage === -1) {
            $perPage = $query->count(); // lấy tất cả nhưng vẫn paginate để tránh lỗi
        }

        $stores = $query->paginate($perPage);

        return view('marketplace/mp_manager/index', compact('stores', 'myStore'));
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
        $store = Store::where('id', $id)->first();

        $this->pageTitle(__('View Store') .' '.$store->name);

        $orders = $store->orders()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $bestSellPros = $store->products()
            ->with(['soldQuantity', 'variations']) // nếu cần load thêm
            ->get()
            ->sortByDesc(fn($product) => $product->sold_count)
            ->take(10);

        return view('marketplace/mp_manager/show', compact('store', 'orders', 'bestSellPros'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
