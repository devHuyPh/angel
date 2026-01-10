<?php

namespace App\Http\Controllers\Client;
use App\Models\CusTomer;
use App\Http\Controllers\Controller;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\DiscountCustomer;
use App\Models\KycRewardHistory;

use App\Models\KycForm;
use App\Models\KycLog;
use App\Models\PendingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;

class KycCustomerController extends Controller
{
   public function __construct()
  {
    $version = get_cms_version();

    Theme::asset()
      ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css', ['bootstrap-css'], version: $version);

    Theme::asset()
      ->add('front-ecommerce-css', 'vendor/core/plugins/ecommerce/css/front-ecommerce.css', version: $version);

    Theme::asset()
      ->container('footer')
      ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'], version: $version)
      ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'], version: $version)
      ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery'], version: $version);
  }
  
    public function index()
    {
        $kycForms = KycForm::all();
        $customerId = Auth::guard('customer')->id();

   		$customer=Customer::where('id',$customerId)->with('rank')->first();
        // $kycs=PendingLog::with('customer')->where('customer_id',$customerId)->first();
        // dd($kycs);
        $kycPending = PendingLog::where('customer_id', Auth::guard('customer')->id())
            ->with('logs','customer')
            ->latest()
            ->first();
        return Theme::scope('kyccustomer.index', compact('kycForms', 'kycPending','customer'), 'kyccustomer.index')->render();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function submit(Request $request)
{
    $kycPending = PendingLog::where('customer_id', Auth::guard('customer')->id())
        ->latest()
        ->first();

    if ($kycPending && in_array($kycPending->status, ['pending', 'approved'])) {
        return redirect()->back()->with('error', 'You cannot submit a new KYC request while your current request is ' . $kycPending->status . '.');
    }

    $kycForm = KycForm::findOrFail($request->kyc_form_id);
    $kycData = json_decode($kycForm->form, true);

    $rules = [];
    foreach ($kycData['field_name'] as $index => $fieldKey) {
        // Sử dụng key không dấu (field_name) cho validation
        $validation = $kycData['validation'][$index];
        $fieldType = $kycData['type'][$index];
        $fieldLength = $kycData['field_length'][$index];

        $rule = $validation;
        if ($fieldType === 'file') {
            $rule .= "|image|mimes:jpeg,png,jpg|max:{$fieldLength}";
        } else {
            $rule .= "|max:{$fieldLength}";
        }

        $rules["data.{$fieldKey}"] = $rule;
    }
    $rules['kyc_form_id'] = 'required|exists:kyc_forms,id';
    $validated = $request->validate($rules);

    $uploadPath = public_path('storage/kyc');
    $data = [];
    foreach ($kycData['field_name'] as $fieldKey) {
        if ($request->hasFile("data.{$fieldKey}")) {
            $file = $request->file("data.{$fieldKey}");
            $fileName = time() . '_' . $fieldKey . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $data[$fieldKey] = 'storage/kyc/' . $fileName;
        } else {
            $data[$fieldKey] = $request->input("data.{$fieldKey}");
        }
    }

    $kycPending = PendingLog::create([
        'customer_id' => Auth::guard('customer')->id(),
        'kyc_form_id' => $validated['kyc_form_id'],
        'name' => !empty($data['full_name']) ? $data['full_name'] : (Auth::guard('customer')->check() ? Auth::guard('customer')->user()->name : 'N/A'),
        'verification_type' => $kycForm->name,
        'data' => json_encode($data),
        'status' => 'pending',
    ]);

    KycLog::create([
        'kyc_pending_id' => $kycPending->id,
        'kyc_pending_name' => $kycPending->name,
        'kyc_verification_type' => $kycPending->verification_type,
        'kyc_status' => $kycPending->status,
        'customer_id' => Auth::guard('customer')->id(),
        'customer_name' => Auth::guard('customer')->user()->name,
        'customer_email' => Auth::guard('customer')->user()->email,
        'customer_phone' => Auth::guard('customer')->user()->phone ?? null,
        'action' => 'submitted',
        'affected_entity' => 'customer',
        'affected_entity_id' => Auth::guard('customer')->id(),
        'system_notification' => 0,
        'data_before' => null,
        'data_after' => json_encode($data),
        'note' => 'Yêu cầu KYC đã được khách hàng gửi.',
        'action_at' => now(),
    ]);
	 $teleMessage = "<b>🔔 Thông Báo Xác Thực Tài Khoản</b>\n"
            . "👤 <b>" . Auth::guard('customer')->user()->fullname . "</b> (<i>" . Auth::guard('customer')->user()->email . "</i>) "
            . "vừa gửi yêu cầu xác thực tài khoản.\n"
            . "📅 Thời gian: " . now()->format('d/m/Y H:i') . "\n"
            . "🔍 Vui lòng kiểm tra và xử lý yêu cầu trong hệ thống.";

        \App\Helpers\TelegramHelper::sendMessage($teleMessage,'kyc');
    return redirect()->route('kyc.index')->with('success', 'Your KYC request has been submitted successfully.');
}
    public function reward(){
        $customer=Auth::guard('customer')->user()->id;
        $rewards=KycRewardHistory::where('customer_id',$customer)->paginate(10);
        // dd($reward);
        return view('kyccustomer.referralbonus',compact('rewards'));
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
