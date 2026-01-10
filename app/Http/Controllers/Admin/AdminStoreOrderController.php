<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreOrder;
use App\Models\VendorNotifications;
use Botble\Ecommerce\Models\Customer;
use Botble\Marketplace\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminStoreOrderController extends Controller
{
  // public function listPendingBonus()
  // {
  //   $orders = StoreOrder::with(['fromStore.storeLevel', 'toStore.storeLevel', 'fromStore.customer'])
  //     ->whereNotNull('from_store')
  //     ->where('status', 'completed')
  //     ->whereNotNull('stock_imported')
  //     ->whereNull('bonus_confirmed')
  //     ->orwhere(function ($query) {
  //       $query->where('type', 1)
  //         ->orWhere(function ($q) {
  //           $q->where('type', '!=', 1)
  //             ->where('payment_status', 'completed');
  //         });
  //     })
  //     ->orderByDesc('updated_at')
  //     ->paginate(10);

  //   return view('admin.store-orders.pending-bonus', compact('orders'));
  // }


  public function listPendingBonus()
  {
    $orders = StoreOrder::with(['fromStore.storeLevel', 'toStore.storeLevel', 'fromStore.customer'])
      ->where(function ($q) {
        $q->whereNotNull('from_store')
          ->where('status', 'completed')
          ->whereNotNull('stock_imported')
          ->whereNull('bonus_confirmed');
      })
      ->where(function ($q) {
        $q->where('type', 1)
          ->orWhere(function ($qq) {
            $qq->where('type', '!=', 1)
              ->where('payment_status', 'completed');
          });
      })
      ->orderByDesc('updated_at')
      ->paginate(10);

    return view('admin.store-orders.pending-bonus', compact('orders'));
  }


  public function view($id)
  {
    $order = StoreOrder::with([
      'fromStore.storeLevel',
      'toStore.storeLevel',
      'fromStore.customer',
      'products.product'
    ])->findOrFail($id);

    return view('admin.store-orders.view', compact('order'));
  }

  public function confirmBonus($id)
  {
    $storeOrder = StoreOrder::with(['fromStore.storeLevel', 'toStore.storeLevel'])->findOrFail($id);
    if ($storeOrder->bonus_confirmed || $storeOrder->status !== 'completed') {
      return back()->with('error', 'Không thể xác nhận thưởng.');
    }

    $fromStore = $storeOrder->fromStore;
    $toStore = $storeOrder->toStore;
    $fromCustomer = $fromStore->customer;
    // dd($fromCustomer->walet_1);
    $levelDiff = (int) $fromStore->storeLevel->id - (int) $toStore->storeLevel->id;
    // dd($levelDiff);

    if ($levelDiff == 1) {
      $bonus = $storeOrder->amount * 0.05;
    } elseif ($levelDiff == 2) {
      $bonus = $storeOrder->amount * 0.10;
    } else {
      return back()->with('error', 'Không đủ điều kiện để thưởng.');
    }
    DB::beginTransaction();
    try {
      $fromCustomer->walet_1 += $bonus;
      $fromCustomer->save();

      Log::info('Ví sau khi cộng:', ['walet_1' => $fromCustomer->walet_1]);

      $storeOrder->bonus_confirmed = true;
      $storeOrder->bonus_amount = $bonus;
      $storeOrder->save();

      DB::commit();

      VendorNotifications::create([
        'title' => 'core/base::layouts.title_bonused_shipping_notification',
        'description' => 'description_bonused_shipping_notification',
        'variables' => json_encode([
          'bonus_amount' => $bonus,
          'text_from_store' => $storeOrder->fromStore->name,
        ]),
        'vendor_id' => $storeOrder->toStore->customer->id,
        'url' => '/marketing/dashboard'
      ]);

      return back()->with('success', 'Đã thưởng: ' . number_format($bonus) . 'đ');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Lỗi thưởng:', ['message' => $e->getMessage()]);
      return back()->with('error', 'Lỗi: ' . $e->getMessage());
    }
  }



  public function listExpiredPending()
  {
    $orders = StoreOrder::with(['fromStore', 'toStore', 'fromStore.storeLevel', 'toStore.storeLevel'])
      ->whereNotNull('from_store')
      ->where('status', 'pending')
      ->where('payment_status', 'completed')
      ->where('created_at', '<=', now()->subHours(24))
      ->orderByDesc('created_at')
      ->paginate(20);

    return view('admin.store-orders.expired-pending', compact('orders'));
  }

  public function editFromStore($id)
  {
    $order = StoreOrder::with(['fromStore', 'toStore', 'fromStore.storeLevel'])->whereNotNull('from_store')->findOrFail($id);

    $currentLevelId = (int) $order->toStore->store_level_id;

    if ($currentLevelId == 3) {
      $availableStores = Store::where('store_level_id', 3)
        ->where('id', '!=', $order->from_store)
        ->get();
    } else {

      $availableStores = Store::where('store_level_id', '>', $currentLevelId)->get();
    }

    // dd($availableStores,$currentLevelId);
    return view('admin.store-orders.edit-from-store', compact('order', 'availableStores'));
  }

  public function updateFromStore(Request $request, $id)
  {
    $order = StoreOrder::findOrFail($id);

    $request->validate([
      'new_from_store' => 'nullable|exists:mp_stores,id',
    ]);

    $order->from_store = $request->input('new_from_store'); // nếu null thì set lại null
    $order->save();
    VendorNotifications::create([
      'title' => 'core/base::layouts.title_update_from_store_notification',
      'description' => 'description_update_from_store_notification',
      'variables' => json_encode([
        'text_transaction_code' => $order->transaction_code,
        'text_from_store' => $order->fromStore->name,
      ]),
      'vendor_id' => $order->toStore->customer->id,
      'url' => '/marketing/dashboard'
    ]);

    return redirect()->route('admin.store-orders.expired-pending')
      ->with('success', 'Đã cập nhật kho gửi cho đơn ' . $order->transaction_code);
  }



  //đơn hàng từ nhà máy

 public function listFactoryOrders(Request $request)
	{
    $query = StoreOrder::with(['toStore.storeLevel'])
      ->whereNull('from_store');

    $search = $request->input('transaction_code');

    if ($search) {
      $query->where('transaction_code', 'like', '%' . $search . '%');
    }

    $paidOrders = (clone $query)
      ->where('payment_status', 'completed')
      ->orderByDesc('created_at')
      ->paginate(20, ['*'], 'paid_page');

    $unpaidOrders = (clone $query)
      ->where(function ($q) {
        $q->whereNull('payment_status')
          ->orWhere('payment_status', '!=', 'completed');
      })
      ->orderByDesc('created_at')
      ->paginate(20, ['*'], 'unpaid_page');

    $paidOrders->appends($request->only('transaction_code'));
    $unpaidOrders->appends($request->only('transaction_code'));

    return view('admin.store-orders.factory-orders', compact('paidOrders', 'unpaidOrders', 'search'));
	}

  public function viewFactoryOrder($id)
  {
    $order = StoreOrder::with(['toStore.storeLevel', 'products.product'])
      ->whereNull('from_store')
      ->findOrFail($id);

    return view('admin.store-orders.view-factory', compact('order'));
  }

  public function updateFactoryOrderStatus(Request $request, $id)
  {
    $request->validate([
      'status' => 'required|in:pending,processing,shipping,delivered,completed,cancelled',
    ]);

    $order = StoreOrder::whereNull('from_store')->findOrFail($id);
    $order->status = $request->input('status');
    $order->save();

    VendorNotifications::create([
      'title' => 'core/base::layouts.title_update_factory_order_status_notification',
      'description' => 'description_update_factory_order_status_notification',
      'variables' => json_encode([
        'text_transaction_code' => $order->transaction_code,
        'text_status_order' => $order->status,
      ]),
      'vendor_id' => $order->toStore->customer->id,
      'url' => '/marketing/dashboard'
    ]);

    return back()->with('success', 'Đã cập nhật trạng thái đơn hàng.');
  }

  public function updateFactoryPaymentStatus(Request $request, $id)
  {
    $request->validate([
      'payment_status' => 'required|in:pending,completed,failed',
    ]);

    $order = StoreOrder::whereNull('from_store')->findOrFail($id);
    $order->payment_status = $request->input('payment_status');
    $order->save();

    return back()->with('success', 'C §-p nh §-t tr §­ng thA­i thanh toA­n thA nh cA´ng.');
  }


  public function listHistoryBonus()
  {
    $orders = StoreOrder::get();
    // dd($orders);
    return view('admin.store-orders.history-bonus', compact('orders'));
  }
}