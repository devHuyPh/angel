<?php

namespace App\Http\Controllers\Admin;
use Botble\Setting\Supports\SettingStore;
use App\Models\DailyBonusLog;
use App\Models\CusTomer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DistributeDailyOrderBonusController extends Controller
{
    public function index()
{
    $dailyBonus = setting('bonus_percentage');

    $dailyBonusLogs = DailyBonusLog::with('customer')
        ->whereHas('customer')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('dailybonusorder.index', compact('dailyBonus', 'dailyBonusLogs'));
}

    public function edit(){
        $dailyBonus=setting('bonus_percentage');
        return view('dailybonusorder.edit',compact('dailyBonus'));
    }

    public function update(Request $request){
        $request->validate([
            'bonus_percentage' => 'required|numeric|min:0|max:100',
        ]);
        $bonusPercentage = $request->input('bonus_percentage');
        setting()->set('bonus_percentage', $bonusPercentage);
        setting()->save();
        return redirect()->route('dailybonusorder.index')->with('success', 'Cập nhật thành công');
    }
	

    public function customerview($id){
        $customer=Customer::where('id',$id)->first();
        return view('dailybonusorder.customerview',compact('customer'));
    }
}
