<?php

namespace App\Http\Controllers\Admin;

use App\Models\KycLog;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\DiscountCustomer;
use App\Models\KycRewardHistory;
use App\Http\Controllers\Controller;
use App\Models\CusTomer;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use App\Models\KycForm;
use App\Models\PendingLog;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KycController extends BaseController
{
    function convertToSlug($string) {
        // Chuyển đổi thành không dấu và thay khoảng trắng thành dấu "_"
        return Str::slug($string, '_');
    }
    public function reward()
    {
        $this->pageTitle(trans('core/dashboard::dashboard.reward'));
        $type = setting('type_kyc_success');
        $value = setting('value_kyc');
        return view('admin.kyc.indexreward', compact('type', 'value'));
    }
    public function rewardupdate()
    {
        $this->pageTitle(trans('core/dashboard::dashboard.update_reward'));
        $type = setting('type_kyc_success');
        $value = setting('value_kyc');
        return view('admin.kyc.updatereward', compact('type', 'value'));
    }


    public function rewardstore(Request $request)
    {
        $request->validate([
            'keys' => 'required',
            'values' => 'required',
        ]);
        $settingKey = ['type_kyc_success', 'value_kyc'];
        if ($request->keys == 'money') {
            $settingValue = [
                $request->keys,
                $request->values
            ];
            foreach ($settingKey as $index => $key) {
                setting()->set($key, $settingValue[$index]);
            }
            setting()->save();

            return redirect()->route('kyc.reward');
        }
        if ($request->keys == 'discount') {

            // $discount=Discount::where('type_option','percentage')
            // ->where('type','coupon')
            // ->where('value',$request->values)
            // ->where('iskyc',1)->first();
            // if(!$discount){
            //     // Discount::where('iskyc', 1)->delete();
            // $discount = Discount::create([
            //     'title' => 'Giảm  ' . $request->values, 
            //     'code' => 'KYC_' . strtoupper(uniqid()), 
            //     'start_date' => now(), 
            //     'end_date' => null,
            //     'quantity' => 1, 
            //     'total_used' => 0, 
            //     'value' => $request->values, 
            //     'type' => 'coupon', 
            //     'type_option' => 'percentage', 
            //     'can_use_with_promotion' => 0, 
            //     'can_use_with_flash_sale' => 0,
            //     'discount_on' => null,
            //     'product_quantity' => null, 
            //     'target' => 'customer', 
            //     'min_order_price' => null,
            //     'apply_via_url' => 0, 
            //     'display_at_checkout' => 1, 
            //     'store_id' => null, 
            //     'description' => 'Mã giảm giá xác thực KYC',
            //     'iskyc'=>1
            // ]);
            $settingKey = ['type_kyc_success', 'value_kyc'];
            $settingValue = [
                $request->keys,
                $request->values
            ];
            foreach ($settingKey as $index => $key) {
                setting()->set($key, $settingValue[$index]);
            }
            setting()->save();

            return redirect()->route('kyc.reward');
        }
    }
    public function identityForm()
    {

        $this->pageTitle(trans('core/dashboard::dashboard.title_identity'));
        
        $kycForms = new KycForm();
        $data = $kycForms->get();
        $address_v = setting('address_verification');
        $identity_v = setting('identity_verification');
        return view('admin.kyc.indexkyc', compact('data', 'address_v', 'identity_v'));
    }

    public function storeIdentityForm(Request $request, BaseHttpResponse $response)
{
    $data = $request->all();

    // Tên trường gốc (có dấu)
    $fieldNamesOriginal = $data['field_name'] ?? [];

    // Chuyển đổi key field_name thành không dấu
    $fieldNames = array_map(function ($name) {
        return Str::slug($name, '_'); // Chuyển tiếng Việt thành không dấu, phân cách bằng "_"
    }, $fieldNamesOriginal);

    // Lưu cả key không dấu và tên gốc
    $formData = json_encode([
        'field_name'   => $fieldNames, // Key không dấu để sử dụng trong validation
        'field_labels' => $fieldNamesOriginal, // Tên gốc để hiển thị
        'type'         => $data['type'] ?? [],
        'field_length' => $data['field_length'] ?? [],
        'length_type'  => $data['length_type'] ?? [],
        'validation'   => $data['validation'] ?? [],
    ]);

    $kycForm = new KycForm();
    $kycForm->name = $data['name'] ?? null;
    $kycForm->status = $data['status'] ?? 0;
    $kycForm->form = $formData;
    $create_form = $kycForm->save();

    if (!$create_form) {
        return $this
            ->httpResponse()
            ->setError()
            ->setMessage(__('Create form error'));
    }

    return $this
        ->httpResponse()
        ->setMessage(__('Created form'));
}
    public function update(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    {

        $settingKey = ['address_verification', 'identity_verification'];
        $settingValue = [
            $request->address_v,
            $request->identity_v
        ];
        foreach ($settingKey as $index => $key) {
            setting()->set($key, $settingValue[$index]);
        }

        setting()->save();

        return redirect()->route('kyc.form')->with('success', 'Cài đặt địa chỉ thành công');
    }

    public function updateIdentityForm(Request $request, BaseHttpResponse $response, $id)
    {
        $data = $request->all();

        // Lấy danh sách field_name và tạo field_labels
        $fieldNames = $data['field_name_' . $id] ?? [];
        $fieldLabels = $fieldNames; // Lưu tên gốc có dấu vào field_labels
        $fieldNames = array_map(function ($name) {
            // Chuyển tên có dấu thành không dấu để làm key
            $key = Str::slug($name, '_'); // Ví dụ: "Địa Chỉ" -> "dia_chi"
            // Đảm bảo key không chứa ký tự đặc biệt và chỉ có chữ thường
            $key = preg_replace('/[^a-z0-9_]/', '', $key);
            return $key;
        }, $fieldNames);

        // Chuẩn bị dữ liệu JSON để lưu vào cột form
        $formData = json_encode([
            'field_name' => $fieldNames, // Key không dấu: ['ho_va_ten', 'dia_chi', ...]
            'field_labels' => $fieldLabels, // Tên gốc có dấu: ['Họ và Tên', 'Địa Chỉ', ...]
            'type' => $data['type_' . $id] ?? [],
            'field_length' => $data['field_length_' . $id] ?? [],
            'length_type' => $data['length_type_' . $id] ?? [],
            'validation' => $data['validation_' . $id] ?? [],
        ]);

        // Tìm và cập nhật KycForm
        $kycForm = KycForm::find($id);

        if ($kycForm) {
            $update_form = $kycForm->update([
                'name' => $data['name'] ?? null,
                'status' => $data['status'] ?? 0,
                'form' => $formData,
            ]);

            if (!$update_form) {
                return $response
                    ->setError()
                    ->setMessage(__('Update form error'));
            }

            return $response
                ->setMessage(__('Updated form successfully'));
        }

        return $response
            ->setError()
            ->setMessage(__('Form not found'));
    }

    public function deleteIdentityForm($id, BaseHttpResponse $response)
    {
        $kycForm = KycForm::find($id);
    
        if (!$kycForm) {
            return redirect()->route('kyc.form')
                ->with('error', __('Form not found'));
        }
    
        $delete_form = $kycForm->delete();
    
        if (!$delete_form) {
            return redirect()->route('kyc.form')
                ->with('error', __('Delete form error'));
        }
    
        return redirect()->route('kyc.form')
            ->with('success', __('Deleted form successfully'));
    }

    public function showPending(Request $request)
    {
        $this->pageTitle(trans('core/dashboard::dashboard.show_pending'));
        $query = $request->input('search');
        $pendings = PendingLog::with('customer')->where('status', 'pending')
            ->when($query, function ($q) use ($query) {
                return $q->where('name', 'like', "%$query%")
                    ->orWhere('verification_type', 'like', "%$query%");
            })
            ->paginate(5);
            $kycForms = KycForm::all()->keyBy('id')->map(function ($form) {
                $formData = json_decode($form->form, true);
                return [
                    'field_name' => $formData['field_name'] ?? [],
                    'field_labels' => $formData['field_labels'] ?? $formData['field_name'] ?? [],
                ];
            });        $this->pageTitle(trans('core/dashboard::dashboard.title_kyc_pending'));
        return view('admin.kyc.pending_kyc', compact('pendings','kycForms'));

        // $this->pageTitle('Identity Form');


    }

    public function logs(Request $request)
    {
        $this->pageTitle(trans('core/dashboard::dashboard.logs'));
        $query = $request->input('search');
        $logs = KycLog::with(['kycPending', 'admin', 'customer'])
            ->when($query, function ($q) use ($query) {
                return $q->where('action', 'like', "%$query%")
                    ->orWhere('note', 'like', "%$query%")
                    ->orWhere('reason', 'like', "%$query%")
                    ->orWhere('customer_name', 'like', "%$query%")
                    ->orWhere('customer_email', 'like', "%$query%")
                    ->orWhere('kyc_pending_name', 'like', "%$query%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kyc.kyc_log', compact('logs'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:ec_customers,id',
            'kyc_form_id' => 'required|exists:kyc_forms,id',
            'name' => 'required|string|max:255',
            'verification_type' => 'required|string|max:255',
            'data' => 'required|array',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        $pending = PendingLog::create([
            'customer_id' => $validated['customer_id'],
            'kyc_form_id' => $validated['kyc_form_id'],
            'name' => $validated['name'],
            'avatar' => $customer->avatar,
            'verification_type' => $validated['verification_type'],
            'data' => $validated['data'],
            'status' => 'pending',
        ]);

        KycLog::create([
            'kyc_pending_id' => $pending->id,
            'kyc_pending_name' => $pending->name,
            'kyc_verification_type' => $pending->verification_type,
            'kyc_status' => $pending->status,
            'admin_id' => null,
            'admin_name' => null,
            'admin_email' => null,
            'customer_id' => $validated['customer_id'],
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_status' => $customer->status ?? 'active',
            'action' => 'submitted',
            'affected_entity' => 'customer',
            'affected_entity_id' => $customer->id,
            'system_notification' => false,
            'data_before' => null,
            'data_after' => $pending->data,
            'note' => 'KYC request submitted by customer.',
            'reason' => null,
            'action_at' => now(),
        ]);

        return redirect()->route('kyc.pending')->with('success', 'KYC request submitted successfully.');
    }
    public function pendingview($id)
    {
        $this->pageTitle(trans('core/dashboard::dashboard.pending_view'));
        $pending = PendingLog::with('customer')->findOrFail($id);
    
        // Lấy KycForm tương ứng với kyc_form_id
        $kycForm = KycForm::find($pending->kyc_form_id);
        $formData = $kycForm ? json_decode($kycForm->form, true) : [];
        $fieldNames = $formData['field_name'] ?? [];
        $fieldLabels = $formData['field_labels'] ?? $fieldNames;
    
        $this->pageTitle(trans('core/dashboard::dashboard.title_kyc_pending'));
        return view('admin.kyc.pending_view', compact('pending', 'fieldNames', 'fieldLabels'));
    }
    // public function pendingapprove($id){
    //     $pending = PendingLog::with('customer')->findOrFail($id);
    //     $admin = Auth::user();
    //     $dataBefore = $pending->data;
    //     // dd($pending->customer->referral_ids);
    //     $pending->update([
    //         'status' => 'approved',
    //     ]);
    //     $pending->customer->update([
    //         'kyc_status'=>1
    //     ]);

    //     if($pending->fresh()->status == 'approved'){
    //         if(setting('type_kyc_success')=='money'){
    //            $addMoney=$pending->customer->walet_2;
    //            $addMoney+=setting('value_kyc');
    //            $pending->customer->update([
    //                'walet_2' => $addMoney,
    //            ]);
    //            $kycRewardHistory=KycRewardHistory::create([
    //             'customer_id'=>$pending->customer->id,
    //             'reward_type'=>setting('type_kyc_success'),
    //             'reward_value'=>setting('value_kyc'),
    //             'description'=>'Được + '.setting('value_kyc').' từ xác nhận KYC.',

    //         ]);
    //            if($pending->customer->referral_ids==null){

    //            }
    //            else{
    //             $customerReferral=Customer::where('id',$pending->customer->referral_ids)->first();
    //             // dd($customerReferral);
    //             $addMoneyReferral=$customerReferral->walet_2 + setting('value_kyc');

    //             $customerReferral->update([
    //                 'walet_2' => $addMoneyReferral,
    //             ]);
    //             $kycRewardHistory=KycRewardHistory::create([
    //                 'customer_id'=>$pending->customer->referral_ids,
    //                 'reward_type'=>setting('type_kyc_success'),
    //                 'reward_value'=>setting('value_kyc'),
    //                 'description'=>'Được + '.setting('value_kyc').' từ giới thiệu xác nhận KYC.',

    //             ]);

    //            }

    //         } 
    //         if(setting('type_kyc_success')=='discount'){
    //              $discountValue=setting('value_kyc');
    //             //  $discount = Discount::where('value', $discountValue)
    //             //     ->where('type_option', 'percentage')
    //             //     ->where('type', 'coupon')
    //             //     ->whereRaw('LOWER(code) LIKE LOWER(?)', ['%KYC%'])
    //             //     ->first();
    //                 // dd($discount);
    //             // $discountCustomer=DiscountCustomer::create([
    //             //     'discount_id'=>$discount->id,
    //             //     'customer_id'=>$pending->customer->id
    //             // ]);
    //             // $discountCustomer->save();
    //             $discount = Discount::create([
    //                     'title' => 'Giảm  ' . $discountValue, 
    //                     'code' => 'KYC_' . strtoupper(uniqid()), 
    //                     'start_date' => now(), 
    //                     'end_date' => null,
    //                     'quantity' => 1, 
    //                     'total_used' => 0, 
    //                     'value' => $discountValue, 
    //                     'type' => 'coupon', 
    //                     'type_option' => 'percentage', 
    //                     'can_use_with_promotion' => 0, 
    //                     'can_use_with_flash_sale' => 0,
    //                     'discount_on' => null,
    //                     'product_quantity' => null, 
    //                     'target' => 'customer', 
    //                     'min_order_price' => null,
    //                     'apply_via_url' => 0, 
    //                     'display_at_checkout' => 1, 
    //                     'store_id' => null, 
    //                     'description' => 'Mã giảm giá xác thực KYC',
    //                 ]);
    //                 $discountCustomer=DiscountCustomer::create([
    //                     'discount_id'=>$discount->id,
    //                     'customer_id'=>$pending->customer->id
    //                 ]);
    //                 $kycRewardHistory=KycRewardHistory::create([
    //                     'customer_id'=>$pending->customer->id,
    //                     'reward_type'=>setting('type_kyc_success'),
    //                     'reward_value'=>setting('value_kyc'),
    //                     'description'=>'Nhận mã giảm giá '.setting('value_kyc').' từ giới thiệu xác nhận KYC.',

    //                 ]);
    //             if($pending->customer->referral_ids==null){

    //             }else{
    //                 $discountReferral=Discount::create([
    //                     'title' => 'Giảm  ' . $discountValue, 
    //                         'code' => 'KYC_GT' . strtoupper(uniqid()), 
    //                         'start_date' => now(), 
    //                         'end_date' => null,
    //                         'quantity' => 1, 
    //                         'total_used' => 0, 
    //                         'value' => $discountValue, 
    //                         'type' => 'coupon', 
    //                         'type_option' => 'percentage', 
    //                         'can_use_with_promotion' => 0, 
    //                         'can_use_with_flash_sale' => 0,
    //                         'discount_on' => null,
    //                         'product_quantity' => null, 
    //                         'target' => 'customer', 
    //                         'min_order_price' => null,
    //                         'apply_via_url' => 0, 
    //                         'display_at_checkout' => 1, 
    //                         'store_id' => null, 
    //                         'description' => 'Mã giảm giá xác thực KYC người giới thiệu',
    //                 ]);
    //                     $kycRewardHistory=KycRewardHistory::create([
    //                         'customer_id'=>$pending->customer->referral_ids,
    //                         'reward_type'=>setting('type_kyc_success'),
    //                         'reward_value'=>setting('value_kyc'),
    //                         'description'=>'Nhận mã giảm giá '.setting('value_kyc').' từ giới thiệu xác nhận KYC.',

    //                     ]);

    //                 $discountReferrals=DiscountCustomer::create([
    //                         'discount_id'=>$discountReferral->id,
    //                         'customer_id'=>$pending->customer->referral_ids
    //                 ]);
    //             }


    //         }
    //     }
    //     KycLog::create([
    //         'kyc_pending_id' => $pending->id,
    //         'kyc_pending_name' => $pending->name,
    //         'kyc_verification_type' => $pending->verification_type,
    //         'kyc_status' => $pending->status,
    //         'admin_id' => $admin->id,
    //         'admin_name' => $admin->name,
    //         'admin_email' => $admin->email,
    //         'customer_id' => $pending->customer_id,
    //         'customer_name' => $pending->customer->name,
    //         'customer_email' => $pending->customer->email,
    //         'customer_phone' => $pending->customer->phone,
    //         'action' => 'approved',
    //         'affected_entity' => 'customer',
    //         'affected_entity_id' => $pending->customer_id,
    //         'system_notification' => true,
    //         'data_before' => $dataBefore,
    //         'data_after' => $pending->data,
    //         'note' => 'Đã phê duyệt sau khi xác minh tất cả tài liệu.',
    //         'reason' => null,
    //         'action_at' => now(),
    //     ]);


    //     return redirect()->route('kyc.pending')->with('success', 'KYC request approved successfully.');
    // }


    public function pendingapprove($id)
    {
        $pending = PendingLog::with('customer')->findOrFail($id);
        $admin = Auth::user();
        $dataBefore = $pending->data;

        if ($pending->status === 'approved') {
            return redirect()->route('kyc.pending')->with('info', 'KYC request is already approved.');
        }

        DB::transaction(function () use ($pending, $admin, $dataBefore) {
            $pending->update(['status' => 'approved']);
            $pending->customer->update(['kyc_status' => 1]);

//             if (setting('type_kyc_success') == 'money') {
//                 $pending->customer->increment('walet_2', setting('value_kyc'));
//                 KycRewardHistory::create([
//                     'customer_id' => $pending->customer->id,
//                     'reward_type' => 'money',
//                     'reward_value' => setting('value_kyc'),
//                     'description' => 'Được + ' . setting('value_kyc') . ' từ xác nhận KYC.',
//                 ]);

//                 if (!empty($pending->customer->referral_ids)) {
//                     $customerReferral = Customer::find($pending->customer->referral_ids);
//                     if ($customerReferral) {
//                         $customerReferral->increment('walet_2', setting('value_kyc'));
//                         KycRewardHistory::create([
//                             'customer_id' => $customerReferral->id,
//                             'reward_type' => 'money',
//                             'reward_value' => setting('value_kyc'),
//                             'description' => 'Được + ' . setting('value_kyc') . ' từ giới thiệu xác nhận KYC.',
//                         ]);
//                     }
//                 }
//             } elseif (setting('type_kyc_success') == 'discount') {
//                 $discountValue = setting('value_kyc');
//                 $discount = Discount::create([
//                     'title' => 'Giảm ' . $discountValue,
//                     'code' => 'KYC_' . strtoupper(uniqid()),
//                     'start_date' => now(),
//                     'end_date' => null,
//                     'quantity' => 1,
//                     'total_used' => 0,
//                     'value' => $discountValue,
//                     'type' => 'coupon',
//                     'type_option' => 'percentage',
//                     'can_use_with_promotion' => 0,
//                     'can_use_with_flash_sale' => 0,
//                     'discount_on' => null,
//                     'product_quantity' => null,
//                     'target' => 'customer',
//                     'min_order_price' => null,
//                     'apply_via_url' => 0,
//                     'display_at_checkout' => 1,
//                     'store_id' => null,
//                     'description' => 'Mã giảm giá xác thực KYC',
//                 ]);
//                 DiscountCustomer::create([
//                     'discount_id' => $discount->id,
//                     'customer_id' => $pending->customer->id
//                 ]);
//                 KycRewardHistory::create([
//                     'customer_id' => $pending->customer->id,
//                     'reward_type' => 'discount',
//                     'reward_value' => $discountValue,
//                     'description' => 'Nhận mã giảm giá ' . $discountValue . '% từ xác nhận KYC.',
//                 ]);

//                 if (!empty($pending->customer->referral_ids)) {
//                     $customerReferral = Customer::find($pending->customer->referral_ids);
//                     if ($customerReferral) {
//                         $discountReferral = Discount::create([
//                             'title' => 'Giảm ' . $discountValue,
//                             'code' => 'KYC_GT' . strtoupper(uniqid()),
//                             'start_date' => now(),
//                             'end_date' => null,
//                             'quantity' => 1,
//                             'total_used' => 0,
//                             'value' => $discountValue,
//                             'type' => 'coupon',
//                             'type_option' => 'percentage',
//                             'can_use_with_promotion' => 0,
//                             'can_use_with_flash_sale' => 0,
//                             'discount_on' => null,
//                             'product_quantity' => null,
//                             'target' => 'customer',
//                             'min_order_price' => null,
//                             'apply_via_url' => 0,
//                             'display_at_checkout' => 1,
//                             'store_id' => null,
//                             'description' => 'Mã giảm giá xác thực KYC người giới thiệu',
//                         ]);
//                         DiscountCustomer::create([
//                             'discount_id' => $discountReferral->id,
//                             'customer_id' => $customerReferral->id
//                         ]);
//                         KycRewardHistory::create([
//                             'customer_id' => $customerReferral->id,
//                             'reward_type' => 'discount',
//                             'reward_value' => $discountValue,
//                             'description' => 'Nhận mã giảm giá ' . $discountValue . '% từ giới thiệu xác nhận KYC.',
//                         ]);
//                     }
//                 }
//             }

            KycLog::create([
                'kyc_pending_id' => $pending->id,
                'kyc_pending_name' => $pending->name,
                'kyc_verification_type' => $pending->verification_type,
                'kyc_status' => 'approved',
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'admin_email' => $admin->email,
                'customer_id' => $pending->customer_id,
                'customer_name' => $pending->customer->name,
                'customer_email' => $pending->customer->email,
                'customer_phone' => $pending->customer->phone,
                'action' => 'approved',
                'affected_entity' => 'customer',
                'affected_entity_id' => $pending->customer_id,
                'system_notification' => true,
                'data_before' => $dataBefore,
                'data_after' => $pending->data,
                'note' => 'Đã phê duyệt sau khi xác minh tất cả tài liệu.',
                'reason' => null,
                'action_at' => now(),
            ]);
        });

        return redirect()->route('kyc.pending')->with('success', 'KYC request approved successfully.');
    }

    public function pendingreject(Request $request, $id)
    {
        $pending = PendingLog::with('customer')->findOrFail($id);

        $admin = Auth::user();

        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $dataBefore = $pending->data;
        $pending->update([
            'status' => 'rejected',
        ]);

        KycLog::create([
            'kyc_pending_id' => $pending->id,
            'kyc_pending_name' => $pending->name,
            'kyc_verification_type' => $pending->verification_type,
            'kyc_status' => $pending->status,
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
            'customer_id' => $pending->customer_id,
            'customer_name' => $pending->customer->name,
            'customer_email' => $pending->customer->email,
            'customer_phone' => $pending->customer->phone,
            'action' => 'rejected',
            'affected_entity' => 'customer',
            'affected_entity_id' => $pending->customer_id,
            'system_notification' => true,
            'data_before' => $dataBefore,
            'data_after' => $pending->data,
            'note' => 'Bị từ chối do tài liệu không hợp lệ.',
            'reason' => $validated['reason'] ?? 'Thiếu tài liệu bắt buộc.',
            'action_at' => now(),
        ]);

        return redirect()->route('kyc.pending')->with('success', 'KYC request rejected successfully.');
    }

    public function view($id)
    {
        $this->pageTitle(trans('core/dashboard::dashboard.kyc_log_view'));
        $log = KycLog::with(['admin', 'customer', 'kycPending'])->findOrFail($id);
        return view('admin.kyc.kyc_log_view', compact('log'));
    }
    public function rewardget()
    {
         $this->pageTitle(trans('core/dashboard::dashboard.rewardget'));
        $rewards = KycRewardHistory::with('customer')->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.kyc.rewardhistory', compact('rewards'));
    }
    public function rewardview($id)
    {
        $this->pageTitle(trans('core/dashboard::dashboard.rewardview'));

        $reward = KycRewardHistory::with('customer')->findOrFail($id);
        // dd($reward->customer->referral_ids);
        $discountCustomer = DiscountCustomer::with('discount')->where('customer_id', $reward->customer_id)->get();
        $user = null;
        if (!empty($reward->customer->referral_ids)) {
            $user = Customer::where('id', $reward->customer->referral_ids)->first();
            // dd($user);
        }
        return view('admin.kyc.viewrewardhistory', compact('reward', 'discountCustomer', 'user'));
    }
}
