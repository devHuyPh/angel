<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerWithdrawal;
use App\Models\CustomerNotification;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Http\Request;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Botble\AuditLog\Models\AuditHistory;
use Illuminate\Support\Carbon;

class AdCustomerWithdrawalController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
  public function index()
{
    $this->pageTitle(trans('core/dashboard::dashboard.withdrawals_manager'));

    $customerWithdrawals = CustomerWithdrawal::with('customer')
        ->whereHas('customer')  
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('admin.withdrawals_manager.index', compact('customerWithdrawals'));
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
    public function edit(string $id)
    {
        $customerWithdrawal = CustomerWithdrawal::where('id', $id)->first();
        return view('admin.withdrawals_manager.edit', compact('customerWithdrawal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // dd($request->all());
        $admin = Auth::user();
        $customerWithdrawal = CustomerWithdrawal::where('id', $id)->where('status', 'pending')->first();

        if(!$customerWithdrawal){
            return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage('Lệnh đã được chỉnh sửa');
        }

        $customer = $customerWithdrawal->customer;
        $currentStatus = $customerWithdrawal->status;
        $newStatus = $request->status;

        $validTransitions = [
            'pending' => ['pending', 'completed', 'rejected', 'cancelled'],
            'completed' => ['completed'],
            'rejected' => ['rejected'],
            'cancelled' => ['cancelled'],
        ];

        if ($request->submit_cancel || $newStatus == 'cancelled') {
            $request->validate([
                'description' => 'required|string|max:255'
            ]);

            if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(trans('core/base::layouts.no_change_status_from') . ' ' .
                        trans('core/base::layouts.' . $currentStatus) . ' ' .
                        trans('core/base::layouts.to') . ' ' .
                        trans('core/base::layouts.' . $newStatus));
            }

            $customerWithdrawal->update([
                'status' => 'cancelled',
                'notes' => $request->description,
                'admin_id' => $admin->id,
                'processed_at' => Carbon::now()
            ]);

            $customer->update([
                'walet_1' => (float) $customer->walet_1 + ((float) $customerWithdrawal->amount + (float) $customerWithdrawal->fee)
            ]);

            CustomerNotification::create([
                'title' => 'core/base::layouts.withdrawal_request_rejected',
                'dessription' => 'withdrawal_request_rejected_description',
                'variables' => json_encode([
                    'amount' => (float) $customerWithdrawal->amount,
                ]),
                'customer_id' => $customer->id,
                'url' => '/marketing/withdrawals/customer'
            ]);

            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::layouts.process_withdrawal') . ' ' . trans('core/base::layouts.success'));
        }

        $request->validate([
            'payment_channel' => 'required|string|max:255',
            'transaction_id' => 'required|string|max:255',
            'status' => 'required|string|in:pending,completed,rejected,cancelled',
        ]);


        // if ((float) $customer->walet_1 < (float) $customerWithdrawal->amount) {
        //     return $this
        //         ->httpResponse()
        //         ->setError()
        //         ->setMessage(trans('core/base::layouts.error_wallet_1'));
        // }

        if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('core/base::layouts.no_change_status_from') . ' ' .
                    trans('core/base::layouts.' . $currentStatus) . ' ' .
                    trans('core/base::layouts.to') . ' ' .
                    trans('core/base::layouts.' . $newStatus));
        }

        // dd($customer);
        // $customer->update([
        //     'walet_1' => (float) $customer->walet_1 - (float) $customerWithdrawal->amount
        // ]);

        $customerWithdrawal->update([
            'withdrawal_method' => $request->payment_channel,
            'transaction_id' => $request->transaction_id,
            'status' => $newStatus,
            'notes' => $request->description,
            'admin_id' => $admin->id,
            'processed_at' => Carbon::now()
        ]);

        if ($newStatus === 'rejected') {
            CustomerNotification::create([
                'title' => 'core/base::layouts.withdrawal_request_rejected',
                'dessription' => 'withdrawal_request_rejected_description',
                'variables' => json_encode([
                    'amount' => (float) $customerWithdrawal->amount,
                ]),
                'customer_id' => $customer->id,
                'url' => '/marketing/withdrawals/customer'
            ]);
        } else {
            CustomerNotification::create([
                'title' => 'core/base::layouts.your_withdrawal_processed',
                'dessription' => 'core/base::layouts.admin_processed_your_withdrawal .' .
                    ' core/base::layouts.withdrawal_method :' . $request->payment_channel . ', ' .
                    'core/base::layouts.transaction_id :' . $request->transaction_id . ', ' .
                    'core/base::layouts.status :' . ' core/base::layouts.' . $newStatus . ' , ' .
                    'core/base::layouts.notes :' . $request->description,
                'customer_id' => $customer->id,
                'url' => '/marketing/withdrawals/customer'
            ]);
        }

        AuditHistory::create([
            'user_agent'     => $request->header('User-Agent'),
            'ip_address'     => $request->ip(),
            'module'         => 'withdrawals',
            'action'         => "updated",
            'user_id'        => $admin->id,
            'reference_user' => $customerWithdrawal->customer_id,
            'reference_id'   => $customerWithdrawal->id,
            'reference_name' => $customer->name,
            'type'           => 'update',
            'request'        => json_encode($request->all()),
        ]);

        if ($request->submitter_exit) {
            return $this
                ->httpResponse()
                ->setNextRoute('withdrawals-manager.index')
                ->setMessage(trans('core/base::layouts.process_withdrawal') . ' ' . trans('core/base::layouts.success'));
        } else {
            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::layouts.process_withdrawal') . ' ' . trans('core/base::layouts.success'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $admin = Auth::user();
        $customerWithdrawal = CustomerWithdrawal::where('id', $id)->first();
        $customer = $customerWithdrawal->customer;

        if ($customerWithdrawal) {
            $customerWithdrawal->delete();
        } else {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('core/base::layouts.delete') . ' ' . trans('core/base::layouts.error'));
        }

        AuditHistory::create([
            'user_agent'     => $request->header('User-Agent'),
            'ip_address'     => $request->ip(),
            'module'         => 'withdrawals',
            'action'         => "deleted",
            'user_id'        => $admin->id,
            'reference_user' => $customerWithdrawal->customer_id,
            'reference_id'   => $customerWithdrawal->id,
            'reference_name' => $customer->name,
            'type'           => 'delete',
            'request'        => json_encode($request->all()),
        ]);

        return $this
            ->httpResponse()
            ->setMessage(trans('core/base::layouts.delete') . ' ' . trans('core/base::layouts.success'));
    }
}
