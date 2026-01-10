<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CustomerNotification;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Models\AdminNotification;
use Illuminate\Http\Request;
use Botble\Ecommerce\Models\Currency;
use App\Models\CustomerWithdrawal;
use App\Models\BankAccount;
use App\Models\Bank;
use Illuminate\Support\Facades\Hash;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;

class CustomerWithdrawalController extends BaseController
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customerCheck = auth('customer')->check();
        $customer = auth('customer')->user();
        $withdrawals = $customer->withdrawals()->orderBy('created_at', 'desc')->paginate(10);

        return Theme::scope('withdrawals.index', compact('withdrawals', 'customer'), 'withdrawals.index')->render();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customerCheck = auth('customer')->check();
        $customer = auth('customer')->user();
        $bankAccounts = $customer->bankAccounts;

        if ($bankAccounts->isEmpty()) {
            return redirect()
                ->route('bank_accounts.index')
                ->with('error_msg', __('Vui lòng liên kết tài khoản ngân hàng trước khi tạo lệnh rút tiền.'));
        }

        $defaultCurency = Currency::where('is_default', 1)->first();
        $currency = session('currency') ?? $defaultCurency->title;
        // dd($currency);

        $walletFeePercent = setting('wallet_fee', 10);
        $fixedFee = setting('fixed_fees', 3000);
        // dd($walletFeePercent);

        return Theme::scope(
            'withdrawals.create',
            compact('customer', 'bankAccounts', 'currency', 'walletFeePercent', 'fixedFee'),
            'withdrawals.create'
        )->render();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customer = auth('customer')->user();
        $bankAccounts = $customer->bankAccounts;

        if ($bankAccounts->isEmpty()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextRoute('bank_accounts.index')
                ->setMessage(__('Vui lòng liên kết tài khoản ngân hàng trước khi tạo lệnh rút tiền.'));
        }

        $defaultBankAccount = $bankAccounts->first();
        $prefix = $defaultBankAccount->payment_sepay_prefix ?? 'WD'; // ví dụ: NSGW
        $randomDigits = str_pad(random_int(0, 99999999999), 11, '0', STR_PAD_LEFT);
        $request->merge([
            'transaction_id' => $prefix . $randomDigits
        ]);

        $request->validate([
            'customer_id'        => 'required|exists:ec_customers,id',
            'amount'             => 'required|numeric|min:50000',
            'transaction_id'     => 'required|unique:customer_withdrawals,transaction_id',
            'currency'           => 'required|string|max:10',
            'bank_account'       => 'nullable',
            'new_bank_name'      => 'nullable|required_if:bank_account,new|string|max:255',
            'new_account_holder' => 'nullable|required_if:bank_account,new|string|max:255',
            'new_account_number' => 'nullable|required_if:bank_account,new|string|max:50|unique:bank_accounts,account_number,NULL,id,user_id,' . $request->customer_id,
        ]);

        $walletFeePercent = (float) setting('wallet_fee', 10);
        $fixedFee = (float) setting('fixed_fees', 3000);
        $withdrawalAmount = (float) $request->amount;
        $percentFee = ($walletFeePercent / 100) * $withdrawalAmount;
        $totalFee = $percentFee + $fixedFee;
        $amountAfterFee = $withdrawalAmount - $totalFee;
        $totalDeduct = $withdrawalAmount;

        if ($customer->walet_1 < $totalDeduct) {
          return $this->httpResponse()
            ->setError()
            ->setMessage('Số dư không đủ. Bạn cần ít nhất ' . number_format($totalDeduct) . '₫ để rút ' . number_format($amountAfterFee) . '₫ (sau khi trừ ' . $walletFeePercent . '% thuế/phí và ' . number_format($fixedFee) . '₫ phí cố định).');
        }

        // Xử lý tài khoản ngân hàng
        if ($request->bank_account !== 'new') {
            $bankAccount = BankAccount::find($request->bank_account);
            if (!$bankAccount) {
                return $this->httpResponse()->setError()->setMessage('Tài khoản ngân hàng không tồn tại.');
            }

            $bank_name = $bankAccount->bank_name;
            $bank_code = $bankAccount->bank_code;
            $account_holder = $bankAccount->account_holder;
            $account_number = $bankAccount->account_number;
            $branch = $bankAccount->branch;
            $swift_code = $bankAccount->swift_code;
        } else {
            $bank_name = $request->new_bank_name;
            $bank_code = $request->new_bank_code;
            $account_holder = $request->new_account_holder;
            $account_number = $request->new_account_number;
            $branch = $request->branch ?? null;
            $swift_code = $request->swift_code ?? null;

            BankAccount::create([
                'user_id'        => $request->customer_id,
                'bank_name'      => $bank_name,
                'bank_code'      => $bank_code,
                'account_number' => $account_number,
                'account_holder' => $account_holder,
                'branch'         => $branch,
                'swift_code'     => $swift_code,
            ]);
        }

        $withdrawal = CustomerWithdrawal::create([
            'customer_id'       => $request->customer_id,
            'amount'            => $amountAfterFee,
            'fee'               => $totalFee,
            'currency'          => $request->currency,
            'status'            => 'pending',
            'transaction_id'    => $request->transaction_id,
            'withdrawal_method' => 'bank_transfer',
            'account_name'      => $account_holder,
            'account_number'    => $account_number,
            'bank_name'         => $bank_name,
            'bank_code'         => $bank_code,
            'bank_branch'       => $branch,
            'swift_code'        => $swift_code,
            'notes'             => $request->description,
            'admin_id'          => null,
            'processed_at'      => null,
        ]);

        if (!$withdrawal) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('core/base::layouts.add_new_withdrawal') . ' ' . trans('core/base::layouts.failed'));
        }

        // Gửi thông báo Telegram
        $teleMessage = "<b>🔔 Yêu cầu rút tiền mới</b>\n"
            . "👤 Khách hàng: <b>" . $customer->name . "</b>\n"
            . "💵 Số tiền: <b>" . format_price($withdrawalAmount) . "</b>\n"
            . "🏦 Ngân hàng: <b>" . $bank_name . "</b>\n"
            . "👛 Số tài khoản: <b>" . $account_number . "</b>\n"
            . "🧾 Mã giao dịch: <code>" . $request->transaction_id . "</code>\n"
            . "⏱️ Thời gian: <i>" . now()->format('H:i d/m/Y') . "</i>";

        \App\Helpers\TelegramHelper::sendMessage($teleMessage);

        // Thông báo khách hàng
        CustomerNotification::create([
            'title' => 'core/base::layouts.your_withdrawal_sended',
            'dessription' => 'withdrawal_request_submitted_description',
            'variables' => json_encode(['amount' => $withdrawalAmount]),
            'customer_id' => $customer->id,
            'url' => '/marketing/withdrawals/customer'
        ]);

        // Thông báo Admin
        AdminNotification::create([
            'title' => __('core/base::layouts.new_withdrawal_request'),
            'action_label' => __('core/base::layouts.view'),
            'action_url' => '/admin/withdrawal-marketing/edit/' . $withdrawal->id,
            'description' => __('core/base::layouts.user_requested_withdrawal', [
                'user_name' => $customer->name,
                'amount' => format_price($withdrawalAmount),
            ]) . ' ' . __('core/base::layouts.please_review'),
            'permission' => '',
            'read_at' => null,
        ]);

        // Trừ số dư (bao gồm cả phí)
        $customer->update([
          'walet_1' => $customer->walet_1 - $totalDeduct,
        ]);


        return $this
            ->httpResponse()
            ->setNextRoute('withdrawals.index')
            ->setMessage(trans('core/base::layouts.add_new_withdrawal') . ' ' . trans('core/base::layouts.success'));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $customerCheck = auth('customer')->check();
        $customer = auth('customer')->user();
        $CustomerWithdrawal = CustomerWithdrawal::where('id', $id)->where('customer_id', $customer->id)->first();



        // $currency = session('currency') ?? $defaultCurency;
        // dd($currency);
        return Theme::scope('withdrawals.edit', compact('CustomerWithdrawal', 'customer'), 'withdrawals.edit')->render();
    }

    public function setupSepay()
    {

        $customerCheck = auth('customer')->check();
        $customer = auth('customer')->user();
    	$banks = Bank::all();
        $existingAccount = $customer->bankAccounts()->first();

        if ($existingAccount) {
            return redirect()->route('withdrawals.edit-setup-sepay')
                ->with('error_msg', __('Bạn đã có tài khoản ngân hàng, vui lòng chỉnh sửa thông tin hiện có.'));
        }

        return Theme::scope('withdrawals.setup-sepay', compact('customer', 'banks'), 'withdrawals.setup-sepay')->render();
    }

    public function postSetupSepay(Request $request)
    {
    // dd($request->all());

        $customerCheck = auth('customer')->check();
        $customer = auth('customer')->user();
        $existingAccount = $customer->bankAccounts()->first();

        if ($existingAccount) {
            return $this->httpResponse()
                ->setError()
                ->setNextRoute('withdrawals.edit-setup-sepay')
                ->setMessage(__('Bạn đã có tài khoản ngân hàng, vui lòng chỉnh sửa thay vì thêm mới.'));
        }

        $request->validate([
            'payment_sepay_bank' => 'required|string|in:vietcombank,vpbank,acb,sacombank,hdbank,vietinbank,techcombank,mbbank,bidv,msb,shinhanbank,tpbank,eximbank,vib,agribank,publicbank,kienlongbank,ocb',
            'payment_sepay_account_number' => 'required|numeric|digits_between:6,20',
            'payment_sepay_account_holder' => 'required|string|max:100',
            // 'payment_sepay_prefix' => 'required|string|max:10',
            // 'sepay_webhook_url' => 'required|url|max:255',
            // 'sepay_webhook_secret' => ['required', 'regex:/^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/i'],
            'confirm_bank_account' => 'accepted', // bắt buộc phải được check (giá trị phải là 1, true, hoặc "on")
        ]);

        $hashWebhookSecret = Hash::make($request->sepay_webhook_secret);

        // dd($hashWebhookSecret);

        $bankAccount = BankAccount::create([
            'user_id' => $customer->id,
            'bank_name' => $request->payment_sepay_bank,
            'bank_code' => $request->payment_sepay_bank,
            'account_number' => $request->payment_sepay_account_number,
            'account_holder' => $request->payment_sepay_account_holder,
            // 'sepay_webhook_secret' => $hashWebhookSecret,
            'payment_sepay_prefix' => 'MTAW',
        ]);

        if ($customer && $customer->is_webhook_sepay_active != 1) {
            $customer->update([
                'is_webhook_sepay_active' => 1,
            ]);
        }

        return $this
            ->httpResponse()
            ->setNextRoute('withdrawals.index')
            ->setMessage(trans('core/base::layouts.add_bank_account') . ' ' . trans('core/base::layouts.success'));

        // $customer->update([
        //     'is_webhook_sepay_active' => 1,
        //     'updated_at' => now(),
        // ]);

        // dd($request->all());

        // return view('withdrawals.setup-sepay', compact('customer'));
    }

    public function editSetupSepay()
    {

        $customerCheck = auth('customer')->check();
        $customer = auth('customer')->user();

        $bankAccount = $customer->bankAccounts()->first();
        $banks = Bank::all();

        // dd($customer->bankAccounts()->first());

        return Theme::scope('withdrawals.edit-setup-sepay', compact('bankAccount', 'banks'), 'withdrawals.edit-setup-sepay')->render();
    }

    public function putSetupSepay(Request $request)
    {

        $customerCheck = auth('customer')->check();
        $customer = auth('customer')->user();

        $request->validate([
            'payment_sepay_bank' => 'required|string|in:vietcombank,vpbank,acb,sacombank,hdbank,vietinbank,techcombank,mbbank,bidv,msb,shinhanbank,tpbank,eximbank,vib,agribank,publicbank,kienlongbank,ocb',
            'payment_sepay_account_number' => 'required|numeric|digits_between:6,20',
            'payment_sepay_account_holder' => 'required|string|max:100',
            'payment_sepay_prefix' => 'nullable|string|max:20',
            'confirm_bank_account' => 'accepted', // bắt buộc phải được check (giá trị phải là 1, true, hoặc "on")
        ]);

        $bankAccount = BankAccount::find($request->id);
        if (!$bankAccount) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('core/base::layouts.no_data_found') . ' ' . trans('core/base::layouts.bank_account'));
        }
        // dd($bankAccount);

        $bankAccount->update([
            'user_id' => $customer->id,
            'bank_name' => $request->payment_sepay_bank,
            'bank_code' => $request->payment_sepay_bank,
            'account_number' => $request->payment_sepay_account_number,
            'account_holder' => $request->payment_sepay_account_holder,
            'payment_sepay_prefix' => $request->payment_sepay_prefix ?: $bankAccount->payment_sepay_prefix,
        ]);

        if ($customer && $customer->is_webhook_sepay_active != 1) {
            $customer->update([
                'is_webhook_sepay_active' => 1,
            ]);
        }

        return $this
            ->httpResponse()
            ->setNextRoute('bank_accounts.index')
            ->setMessage(trans('core/base::layouts.edit_bank_account') . ' ' . trans('core/base::layouts.success'));

        // $customer->update([
        //     'is_webhook_sepay_active' => 1,
        //     'updated_at' => now(),
        // ]);

        // dd($request->all());

        // return view('withdrawals.setup-sepay', compact('customer'));
    }
}
