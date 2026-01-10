<?php

namespace App\Http\Controllers;


use App\Models\MpStore;
use App\Models\StoreLevel;
use Illuminate\Http\Request;
use Botble\Location\Models\State;
use Botble\Location\Models\City;
use Botble\Marketplace\Enums\PayoutPaymentMethodsEnum;
use Illuminate\Support\Arr;
use Botble\Marketplace\Models\Store as MarketplaceStore;


class StoreLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $levels = StoreLevel::withCount('stores')
            ->orderBy('value')
            ->get();

        $storeQuery = MarketplaceStore::query()
            ->with([
                'storeLevel',
                'customer',
                'customer.referrer',
            ])
            ->withCount('orders');

        if ($request->filled('level')) {
            $storeQuery->where('store_level_id', $request->integer('level'));
        }

        if ($keyword = $request->input('keyword')) {
            $storeQuery->where('name', 'like', '%' . $keyword . '%');
        }

        $stores = $storeQuery
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('store_levels.index', [
            'levels' => $levels,
            'stores' => $stores,
            'selectedLevel' => $request->input('level'),
            'keyword' => $keyword ?? null,
        ]);
    }


public function showStore(MarketplaceStore $store)
    {
        $store->load([
            'storeLevel',
            'customer',
            'customer.vendorInfo',
        ]);

        $orders = $store->orders()
            ->with([
                'products.product',
                'payment',
            ])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $products = $store->products()
            ->orderBy('name')
            ->get();

        $inventoryValue = $products->sum(function ($product) {
            $price = $product->front_sale_price ?? $product->price ?? 0;

            return (float) ($product->quantity ?? 0) * (float) $price;
        });

        $vendorInfo = $store->customer?->vendorInfo;
        $paymentChannel = $vendorInfo?->payout_payment_method ?? PayoutPaymentMethodsEnum::BANK_TRANSFER;
        $bankFields = PayoutPaymentMethodsEnum::getFields($paymentChannel);
        $bankInfo = [];

        if ($vendorInfo && $vendorInfo->bank_info) {
            foreach ($bankFields as $key => $field) {
                $value = Arr::get($vendorInfo->bank_info, $key);

                if ($value) {
                    $bankInfo[] = [
                        'label' => Arr::get($field, 'title'),
                        'value' => $value,
                    ];
                }
            }
        }

        return view('store_levels.show', [
            'store' => $store,
            'orders' => $orders,
            'products' => $products,
            'inventoryValue' => $inventoryValue,
            'bankInfo' => $bankInfo,
            'paymentChannel' => $paymentChannel,
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      return view('store_levels.create');
  }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $request->validate([
      'name' => 'required|string',
      'value' => 'required|integer|min:0',
    ]);

    StoreLevel::create($request->only(['name', 'value']));
    return redirect()->route('store-levels.index')->with('success', 'Thêm loại cửa hàng thành công!');
  }

    /**
     * Display the specified resource.
     */
    public function show(StoreLevel $store_level)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
  public function edit(StoreLevel $storeLevel)
  {
    // dd($storeLevel);
    return view('store_levels.edit', compact('storeLevel'));
  }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, StoreLevel $storeLevel)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      // 'value' => 'required|numeric|min:0',
      'commission'=>'required|numeric|min:0|max:100',
    
    ]);

    $storeLevel->update($request->only(['name', 'value','commission']));

    return redirect()->route('store-levels.index')->with('success', 'Cập nhật thành công!');
  }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreLevel $storeLevel)
    {
      $storeLevel->delete();
    return redirect()->route('store-levels.index')->with('success', 'Xóa thành công!');
  }

  public function assignForm()
  {
    $levels = StoreLevel::all();
    $stores = MpStore::all();
// dd($stores);
    return view('store_levels.assign', compact('levels', 'stores'));
  }

  public function assign(Request $request)
  {
    $request->validate([
      'level_id' => 'required|exists:store_levels,id',
      'store_ids' => 'required|array',
      'store_ids.*' => 'exists:mp_stores,id',
    ]);
    // dd($request->store_ids, $request->level_id);

    MpStore::whereIn('id', $request->store_ids)
      ->update(['store_level_id' => $request->level_id]);

    return redirect()->route('store-levels.assign.form')->with('success', 'Gán loại cửa hàng thành công!');
  }
 public function listStore()
  {
    $levels = StoreLevel::with(['stores' => function ($query) {
      $query->orderBy('name'); // Sắp xếp cửa hàng theo tên
    }])
      ->orderBy('value') // Sắp xếp cấp độ theo value (5tr, 50tr, 200tr)
      ->get(); // Lấy tất cả để nhóm theo tỉnh
    // Lấy danh sách tỉnh duy nhất
    $states = State::whereIn('id', MpStore::distinct()->pluck('state'))
      ->orderBy('name')
      ->get();
    $cities=City::whereIn('id', MpStore::distinct()->pluck('city'))->orderBy('name')->get();

    return view('store_levels.liststore', compact('levels', 'states','cities'));
  }
public function getHigherLevelStores($storeId)
  {
    try {
      $store = MpStore::with(['level', 'stateModel'])->findOrFail($storeId);
      if (!$store->level) {
        \Log::error('Store level not found for store ID: ' . $storeId);
        return response()->json(['error' => 'Cấp độ cửa hàng không tồn tại'], 404);
      }

      $currentLevelValue = $store->level->value;
      $stateId = $store->state;

      $higherLevels = StoreLevel::where('value', '>', $currentLevelValue)
        ->orderBy('value')
        ->get();

      $result = [];
      foreach ($higherLevels as $level) {
        $stores = MpStore::where('store_level_id', $level->id)
          ->where('state', $stateId)

          ->with('stateModel')
          ->orderBy('name')
          ->get(['id', 'name', 'state'])
          ->map(function ($store) {
            return [
              'id' => $store->id,
              'name' => $store->name,
              'state_name' => $store->stateModel ? $store->stateModel->name : '---'
            ];
          })
          ->toArray();
        if ($stores) {
          $result[] = [
            'level_name' => $level->name,
            'level_value' => $level->value,
            'stores' => $stores
          ];
        }
      }

      return response()->json($result);
    } catch (\Exception $e) {
      \Log::error('Error in getHigherLevelStores: ' . $e->getMessage());
      return response()->json(['error' => 'Lỗi server'], 500);
    }
  }

}
