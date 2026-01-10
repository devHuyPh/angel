<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\CustomerNotification;
use App\Models\CustomerWithdrawal;
use App\Models\DepositHistory;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class WithdrawalWebhookController extends BaseController
{
    public function __invoke(Request $request, SettingStore $settingStore): Response
    {
        $authorization = $request->header('Authorization');

        if (!$authorization) {
            Log::warning('Missing Authorization header');
            return response('Unauthorized', 401);
        }

        if (str_starts_with($authorization, 'Apikey ')) {
            $token = substr($authorization, strlen('Apikey '));
        } else {
            Log::warning('Invalid Authorization header format', ['authorization' => $authorization]);
            return response('Unauthorized', 401);
        }

        $code = $request->input('code');
        $transferAmount = $request->input('transferAmount');

        $customer_withdrawal = CustomerWithdrawal::where('transaction_id', $code)
            ->where('amount', $transferAmount)->first();
    
        if(!$customer_withdrawal){
            return response('not foud', 404);
        }

        $customer = $customer_withdrawal->customer;
        $bankAccount = $customer->bankAccounts()->first();
        $expectedToken = $bankAccount->sepay_webhook_secret;

        if (!Hash::check($token, $expectedToken)) {
            Log::warning('Invalid API token', [
                'given' => $token,
                'expected' => $expectedToken,
            ]);
            return response('Unauthorized', 401);
        }

        if($customer_withdrawal->status != 'completed'){
            $customer_withdrawal->update([
                'status' => 'completed',
                'processed_at' => Carbon::now()
            ]);

            CustomerNotification::create([ 
                'title' => 'core/base::layouts.withdrawal-success',
                'dessription' => 'core/base::layouts.your-withdrawal-success '.format_price($customer_withdrawal->amount).
                                ' core/base::layouts.in-your-bank-account '.
                                ' core/base::layouts.your-talent-balence '.format_price($customer->walet_1),
                'customer_id' => $customer->id,
                'url' => 'withdrawal.show'
            ]);
        }


        // $code = $request->input('code');
        // $transferAmount = $request->input('transferAmount');

        // $deposit = DepositHistory::where('transaction_code', $code)
        //     ->where('amount', $transferAmount)->first();

        // if($deposit){
        //     $deposit->update([
        //         'status' => 1,
        //         'confirmed_at' => Carbon::now()
        //     ]);
        // }

        // $customer = $deposit->user;

        // $customer->update([
        //     'walet_1' => $customer->walet_1 + $deposit->amount 
        // ]);



        return response('ok');
    }
}
