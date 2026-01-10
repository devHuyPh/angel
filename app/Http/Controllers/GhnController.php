<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Botble\Setting\Supports\SettingStore;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Shipment;



class GhnController extends BaseController
{

    public function update(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        // dd('updateController');
        // Lọc dữ liệu chỉ lấy các cài đặt bắt đầu bằng 'shipping_ghn_'
        $data = Arr::where($request->except(['_token']), function ($value, $key) {
            return Str::startsWith($key, 'shipping_ghn_');
        });

        // Lưu cài đặt vào hệ thống
        foreach ($data as $settingKey => $settingValue) {
            $settingStore->set($settingKey, $settingValue);
        }

        // Lưu cài đặt
        $settingStore->save();

        // Xóa cache liên quan đến phí vận chuyển
        Cache::forget('shipping_ghn_cache_response');

        // Trả về phản hồi thành công
        return $response->setMessage(__('Cấu hình GHN đã được lưu!'));
    }

    public function updateSession(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'shipping_amount_inp' => 'required|numeric',
            'discount_amount_ipn' => 'required|numeric',
            'total_amount_ipn' => 'required|numeric',
            'discount_poin' => 'numeric',
        ]);

        session([
            'shipping_amount' => $request->get('shipping_amount_inp'),
            'discount_amount' => (float) $request->get('discount_amount_ipn') + (float) $request->get('discount_poin'),
            'amount' => $request->get('total_amount_ipn')
        ]);

        // $order->update([
        //     'shipping_amount' => $request->get('shipping_amount_inp'),
        //     'amount' => $request->get('total_amount_ipn')
        // ]);

        return response()->json(['success' => true, 'message' => 'Order updated successfully!']);
    }
    

    public function updateShipment(Request $request, $id, BaseHttpResponse $response) {
        logger('Dữ liệu nhận được từ AJAX:', $request->all());
    
        $request->validate([
            'ghn_created' => 'required|boolean',
            'ghn_code' => 'required|string',
            'estimate_date_shipped' => 'required|date',
        ]);
    
        $shipment_update = Shipment::query()->find($id);
        
        if (!$shipment_update) {
            logger('Lỗi: Không tìm thấy shipment có ID ' . $id);
            return $response->setError()->setMessage(__('Đơn giao hàng không tồn tại!'));
        }
    
        logger('Cập nhật shipment với dữ liệu:', [
            'ghn_created' => $request->ghn_created,
            'ghn_code' => $request->ghn_code,
            'estimate_date_shipped' => $request->estimate_date_shipped
        ]);
    
        try {
            $shipment_update->update([
                'ghn_created' => $request->ghn_created,
                'ghn_code' => $request->ghn_code,
                'estimate_date_shipped' => $request->estimate_date_shipped
            ]);
        } catch (\Exception $e) {
            logger('Lỗi khi cập nhật shipment: ' . $e->getMessage());
            return $response->setError()->setMessage(__('Lỗi khi cập nhật shipment!'));
        }

        $updatedShipment = Shipment::query()->find($id);

        return $response->setData($updatedShipment)->setMessage(__('Cập nhật đơn hàng thành công!'));

    }

    public function cancelOrder($id, BaseHttpResponse $response){
        $shipment_cancel = Shipment::query()->find($id);
        if (!$shipment_cancel) {
            logger('Lỗi: Không tìm thấy shipment có ID ' . $id);
            return $response->setError()->setMessage(__('Đơn giao hàng không tồn tại!'));
        }

        try {
            $shipment_cancel->update([
                'ghn_created' => false,
                'ghn_code' => null,
                'estimate_date_shipped' => null
            ]);
        } catch (\Exception $e) {
            logger('Lỗi khi cập nhật shipment: ' . $e->getMessage());
        }

        return $response->setMessage(__('Cấu hình GHN đã được lưu!'));
          
    }
    
    

}
