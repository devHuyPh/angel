<?php

namespace App\Http\Controllers\Admin;
use Botble\Setting\Supports\SettingStore;
use App\Models\Referral;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Botble\Ecommerce\Models\Customer;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Botble\Payment\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;


class ReferralController extends BaseController
{

    const PATH_VIEW='Referral.';
    // Hàm hiện trang referral
    public function index(){
        $data['referrals'] = Referral::get();
        return view(self::PATH_VIEW. __FUNCTION__, $data);
    }
    // Hàm save change checkbox referral
    public function save(Request $request, SettingStore $settingStore){
        $data = $request->except('_token');
        foreach ($data as $settingKey => $settingValue) {
            $settingStore->set($settingKey, $settingValue);
        }

        $settingStore->save();

        return back()->with('success', 'Form submitted successfully!');
    }

    public function action(Request $request, SettingStore $settingStore){
        $request->validate([
            'level*' => 'required|integer|min:1',
            'percent*' => 'required|numeric',
            'commission_type' => 'required',
        ]);

        Referral::where('commission_type', $request->commission_type)->delete();
        for ($i = 0; $i < count($request->level); $i++) {
            $referral = new Referral();
            $referral->commission_type = $request->commission_type;
            $referral->level = $request->level[$i];
            $referral->percent = $request->percent[$i];
            $referral->save();
        }

        return back()->with('success', 'Form submitted successfully!');
    }
   

  public function referrals()
  { 

    $rootUsers = Customer::getRootUsers();
    // $rootUsers = $this->sumRevenueOfF1();
    // dd($rootUsers);
//   dd($rootUsers);
    return view('Referral.referrals', [
      'rootUsers' => $rootUsers,
      'status' => !!$rootUsers,
      'level' => 0
  ]);
  }
  public function children_referrals(Request $request)
  {
    $parentId = $request->input('parent_id');
        $level = $request->input('level', 1);
        $data = Customer::getChildren($parentId, $level);
        // dd($data);
        if ($data['status']) {
            // dd($data);   
            return view('Referral.children_referrals', $data)->render();
        }
        return '';
  }
//   public function 
}
