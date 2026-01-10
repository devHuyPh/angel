<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CusTomer;
use Botble\Ecommerce\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class CustomerController extends BaseController
{
    public function updateDowlineAndRank($id)
    {
        $customer = CusTomer::findOrFail($id);

        // Cập nhật total_dowline
        $customer->updateTotalDowline();

        // Gán rank
        $rankName = $customer->assignRank();

        return response()->json([
            'message' => 'Dowline and rank updated successfully',
            'customer' => $customer->name,
            'total_dowline' => $customer->total_dowline,
            'rank' => $rankName,
        ]);
    }

   public function show($id)
    {
        $customer = Customer::with('downline', 'orders')->findOrFail($id);
        $rankName = $customer->assignRank(); // Chỉ để lấy tên rank hiển thị, không cần cập nhật vì Observer đã xử lý

        return view('customers.show', compact('customer', 'rankName'));
    }
    // Cập nhật tất cả customers
    public function updateAll()
    {
        $customers = CusTomer::all();
        foreach ($customers as $customer) {
            $customer->updateTotalDowline();
            $customer->assignRank();
        }

        return redirect()->route('customers.index')->with('success', 'All customers updated');
    }
}
