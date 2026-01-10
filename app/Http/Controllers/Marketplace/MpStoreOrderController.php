<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\OrderStoreProduct;
use App\Models\StoreLevel;
use App\Models\StoreOrder;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Product;
use Botble\Marketplace\Http\Controllers\BaseController;
use Botble\Marketplace\Models\Store;
use Botble\Payment\Models\Payment;
use Illuminate\Http\Request;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\VendorNotifications;

use function Illuminate\Log\log;

class MpStoreOrderController extends BaseController
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
    $store = $customer->store;

    // Đơn bạn đã xử lý
    $myStoreOrders = $store->myStoreOrders()->orderByDesc('updated_at')->limit(12)->get();
    $myStoreOrdersCount = $store->myStoreOrders()->where('status','!=', 'completed')->count();

    // dd($myStoreOrdersCount);
    // Đơn bạn chưa xử lý (đơn nhập)
    $fromMyStoreOrders = $store->fromMyStoreOrders()
      ->where('payment_status', 'completed')
      ->where('status', '!=', 'completed')
      ->orderByDesc('created_at')->limit(10)->get();
    $fromMyStoreOrdersCount = $store->fromMyStoreOrders()
      ->where('payment_status', 'completed')
      ->where('status', '!=', 'completed')
      ->count();

    $auto_orders = StoreOrder::with(['toStore', 'products.product'])
      ->where('from_store', $store->id)
      ->where('type', 1) // loại đơn bù kho
      ->where('status', '!=','completed')
      // ->where('payment_status', 'completed')
      ->orderByDesc('created_at')
      ->paginate(10);

    $imp_orders = StoreOrder::with(['fromStore', 'products.product'])
      ->where('to_store', $store->id)
      ->where('type', 1) // loại đơn bù kho
      ->where('status', 'completed') // đã giao nhưng chưa nhập kho
      ->where('stock_imported', null) // đã giao nhưng chưa nhập kho
      ->orderByDesc('created_at')
      ->paginate(10);
    // dd($imp_orders);

    return view('marketplace/mp_store_order/index', compact(
      'store',
      'myStoreOrders',
      'fromMyStoreOrders',
      'myStoreOrdersCount',
      'fromMyStoreOrdersCount','auto_orders','imp_orders',
    ));
  }





  public function view(string $transaction_code)
  {
    $customer = auth('customer')->user();
    $store = $customer->store;

    $storeOrder = StoreOrder::where('transaction_code', $transaction_code)->firstOrFail();
    // dd($storeOrder->from_store, $store->id );



    return view('marketplace/mp_store_order/view', compact('storeOrder'));
  }


  public function confirmImportStock($id)
  {
    $customer = auth('customer')->user();
    $store = $customer->store;

    $storeOrder = StoreOrder::with(['products.product'])->findOrFail($id);

    if ($storeOrder->to_store !== $store->id) {
      abort(403, 'Bạn không có quyền xác nhận đơn hàng này.');
    }

    if ($storeOrder->stock_imported) {
      return redirect()
      ->route('marketplace.vendor.store-orders.view')
      ->with('error', 'Đơn hàng này đã được nhập kho.');
    }

    if ($storeOrder->status !== 'completed') {
      return redirect()
        ->route('marketplace.vendor.store-orders.view')
        ->with('error', 'Chỉ đơn hàng đã giao mới được nhập kho.');
    }

    DB::beginTransaction();

    try {
      foreach ($storeOrder->products as $item) {
        $originalProduct = $item->product;
        if (!$originalProduct)
          continue;

        $toProduct = \Botble\Ecommerce\Models\Product::where([
          'store_id' => $storeOrder->to_store,
          'name' => $originalProduct->name,
        ])->first();

        if ($toProduct) {
          $toProduct->quantity += $item->qty;
          $toProduct->save();
        }

        if ($storeOrder->from_store) {
          $fromProduct = \Botble\Ecommerce\Models\Product::where([
            'store_id' => $storeOrder->from_store,
            'name' => $originalProduct->name,
          ])->first();

          if ($fromProduct) {
            $fromProduct->quantity = max(0, $fromProduct->quantity - $item->qty);
            $fromProduct->save();
          }
        }
      }

      $storeOrder->stock_imported = true;
      $storeOrder->save();

      DB::commit();
      return back()->with('success', 'Xác nhận nhập kho thành công.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Lỗi nhập kho: ' . $e->getMessage());
      return redirect()
        ->route('marketplace.vendor.store-orders.view')
        ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
  }


  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $customer = auth('customer')->user();
    $store = $customer->store;
    $products = $store->products;

    $allStolevel = StoreLevel::get();

    $parentCodeStore = null;
    $parentStores = collect();

    // dd($customer->store);

    foreach ($allStolevel as $storeLevel) {
      if ($storeLevel?->value > $store?->storeLevel?->value) {
        $parentCodeStore = $storeLevel->id;
        break;
      }
    }

    // dd($parentCodeStore);

    if ($parentCodeStore) {
      $query = Store::where('store_level_id', $parentCodeStore);

      if ($parentCodeStore == 3) {
        $query->where('state', $store->state);
      } elseif ($parentCodeStore == 2) {
        $query->where('city', $store->city)
          ->orWhere('state', $store->state)
          ->where('id', '!=', $store->id);
      }

      $parentStores = $query->get();
    }

    // dd($parentStores[1]->products);

    return view('marketplace/mp_store_order/create', compact('store', 'products', 'parentStores'));
  }




  /**
   * Store a newly created resource in storage.
   */

  protected function generateUniqueTransactionCode()
  {
    do {
      $code = setting('payment_sepay_prefix') . 'SR' . str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
    } while (Payment::where('charge_id', $code)->exists() || StoreOrder::where('transaction_code', $code)->exists());

    return $code;
  }

  public function store(Request $request)
  {
    $products = [];

    // Lấy thông tin kho nhận hàng
    $store = Store::find($request->to_store);
    if (!$store) {
      return $this->httpResponse()
        ->setError()
        ->setMessage('Kho nhận hàng không tồn tại');
    }

    // Có thể null nếu là nhà máy
    $from_store = $request->from_store ? Store::find($request->from_store) : null;

    // Tạo mã giao dịch
    $transactionCode = $this->generateUniqueTransactionCode();

    // Lấy tên khách hàng viết hoa, không dấu (nếu có), nếu không thì gán "FACTORY"
    $storeNameFormatted = $store->customer
      ? Str::upper(preg_replace('/[^A-Z0-9]/', '', Str::ascii($store->customer->name)))
      : 'FACTORY';

    // Lọc danh sách sản phẩm
    foreach ($request->all() as $key => $value) {
      if (Str::endsWith($key, '_qty')) {
        $productId = (int) str_replace('_qty', '', $key);
        $qty = (int) $value;

        if ($qty > 0) {
          $products[] = [
            'product_id' => $productId,
            'qty' => $qty,
          ];
        }
      }
    }

    if (empty($products)) {
      return $this->httpResponse()
        ->setError()
        ->setMessage('Để tạo đơn phải có ít nhất 1 sản phẩm có số lượng lớn hơn 0');
    }

    // Tạo đơn hàng
    $storeOrder = StoreOrder::create([
      'from_store' => $request->from_store, // vẫn chấp nhận null
      'to_store' => $request->to_store,
      'status' => 'pending',
      'confirm_date' => null,
      'transaction_code' => $transactionCode . $storeNameFormatted,
      'amount' => $request->amount
    ]);

    // Ghi chi tiết sản phẩm
    foreach ($products as $product) {
      OrderStoreProduct::create([
        'product_id' => $product['product_id'],
        'order_store_id' => $storeOrder->id,
        'qty' => $product['qty'],
      ]);
    }

    // Gửi FCM nếu từ kho là của khách hàng và có token
    $fcmToken = optional(optional($from_store)->customer)->fcm_token;
    if ($fcmToken) {
      \App\Helpers\FCMHelper::sendToToken(
        $fcmToken,
        'Bạn có đơn hàng mới',
        'Vui lòng kiểm tra đơn hàng mới trong hệ thống.',
        url('/vendor/store-order/index')
      );
    }

    return $this->httpResponse()
      ->setNextRoute('marketplace.vendor.store-orders.checkout', $storeOrder->id)
      ->setMessage('Tạo đơn thành công');
  }


  public function checkNewOrders(Request $request)
  {
    $store = auth('customer')->user()->store;

    $hasNew = StoreOrder::where('from_store', $store->id)
      ->where('created_at', '>=', now()->subSeconds(20)) // kiểm tra đơn hàng mới trong 20s gần nhất
      ->exists();

    return response()->json(['hasNewOrders' => $hasNew]);
  }

  public function checkout(string $id)
  {
    $customer = auth('customer')->user();
    $store = $customer->store;
    $storeOrder = StoreOrder::where('id', $id)->first();

    if (!$storeOrder) {
      return $this
        ->httpResponse()
        ->setError()
        ->setNextRoute('marketplace.vendor.store-orders.index')
        ->setMessage('Đơn hàng không tồn tại');
    }

    $fromStoreName = optional($storeOrder->fromStore)->name ?? 'Nha may';

    if ($storeOrder->payment_status == 'completed') {
      VendorNotifications::create([
        'title' => 'core/base::layouts.title_created_store_order_notification',
        'description' => 'description_created_store_order_notification',
        'variables' => json_encode([
          'amount' => $storeOrder->amount,
          'text_from_store' => $fromStoreName,
        ]),
        'vendor_id' => $customer->id,
        'url' => '/marketing/dashboard'
      ]);
    }
    if ($storeOrder->to_store != $store->id) {
      return $this
        ->httpResponse()
        ->setError()
        ->setNextRoute('marketplace.vendor.store-orders.index')
        ->setMessage('Bạn không có quyền xem thông tin này');
    }

    // dd($storeOrder);

    return view('marketplace/mp_store_order/checkout', compact('store', 'storeOrder'));
  }

  public function checkStatus(Request $request, string $id): JsonResponse
  {
    $storeOrder = StoreOrder::find($id);

    if (!$storeOrder) {
      return response()->json([
        'message' => 'Deposit not found',
      ], 404);
    }

    return response()->json([
      'status' => $storeOrder->payment_status,
    ]);
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
  public function edit(string $transaction_code)
  {
    $customer = auth('customer')->user();
    $store = $customer->store;

    $storeOrder = StoreOrder::where('transaction_code', $transaction_code)->firstOrFail();

    if ($storeOrder->from_store != $store->id) {
      return $this
        ->httpResponse()
        ->setError()
        ->setNextRoute('marketplace.vendor.store-orders.index')
        ->setMessage('Bạn không có quyền chỉnh sửa đơn hàng này');
    }

    if ($storeOrder->payment_status != 'completed') {
      return $this
        ->httpResponse()
        ->setError()
        ->setNextRoute('marketplace.vendor.store-orders.index')
        ->setMessage('Chỉ có thể cập nhật trạng thái khi đơn đã thanh toán thành công');
    }

    $statusOptions = [
      'pending' => 'Chờ xác nhận',
      'processing' => 'Xác nhận giao hàng',
      'shipping' => 'Đang vận chuyển',
      'delivered' => 'Đã giao hàng',
      'completed' => 'Đã hoàn thành',
      'cancelled' => 'Đã hủy',
    ];

    return view('marketplace/mp_store_order/edit', compact('storeOrder', 'statusOptions'));
  }


  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $transaction_code)
  {
    $request->validate([
      'status' => 'required|in:pending,shipping,delivered,processing,cancelled,completed',
      'completed_image' => 'nullable|image|max:20482',
    ]);

    $storeOrder = StoreOrder::where('transaction_code', $transaction_code)->firstOrFail();

    $customer = auth('customer')->user();
    $store = $customer->store;

    // Kiểm tra quyền
    if ($storeOrder->from_store != $store->id) {
      return $this
        ->httpResponse()
        ->setError()
        ->setNextRoute('marketplace.vendor.store-orders.index')
        ->setMessage('Bạn không có quyền cập nhật đơn hàng này');
    }

    // Kiểm tra trạng thái thanh toán
    if ($storeOrder->payment_status != 'completed') {
      return $this
        ->httpResponse()
        ->setError()
        ->setNextRoute('marketplace.vendor.store-orders.index')
        ->setMessage('Chỉ cập nhật trạng thái khi đơn đã thanh toán thành công');
    }

    $path = null;

    // Nếu là trạng thái hoàn thành thì phải có ảnh
    if ($request->status === 'completed') {
      if (!$request->hasFile('completed_image')) {
        return $this
          ->httpResponse()
          ->setError()
          ->setNextRoute('marketplace.vendor.store-orders.edit', $storeOrder->id)
          ->setMessage('Vui lòng tải lên ảnh minh chứng để hoàn thành đơn hàng');
      }

      // Lưu ảnh
      $path = $request->file('completed_image')->store('store-orders', 'public');
      $storeOrder->completed_image = $path;

      try {
        $botToken = '7792877309:AAE9HmOSK9Ycxwmjn6rKn3E1Z8Lyo7nic2Q';
        $chatId = '-4640887033';

        $message = "📦 Có nhà cung cấp vừa hoàn thành đơn giao, vui lòng kiểm tra lại.";

        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
          'chat_id' => $chatId,
          'text' => $message,
        ]);
      } catch (\Exception $e) {
        Log::error('Telegram text notification failed: ' . $e->getMessage());
      }
    }

    // Cập nhật trạng thái đơn
    $storeOrder->status = $request->status;
    $storeOrder->save();

    return $this
      ->httpResponse()
      ->setNextRoute('marketplace.vendor.store-orders.index')
      ->setMessage('Cập nhật trạng thái đơn hàng thành công');
  }



  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }


  //list pending kho
  public function listPendingConfirm()
  {
    $store = auth('customer')->user()->store;

    $orders = StoreOrder::with(['toStore', 'toStore.storeLevel'])
      ->where('from_store', $store->id)
      ->where('status', 'pending')
      ->orderByDesc('created_at')
      ->paginate(10);

    return view('marketplace/mp_store_order/pending-confirm', compact('orders'));
  }

  public function confirmView($id)
  {
    $store = auth('customer')->user()->store;
    $order = StoreOrder::with(['toStore', 'products.product'])->findOrFail($id);

    if ($order->from_store != $store->id || $order->status !== 'pending') {
      abort(403, 'Bạn không có quyền xác nhận đơn này');
    }

    // Danh sách kho cùng cấp
    $alternativeStores = Store::where('store_level_id', $store->store_level_id)
      ->where('id', '!=', $store->id)
      ->get();

    return view('marketplace/mp_store_order/confirm-view', compact('order', 'alternativeStores'));
  }


  public function confirmDelivery(Request $request, $id)
  {
    $store = auth('customer')->user()->store;
    $order = StoreOrder::findOrFail($id);

    if ($order->from_store != $store->id || $order->status !== 'pending') {
      return back()->with('error', 'Không thể xác nhận đơn này');
    }

    $request->validate([
      'new_from_store' => 'nullable|exists:mp_stores,id',
    ]);

    if ($request->filled('new_from_store')) {
      $order->from_store = $request->input('new_from_store');
    }

    $order->status = 'processing';
    $order->confirm_date = now();
    $order->save();

    return redirect()->route('marketplace.vendor.store-orders.pending-confirm')
      ->with('success', 'Đã xác nhận giao hàng');
  }

public function buedit(string $transaction_code)
  {
    $customer = auth('customer')->user();
    $store = $customer->store;

    $storeOrder = StoreOrder::where('transaction_code', $transaction_code)
      ->where('type', 1) // Chỉ đơn bù
      ->firstOrFail();

    if ($storeOrder->from_store != $store->id) {
      return back()->with('error', 'Bạn không có quyền chỉnh sửa đơn bù kho này');
    }

    $statusOptions = [
      'pending' => 'Chờ xác nhận',
      'processing' => 'Đang chuẩn bị hàng',
      'shipping' => 'Đang vận chuyển',
      'delivered' => 'Đã giao hàng',
      'completed' => 'Hoàn thành',
      'cancelled' => 'Đã hủy',
    ];

    return view('marketplace/mp_store_order/replenish_edit', compact('storeOrder', 'statusOptions'));
  }

  public function buupdate(Request $request, string $transaction_code)
  {
    $request->validate([
      'status' => 'required|in:pending,processing,shipping,delivered,completed,cancelled',
      'completed_image' => 'nullable|image|max:20482',
    ]);

    $storeOrder = StoreOrder::with('products')->where('transaction_code', $transaction_code)
      ->where('type', 1)
      ->firstOrFail();

    $customer = auth('customer')->user();
    $store = $customer->store;

    if ($storeOrder->from_store != $store->id) {
      return back()->with('error', 'Bạn không có quyền cập nhật đơn bù kho này');
    }

    $path = null;

    if ($request->status === 'completed') {
      if (!$request->hasFile('completed_image')) {
        return back()->with('error', 'Vui lòng tải ảnh xác nhận để hoàn tất đơn hàng');
      }

      $path = $request->file('completed_image')->store('store-orders', 'public');
      $storeOrder->completed_image = $path;

      // Gửi thông báo Telegram
      try {
        $botToken = '7792877309:AAE9HmOSK9Ycxwmjn6rKn3E1Z8Lyo7nic2Q';
        $chatId = '-4640887033';

        $message = "📦 Có nhà cung cấp vừa hoàn thành đơn giao, vui lòng kiểm tra lại.";

        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
          'chat_id' => $chatId,
          'text' => $message,
        ]);
      } catch (\Exception $e) {
        Log::error('Telegram text notification failed: ' . $e->getMessage());
      }

      // Cập nhật tồn kho
      DB::transaction(function () use ($storeOrder) {
        foreach ($storeOrder->products as $item) {
          $productId = $item->product_id;
          $qty = $item->qty;

          // Trừ từ kho cấp trên
          $fromProduct = DB::table('ec_products')
            ->where('store_id', $storeOrder->from_store)
            ->where('id', $productId)
            ->first();

          if ($fromProduct) {
            DB::table('ec_products')
              ->where('id', $productId)
              ->where('store_id', $storeOrder->from_store)
              ->update([
                'quantity' => max(0, $fromProduct->quantity - $qty),
              ]);
          }

        }
      });
    }

    $storeOrder->status = $request->status;
    if ($path)
      $storeOrder->completed_image = $path;
    $storeOrder->save();

    return redirect()->route('marketplace.vendor.store-orders.index')
      ->with('success', 'Cập nhật trạng thái đơn bù kho thành công');
  }


  public function autoImportView($id)
{
    $customer = auth('customer')->user();
    $store = $customer->store;

    $storeOrder = StoreOrder::with(['products.product', 'fromStore', 'toStore'])->findOrFail($id);

    if ($storeOrder->to_store != $store->id) {
        abort(403, 'Bạn không có quyền xem đơn hàng này.');
    }
    // dd($storeOrder);
    return view('marketplace.mp_store_order.auto-import-view', compact('storeOrder'));
}


  //bù đơn
  public function buconfirmDelivery($id)
  {
    $customer = auth('customer')->user();
    $store = $customer->store;

    $storeOrder = StoreOrder::with(['products.product'])->findOrFail($id);

    // Kiểm tra quyền xác nhận
    if (!$store || $storeOrder->from_store != $store->id) {
      abort(403, 'Bạn không có quyền xác nhận đơn này');
    }

    if ($storeOrder->status !== 'pending') {
      return back()->with('error', 'Đơn hàng không ở trạng thái chờ xử lý');
    }

    DB::beginTransaction();
    try {
      foreach ($storeOrder->products as $item) {
        $product = Product::where([
          'store_id' => $store->id,
          'name' => $item->product->name,
        ])->first();

        if ($product && $product->quantity >= $item->qty) {
          $product->quantity -= $item->qty;
          $product->save();
        } else {
          throw new \Exception("Không đủ tồn kho sản phẩm: {$item->product->name}");
        }
      }

      $storeOrder->status = 'completed';
      $storeOrder->confirm_date = now();
      $storeOrder->save();

      DB::commit();
      return back()->with('success', '✅ Đã xác nhận giao hàng và trừ kho thành công');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Lỗi xác nhận giao hàng: ' . $e->getMessage());
      return back()->with('error', '❌ Có lỗi xảy ra: ' . $e->getMessage());
    }
  }

  // ✅ 2. Kho nhận xác nhận nhập kho đơn bù
  public function confirmImport($id)
  {
    $customer = auth('customer')->user();
    $store = $customer->store;

    DB::beginTransaction();

    try {
      // Lock đơn hàng để tránh xử lý song song
      $storeOrder = StoreOrder::with(['products.product'])
        ->where('id', $id)
        ->lockForUpdate()
        ->firstOrFail();

      if (!$store || $storeOrder->to_store != $store->id) {
        DB::rollBack();
        abort(403, 'Bạn không có quyền xác nhận đơn nhập kho này');
      }

      if ($storeOrder->status !== 'completed') {
        DB::rollBack();
        return back()->with('error', 'Đơn hàng chưa được giao hoàn tất');
      }

      if ($storeOrder->stock_imported) {
        DB::rollBack();
        return back()->with('success', '✅ Đơn hàng này đã được nhập kho.');
      }

      foreach ($storeOrder->products as $item) {
        $sourceProduct = $item->product;

        if (!$sourceProduct || !$sourceProduct->sku) {
          continue;
        }

        // So khớp sản phẩm trong kho hiện tại theo SKU
        $product = Product::where('store_id', $store->id)
          ->where('sku', $sourceProduct->sku)
          ->lockForUpdate() // Lock luôn sản phẩm nếu cần tránh double update
          ->first();

        if ($product) {
          $product->quantity += $item->qty;
          $product->save();
        } else {
          Log::warning("Sản phẩm SKU {$sourceProduct->sku} không tồn tại tại kho ID {$store->id}");
        }
      }

      $storeOrder->stock_imported = true;
      $storeOrder->save();

      DB::commit();

      return back()->with('success', '✅ Đã xác nhận nhập kho thành công.');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Lỗi nhập kho đơn bù: ' . $e->getMessage());
      return back()->with('error', '❌ Có lỗi xảy ra: ' . $e->getMessage());
    }
  }

}
