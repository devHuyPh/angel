<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorLateDelivery;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\Order;
use App\Tables\VendorLateDeliveryTable;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Botble\Marketplace\Models\Store;
use Illuminate\Support\Facades\DB;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Models\VendorNotifications;
use App\Models\CustomerNotification;
use Botble\Base\Models\AdminNotification;

class VendorLateDeliveryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(VendorLateDeliveryTable $table)
    {
        if (! request()->ajax()) {
            $expiredHours = (int) setting('auto-confirmation-time', 24);
            $cutoff = now()->subHours($expiredHours);
            $newLate = 0;

            // Xóa các đơn đã hoàn thành/đã hủy khỏi danh sách giao muộn
            VendorLateDelivery::on('mysql')
                ->whereHas('order', function ($q) {
                    $q->whereIn('status', [OrderStatusEnum::COMPLETED, OrderStatusEnum::CANCELED]);
                })
                ->delete();

            // Đồng bộ danh sách đơn trễ (không cần cron)
            $orders = Order::on('mysql')
                ->with(['store', 'payment'])
                ->whereHas('payment', fn ($q) => $q->where('status', 'completed'))
                ->whereNotIn('status', [OrderStatusEnum::COMPLETED, OrderStatusEnum::CANCELED])
                ->where(function ($query) use ($cutoff) {
                    $query->where(function ($q) use ($cutoff) {
                        $q->whereNull('change_to_store_at')
                            ->where('created_at', '<=', $cutoff);
                    })->orWhere(function ($q) use ($cutoff) {
                        $q->whereNotNull('change_to_store_at')
                            ->where('change_to_store_at', '<=', $cutoff);
                    });
                })
                ->get();

            foreach ($orders as $order) {
                $created = VendorLateDelivery::on('mysql')->firstOrCreate(
                    [
                        'order_id' => $order->id,
                    ],
                    [
                        'store_id' => $order->store_id ?? 0,
                        'customer_id' => $order->user_id,
                        'status' => 0,
                    ]
                );

                if ($created->wasRecentlyCreated) {
                    $newLate++;
                }
            }

            if ($newLate > 0) {
                AdminNotification::create([
                    'title' => 'Đơn hàng muộn cần xử lý',
                    'action_label' => 'Xem',
                    'action_url' => route('vendor-late-delivery.index'),
                    'description' => "Có {$newLate} đơn hàng muộn cần xử lý.",
                ]);
            }
        }

        $this->pageTitle(__('Đơn giao muộn'));

        return $table->renderTable();
    }

    public function mergeOrdersByStoreProductName(Collection $orders, ?Store $storeSelect, Collection $storeProductMap)
    {
        if ($orders->isEmpty()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Đơn hàng không tồn tại');
        }

        DB::beginTransaction();

        try {
            $mainOrder = $orders->first();
            $otherOrders = $orders->slice(1);

            $shipmentTotalFee = 0;
            $paymentTotalAmount = 0;
            $totalAmountToAdd = 0;
            $totalSubTotalToAdd = 0;
            $totalWeightToAdd = 0;

            foreach ($otherOrders as $order) {
                foreach ($order->products as $product) {
                    $productNameNormalized = mb_strtolower(trim($product->product_name));
                    if (isset($storeProductMap[$productNameNormalized])) {
                        $storeProduct = $storeProductMap[$productNameNormalized];

                        $product->order_id = $mainOrder->id;
                        $product->product_id = $storeProduct->id;
                        $product->product_name = $storeProduct->name;
                        $product->save();

                        $totalWeightToAdd += (float) $product->weight * $product->qty;
                    }
                }

                $totalAmountToAdd += (float) $order->amount;
                $totalSubTotalToAdd += (float) $order->sub_total;

                if ($order->shipment) {
                    $shipmentTotalFee += (float) $order->shipment->shipping_fee_store;

                    $order->shipment->weight = max(0, (float) $order->shipment->weight - $totalWeightToAdd);
                    $order->shipment->order_id = $mainOrder->id;
                    $order->shipment->store_id = $storeSelect?->id; // null nếu không có store
                    $order->shipment->save();

                    if ($order->shipment->id !== $mainOrder->shipment?->id) {
                        $order->shipment->delete();
                    }
                }

                if ($order->payment) {
                    $paymentTotalAmount += (float) $order->payment->amount;
                    $order->payment->order_id = $mainOrder->id;
                    $order->payment->save();
                }

                if (OrderProduct::where('order_id', $order->id)->count() === 0) {
                    $order->delete();
                }
            }

            if ($otherOrders->isNotEmpty()) {
                if ($mainOrder->shipment) {
                    $mainOrder->shipment->shipping_fee_store += $shipmentTotalFee;
                    $mainOrder->shipment->weight += $totalWeightToAdd;
                    $mainOrder->shipment->store_id = $storeSelect?->id;
                    $mainOrder->shipment->save();
                }

                if ($mainOrder->payment) {
                    $mainOrder->payment->amount += $paymentTotalAmount;
                    $mainOrder->payment->save();
                }

                $mainOrder->amount += $totalAmountToAdd;
                $mainOrder->sub_total += $totalSubTotalToAdd;
            }

            $mainOrder->store_id = $storeSelect?->id;
            $mainOrder->save();

            $this->updateOrderProductsToMatchStore($mainOrder, $storeProductMap);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateOrderProductsToMatchStore(Order $order, Collection $storeProductMap)
    {
        $mergedProducts = OrderProduct::where('order_id', $order->id)->get();
        $grouped = $mergedProducts->groupBy(fn($item) => mb_strtolower(trim($item->product_name)));

        // dd($mergedProducts);
        foreach ($grouped as $productName => $group) {
            $first = $group->first();
            // dd($grouped);
            $storeProduct = $storeProductMap[$productName] ?? null;
            // dd($storeProduct);

            if (!$storeProduct) {
                continue;
            }

            if ($group->count() > 1) {
                $totalQty = $group->sum('qty');

                $first->update([
                    'qty' => $totalQty,
                    'product_id' => $storeProduct->id,
                    'product_name' => $storeProduct->name,
                ]);
                // dd($group->slice(1));
                $group->slice(1)->each->delete();
            } else {
                $first->update([
                    'product_id' => $storeProduct->id,
                    'product_name' => $storeProduct->name,
                ]);
            }
        }
    }

    public function deductProductQuantityFromOrderToken(string $token)
    {
        DB::beginTransaction();

        try {
            $orderUpdate = Order::where('token', $token)->firstOrFail();
            // dd($orderUpdate->products[1]->product);
            foreach ($orderUpdate->products as $orderProduct) {
                $product = $orderProduct->product;

                if (!$product) {
                    $orderUpdate->delete();

                    return $this
                        ->httpResponse()
                        ->setError()
                        ->setMessage(__('Sản phẩm của kho đã được bán hết hoặc ít hơn số lượng trước khi bạn đặt, vui lòng đặt lại đơn mới'));
                }

                $currentQty = $product->quantity;
                $deductQty = $orderProduct->qty;

                if ($currentQty < $deductQty) {
                    $orderUpdate->delete();

                    return $this
                        ->httpResponse()
                        ->setError()
                        ->setMessage(__('Sản phẩm của kho đã được bán hết hoặc ít hơn số lượng trước khi bạn đặt, vui lòng đặt lại đơn mới'));
                }

                $product->update([
                    'quantity' => $currentQty - $deductQty,
                ]);
            }

            $orderUpdate->change_to_store_at = Carbon::now();
            $orderUpdate->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            // Ghi log nếu cần
            Log::error("Failed to deduct product quantity for order token {$token}: " . $e->getMessage());

            // Tuỳ trường hợp, có thể throw lại để controller xử lý
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $token)
    {
        // dd($request->all());
        $order_update = Order::query()->where('token', $token)->get();
        $order_update = Order::on('mysql')->where('token', $token)->get();
        $storeId = $request->store_id;

        if ($storeId !== null) {
            if ($storeId === 'none') {
                $storeSelect = null;
            } else {
                $storeSelect = Store::find($storeId);
            }

            if ($storeSelect !== null || $storeId === 'none') {
                $requiredProducts = collect($order_update->first()?->products ?? [])->mapWithKeys(function ($product) {
                    return [mb_strtolower(trim($product->product_name)) => $product->qty];
                });

                // Kiểm tra tồn kho trước khi chuyển
                $inventorySource = $storeId === 'none'
                    ? Product::on('mysql')->where('store_id', 0)->get()
                    : $storeSelect->products;

                foreach ($requiredProducts as $name => $qty) {
                    $product = $inventorySource->first(fn($p) => mb_strtolower(trim($p->name)) === $name);
                    $available = $product?->quantity ?? 0;

                    if ($available < $qty) {
                        return $this
                            ->httpResponse()
                            ->setError()
                            ->setMessage(__('Kho được chọn không đủ hàng cho sản phẩm :name (cần :need, còn :left)', [
                                'name' => $product?->name ?? $name,
                                'need' => $qty,
                                'left' => $available,
                            ]));
                    }
                }

                // Trả lại số lượng cũ trước khi gộp
                foreach ($order_update as $orderUpdate) {
                    foreach ($orderUpdate->products as $orderProduct) {
                        if ($orderProduct->product_id) {
                            $product = $orderProduct->product;
                            if ($product) {
                                $product->quantity += $orderProduct->qty;
                                $product->save();
                            }
                        }
                    }
                }

                // Nếu store_id là 'none', lấy các sản phẩm có store_id = 0
                if ($storeId === 'none') {
                    $storeProductMap = Product::on('mysql')->where('store_id', 0)
                        ->get()
                        ->keyBy(fn($p) => mb_strtolower(trim($p->name)));
                } else {
                    $storeProductMap = $storeSelect->products
                        ->keyBy(fn($p) => mb_strtolower(trim($p->name)));
                }

                $this->mergeOrdersByStoreProductName($order_update, $storeSelect, $storeProductMap);

                $this->deductProductQuantityFromOrderToken($token);

                $vendorLateDelivery = VendorLateDelivery::on('mysql')->findOrFail($request->id);

                $vendorLateDelivery->status = 1; // đánh dấu đã chuyển kho
                $vendorLateDelivery->store_id = $storeSelect?->id ?? 0;
                $vendorLateDelivery->save();

                $orderCode = $order_update->first()?->code;

                // Thông báo cho chủ kho mới
                if ($storeSelect && $storeSelect->customer) {
                    VendorNotifications::create([
                        'title' => 'core/base::layouts.late_delivery_reassigned_title',
                        'description' => 'late_delivery_reassigned_vendor',
                        'variables' => json_encode([
                            'text_order_code' => $orderCode,
                            'text_from_store' => $vendorLateDelivery->store->name ?? __('Công ty'),
                        ]),
                        'vendor_id' => $storeSelect->customer->id,
                        'url' => route('marketplace.vendor.orders.index'),
                        'viewed' => 0,
                    ]);

                    // Đồng bộ thêm bản ghi cho widget notification của vendor (dùng customer guard)
                    CustomerNotification::create([
                        'title' => 'core/base::layouts.late_delivery_reassigned_title',
                        'dessription' => 'late_delivery_reassigned_vendor',
                        'variables' => json_encode([
                            'text_order_code' => $orderCode,
                            'text_from_store' => $vendorLateDelivery->store->name ?? __('Công ty'),
                        ]),
                        'customer_id' => $storeSelect->customer->id,
                        'url' => route('marketplace.vendor.orders.index'),
                        'viewed' => 0,
                    ]);
                }

                // Thông báo cho khách hàng của đơn (nếu có)
                $customerId = $order_update->first()?->user_id;
                if ($customerId) {
                    CustomerNotification::create([
                        'title' => 'core/base::layouts.late_delivery_reassigned_title',
                        'dessription' => 'late_delivery_reassigned_customer',
                        'variables' => json_encode([
                            'text_order_code' => $orderCode,
                        ]),
                        'customer_id' => $customerId,
                        'url' => route('customer.orders.view', $order_update->first()?->id ?? 0),
                        'viewed' => 0,
                    ]);
                }

                return $this
                    ->httpResponse()
                    ->setMessage('Cập nhật người giao hàng thành công');
            }
        }

        return $this
            ->httpResponse()
            ->setError()
            ->setMessage('Cửa hàng đã chọn không phù hợp, do tồn kho hoặc địa chỉ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function edit(int $id): Response
    {
        $vendorLateDelivery = VendorLateDelivery::on('mysql')->with(['order.address', 'store', 'order.products'])
            ->findOrFail($id);

        $order = $vendorLateDelivery->order;
        $address = $order?->address;

        $orderProducts = collect($order?->products ?? []);

        $requiredProducts = $orderProducts->mapWithKeys(function ($product) {
            return [mb_strtolower(trim($product->product_name)) => $product->qty];
        });

        $recommendedStores = Store::on('mysql')->with('products')
            ->where('id', '!=', $vendorLateDelivery->store_id)
            ->when($address, function ($query) use ($address) {
                $query->where(function ($q) use ($address) {
                    $q->where('city', $address->city)
                        ->orWhere('state', $address->state);
                });
            })
            ->orderByRaw('city = ? desc', [$address?->city])
            ->orderByRaw('state = ? desc', [$address?->state])
            ->get()
            ->filter(function ($store) use ($requiredProducts) {
                if ($requiredProducts->isEmpty()) {
                    return true;
                }

                foreach ($requiredProducts as $name => $qty) {
                    $product = $store->products->first(function ($p) use ($name, $qty) {
                        return mb_strtolower(trim($p->name)) === $name && $p->quantity >= $qty;
                    });

                    if (! $product) {
                        return false;
                    }
                }

                return true;
            })
            ->values();

        $allStores = Store::on('mysql')
            ->with('products')
            ->where('id', '!=', $vendorLateDelivery->store_id)
            ->get();

        $otherStores = $allStores->whereNotIn('id', $recommendedStores->pluck('id'))->values();

        $storeInventories = [];
        $storeHasEnough = [];

        $allCheckStores = $recommendedStores->concat($otherStores)->unique('id');

        foreach ($allCheckStores as $store) {
            $inventory = [];
            foreach ($requiredProducts as $name => $qty) {
                $product = $store->products->first(fn($p) => mb_strtolower(trim($p->name)) === $name);
                $available = $product?->quantity ?? 0;
                $inventory[] = [
                    'name' => $product?->name ?? $name,
                    'required' => $qty,
                    'available' => $available,
                    'enough' => $available >= $qty,
                ];
            }
            $storeInventories[$store->id] = $inventory;
            $storeHasEnough[$store->id] = collect($inventory)->every(fn($item) => $item['enough']);
        }

        // Công ty (store_id = 0)
        $companyProducts = Product::on('mysql')->where('store_id', 0)->get();
        $companyInventory = [];
        foreach ($requiredProducts as $name => $qty) {
            $product = $companyProducts->first(fn($p) => mb_strtolower(trim($p->name)) === $name);
            $available = $product?->quantity ?? 0;
            $companyInventory[] = [
                'name' => $product?->name ?? $name,
                'required' => $qty,
                'available' => $available,
                'enough' => $available >= $qty,
            ];
        }

        $storeInventories['none'] = $companyInventory;
        $storeHasEnough['none'] = collect($companyInventory)->every(fn($item) => $item['enough']);

        return response()->view('admin.vendor_late_delivery.edit-page', [
            'vendorLateDelivery' => $vendorLateDelivery,
            'order' => $order,
            'address' => $address,
            'recommendedStores' => $recommendedStores,
            'otherStores' => $otherStores,
            'storeInventories' => $storeInventories,
            'storeHasEnough' => $storeHasEnough,
            'orderProducts' => $orderProducts,
        ]);
    }
}
