<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\CustomerNotification;
use App\Models\DepositHistory;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DepositWebhookController extends BaseController
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

        $expectedToken = setting('payment_sepay_webhook_secret');

        if (!Hash::check($token, $expectedToken)) {
            Log::warning('Invalid API token', [
                'given' => $token,
                'expected' => $expectedToken,
            ]);
            return response('Unauthorized', 401);
        }

        $code = $request->input('code');
        $transferAmount = $request->input('transferAmount');

        $deposit = DepositHistory::where('transaction_code', $code)
            ->where('amount', $transferAmount)->first();
    
        if(!$deposit){
            return response('Not foud', 404);
        }

        $deposit->update([
            'status' => 1,
            'confirmed_at' => Carbon::now()
        ]);

        $customer = $deposit->user;

        $customer->update([
            'walet_1' => $customer->walet_1 + $deposit->amount 
        ]);

        CustomerNotification::create([ 
            'title' => 'core/base::layouts.deposit-success',
            'dessription' => 'core/base::layouts.your-deposit-success '.format_price($deposit->amount).
                            ' core/base::layouts.in-your-wallet '.
                            ' core/base::layouts.your-talent-balence '.format_price($customer->walet_1),
            'customer_id' => $customer->id,
            'url' => 'deposit.show'
        ]);

        return response('ok');
    }
}
