<?php

namespace App\Http\Controllers\Admin;

use Botble\Setting\Supports\SettingStore;
use Botble\Base\Http\Responses\BaseHttpResponse;
use App\Http\Controllers\Controller;
use App\Models\ReferralCommission;
use App\Models\StoreOrder;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class ReferralCommissionController extends BaseController
{
    const PATH_WEB = 'admin.referral.';
    public function index()
    {
        $warehouse = setting('warehouse-referral-commission');
        $direct = setting('direct-referral-commission');
        $indirect = setting('indirect-referral-commission');
        $wallet_fee = setting('wallet_fee');
        $fixed_fees = setting('fixed_fees', 3000);
        $fee_dis_ware_to_mini = setting('distribution_warehouse_to_mini_warehouse');
        $fee_gen_ware_to_mini = setting('general_warehouse_to_mini_warehouse');
        $fee_mini_ware_to_customer = setting('fee_mini_warehouse_customer');
        $fee_dis_ware_to_customer = setting('fee_distribution_warehouse_to_customer');
        $fee_gen_ware_to_customer = setting('fee_general_warehouse_to_customer');
        $monthly_repurchase = setting('monthly_repurchase');
        $auto_confirmation_time=setting('auto-confirmation-time');
        $minimum_withdrawal_amount_per_customer=setting('minimum-withdrawal-amount-per-customer');
        $wallet_min_amount = setting('wallet_min_amount', 0);
        return view(self::PATH_WEB . __FUNCTION__, compact('warehouse','direct', 'indirect', 'wallet_fee', 'fixed_fees', 'fee_dis_ware_to_mini',
         'fee_gen_ware_to_mini', 'fee_mini_ware_to_customer', 'fee_dis_ware_to_customer', 
         'fee_gen_ware_to_customer', 'monthly_repurchase','auto_confirmation_time','minimum_withdrawal_amount_per_customer','wallet_min_amount'));
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editreferral()
    {
        $warehouse = setting('warehouse-referral-commission');
        $direct = setting('direct-referral-commission');
        $indirect = setting('indirect-referral-commission');
        $wallet_fee = setting('wallet_fee');
        $fixed_fees = setting('fixed_fees', 3000);
        $fee_dis_ware_to_mini = setting('distribution_warehouse_to_mini_warehouse');
        $fee_gen_ware_to_mini = setting('general_warehouse_to_mini_warehouse');
        $fee_dis_ware_to_customer = setting('fee_distribution_warehouse_to_customer');
        $fee_gen_ware_to_customer = setting('fee_general_warehouse_to_customer');
        $monthly_repurchase = setting('monthly_repurchase');
        $auto_confirmation_time=setting('auto-confirmation-time');
        $minimum_withdrawal_amount_per_customer=setting('minimum-withdrawal-amount-per-customer');
        $wallet_min_amount = setting('wallet_min_amount', 0);
        return view(self::PATH_WEB . __FUNCTION__, compact('warehouse','direct', 'indirect', 'wallet_fee', 'fixed_fees', 
        'fee_dis_ware_to_mini', 'fee_gen_ware_to_mini', 'fee_dis_ware_to_customer', 
        'fee_gen_ware_to_customer', 'monthly_repurchase','auto_confirmation_time','minimum_withdrawal_amount_per_customer','wallet_min_amount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        $request->validate([
            'warehouse'=> 'required|max:255', 
            'direct' => 'required|max:255',
            'indirect' => 'required',
            'wallet_fee' => 'required',
            'fixed_fees' => 'required',
            // 'fee_dis_ware_to_mini' => 'required',
            // 'fee_gen_ware_to_mini' => 'required',
            // 'fee_mini_ware_to_customer' => 'required',
            'fee_dis_ware_to_customer' => 'required',
            // 'fee_gen_ware_to_customer' => 'required',
            'monthly_repurchase' => 'required',
            'auto_confirmation_time'=>'required',
            'minimum_withdrawal_amount_per_customer'=>'required',
            'wallet_min_amount' => 'nullable|numeric|min:0'
        ]);
        // dd($request->all());
        $settingKey = ['warehouse-referral-commission','direct-referral-commission', 'indirect-referral-commission', 
        'wallet_fee', 'fixed_fees', 'fee_distribution_warehouse_to_customer', 
        'monthly_repurchase','auto-confirmation-time','minimum-withdrawal-amount-per-customer','wallet_min_amount'];
        $settingValue = [
            $request->warehouse,
            $request->direct,
            $request->indirect,
            $request->wallet_fee,
            $request->fixed_fees,
            // $request->fee_dis_ware_to_mini,
            // $request->fee_gen_ware_to_mmini//            $request->fee_mini_ware_to_customer,
            $request->fee_dis_ware_to_customer,
            // $request->fee_gen_ware_to_customer,
            $request->monthly_repurchase,
            $request->auto_confirmation_time,
            $request->minimum_withdrawal_amount_per_customer,
            $request->wallet_min_amount
        ];
        foreach ($settingKey as $index => $key) {
            setting()->set($key, $settingValue[$index]);
        }

        setting()->save();

        return redirect()->route('referralcommission.index')->with('success', 'Cài đặt giới thiệu đã được cập nhật thành công');
    }
    public function indexActiveAccount()
    {
        $activeAcount = setting('active_account');
        return view('active_account.index', compact('activeAcount'));
    }
    public function editActiveAccount()
    {
        $activeAcount = setting('active_account');
        return view('active_account.edit', compact('activeAcount'));
    }
    public function updateActiveAccount(Request $request)
    {
        $request->validate([
            'active_account' => 'required',
        ]);
        $activeAccount = $request->input('active_account');
        setting()->set('active_account', $activeAccount);
        setting()->save();
        return redirect()->route('active_account.index')->with('success', 'Cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function list()
    {
        $orders = ReferralCommission::select('order_id')
            ->groupBy('order_id')
            ->with([
                'order:id,code,created_at', // Lấy thêm thông tin đơn hàng
            ])
            ->withCount('orderCommissions')
            ->orderByDesc('order_id')
            ->paginate(20);

        // dd($orders);
        return view('admin.referral-commissions.index', compact('orders'));
    }

    public function detail($id)
    {
        $commissions = ReferralCommission::where('order_id', $id)
            ->with(['customer', 'order'])
            ->orderBy('level')
            ->get();

        return view('admin.referral-commissions.detail', compact('commissions', 'id'));
    }
}
