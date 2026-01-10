<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;

class BankAccountController extends BaseController
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
        $accounts = BankAccount::with('bank')->where('user_id', $customer->id)->get();
        return Theme::scope('bank_accounts.index', compact('accounts'), 'bank_accounts.index')->render();
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
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_code_new' => 'required|string|max:10',
            'account_number' => 'required|string|max:50|unique:bank_accounts',
            'account_holder' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:20',
        ]);
        $customer = auth('customer')->user();

        BankAccount::create([
            'user_id' => $customer->id,
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code_new,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'branch' => $request->branch,
            'swift_code' => $request->swift_code,
        ]);

        return $this
            ->httpResponse()
            ->setMessage(trans('core/base::layouts.add_bank_account') . ' ' . trans('core/base::layouts.success'));
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
        $customerCheck = auth('customer')->check();
        $customer = auth('customer')->user();
        $bankAccount = BankAccount::where('id', $id)
                                  ->where('user_id', $customer->id) 
                                  ->firstOrFail();

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_code_new' => 'required|string|max:10',
            'account_number' => 'required|string|max:50|unique:bank_accounts,account_number,' . $bankAccount->id,
            'account_holder' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:20',
        ]);

        $bankAccount->update($request->all());

        return $this
            ->httpResponse()
            ->setMessage(trans('core/base::layouts.edit_bank_account') . ' ' . trans('core/base::layouts.success'));
    }

    public function destroy(string $id)
    {
        $customerCheck = auth('customer')->check();
        $customer = auth('customer')->user();
        $bankAccount = BankAccount::where('id', $id)
                                  ->where('user_id', $customer->id) 
                                  ->firstOrFail();
    
        $bankAccount->delete();
    
        return $this
            ->httpResponse()
            ->setMessage(trans('core/base::layouts.delete') . ' ' . trans('core/base::layouts.success'));
    }
}
