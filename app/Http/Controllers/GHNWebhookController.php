<?php

namespace App\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Botble\Ecommerce\Models\Shipment;

class GHNWebhookController extends BaseController
{
    public function handleWebhook(Request $request)
    {
        // Log dữ liệu nhận được từ GHN để debug
        Log::info('GHN Webhook Received:', $request->all());

        // Lấy dữ liệu từ request
        $data = $request->all();

        // Kiểm tra xem có mã vận đơn hay không
        if (!isset($data['OrderCode']) || !isset($data['Status'])) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 400);
        }

        // Tìm Shipment theo OrderCode
        $shipment = Shipment::where('ghn_code', $data['OrderCode'])->first();

        if (!$shipment) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
        }

        // Cập nhật trạng thái đơn hàng trong hệ thống
        $shipment->update([
            'status' => $data['Status'], // Cập nhật trạng thái từ GHN
            'cod_amount' => $data['CODAmount'] ?? $shipment->cod_amount,
            'total_fee' => $data['TotalFee'] ?? $shipment->total_fee,
            'updated_at' => now(),
        ]);

        // Log lại sau khi cập nhật
        Log::info('GHN Order Updated:', ['order_code' => $data['OrderCode'], 'status' => $data['Status']]);

        return response()->json(['message' => 'Webhook xử lý thành công'], 200);
    }
}
