<?php

namespace App\Http\Controllers\Webhook;

use App\Models\StoreOrder;
use App\Models\VendorNotifications;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StoreOrderWebhookController extends BaseController
{
    public function __invoke(Request $request, SettingStore $settingStore): Response
    {
        // 1. Verify webhook token
        $authorization = $request->header('Authorization');

        if (! $authorization) {
            Log::warning('StoreOrder webhook: missing Authorization header', [
                'headers' => $request->headers->all(),
            ]);

            return response('Unauthorized', 401);
        }

        if (str_starts_with($authorization, 'Apikey ')) {
            $token = substr($authorization, strlen('Apikey '));
        } else {
            Log::warning('StoreOrder webhook: invalid Authorization header format', [
                'authorization' => $authorization,
            ]);

            return response('Unauthorized', 401);
        }

        $expectedToken = setting('payment_sepay_webhook_secret');

        if (! $expectedToken || ! Hash::check($token, $expectedToken)) {
            Log::warning('StoreOrder webhook: invalid API token', [
                'given' => $token,
            ]);

            return response('Unauthorized', 401);
        }

        // 2. Log raw payload to debug
        Log::info('StoreOrder webhook payload', $request->all());

        // 3. Lấy mã code + số tiền
        // Ưu tiên code; nếu thiếu, dùng content hoặc transaction_code (SePay gửi content = transaction_code)
        $code = $request->input('code')
            ?? $request->input('content')
            ?? $request->input('transaction_code');

        $transferAmount = (int) ($request->input('transferAmount') ?? $request->input('amount'));

        if (empty($code) || $transferAmount <= 0) {
            Log::warning('StoreOrder webhook: missing code or invalid amount', [
                'code' => $code,
                'transferAmount' => $transferAmount,
            ]);

            // Không trả 4xx để tránh provider retry liên tục
            return response('ignored: invalid_payload', 200);
        }

        // 4. Tìm đơn hàng theo transaction_code + amount
        $storeOrder = StoreOrder::where('transaction_code', $code)
            ->where('amount', $transferAmount)
            ->first();

        if (! $storeOrder) {
            Log::warning('StoreOrder webhook: order not found', [
                'code' => $code,
                'transferAmount' => $transferAmount,
            ]);

            // Trả 200 để provider ghi nhận đã nhận
            return response('ignored: order_not_found', 200);
        }

        // 5. Nếu đã completed thì bỏ qua
        if ($storeOrder->payment_status === 'completed') {
            Log::info('StoreOrder webhook: order already completed', [
                'id' => $storeOrder->id,
                'transaction_code' => $storeOrder->transaction_code,
            ]);

            return response('ok', 200);
        }

        // 6. Cập nhật trạng thái thanh toán
        $storeOrder->update([
            'payment_status' => 'completed',
        ]);

        $customerFromName = 'Cong ty';
        $customerFrom = $storeOrder->fromStore;
        $customerTo = $storeOrder->toStore->customer;

        if ($customerFrom) {
            $customerFromName = $customerFrom->name;
            $shippingFee = 0;

            $fromLevel = (int) optional($customerFrom->storeLevel)->id;
            $toLevel = (int) optional($storeOrder->toStore->storeLevel)->id;
            $levelDiff = $fromLevel - $toLevel;

            if ($levelDiff === 1) {
                $shippingFee += $storeOrder->amount * 5 / 100;
            } elseif ($levelDiff === 2) {
                $shippingFee += $storeOrder->amount * 10 / 100;
            }

            VendorNotifications::create([
                'title' => 'core/base::layouts.goods_receipt_notification',
                'description' => 'supplier_order_request_notification',
                'variables' => json_encode([
                    'amount' => $storeOrder->amount,
                    'shipfee' => $shippingFee,
                    'text_storename' => $storeOrder->toStore->name,
                ]),
                'vendor_id' => $customerFrom->customer->id,
                'url' => route('marketplace.vendor.store-orders.index'),
            ]);
        }

        VendorNotifications::create([
            'title' => 'core/base::layouts.goods_receipt_notification',
            'description' => 'purchase_order_paid_notification',
            'variables' => json_encode([
                'amount' => $storeOrder->amount,
                'text_storename' => $customerFromName,
            ]),
            'vendor_id' => $customerTo->id,
            'url' => route('marketplace.vendor.store-orders.index'),
        ]);

        return response('ok', 200);
    }
}
