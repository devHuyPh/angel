<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DepositHistory;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Currency;
use Botble\Payment\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepositHistoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer = auth('customer')->user();
        $deposits = $customer->deposit()->orderBy('created_at', 'desc')->paginate(10);
        // $withdrawals = $customer->withdrawals()->orderBy('created_at', 'desc')->paginate(10);
        // dd($deposits);
        return view('deposits_of_money.index', compact('deposits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customer = auth('customer')->user();
        $currency = session('currency') ?? 'VND';
        $currencies = Currency::get();
        return view('deposits_of_money.checkout', compact('customer', 'currency', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    protected function generateUniqueTransactionCode()
    {
        do {
            $code = setting('payment_sepay_prefix') . str_pad(random_int(0, 99999999999), 11, '0', STR_PAD_LEFT);
        } while (Payment::where('charge_id', $code)->exists());

        return $code;
    }

    public function store(Request $request)
    {
        // dd(session()->all());
        // dd($request->all());
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10000'],
            'currency' => ['required', 'string', 'max:10'],
            'payment_method' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'agree_terms_and_policy' => ['required', 'in:1'],
        ], [
            'agree_terms_and_policy.in' => 'Bạn phải đồng ý với điều khoản và chính sách.',
        ]);

        $transactionCode = $this->generateUniqueTransactionCode();

        if ($request->agree_terms_and_policy != 1) {
            $transactionCode = null;
        }

        // dd($transactionCode);

        $deposit = DepositHistory::create([
            'user_id' => auth('customer')->user()->id,
            'amount' => $validated['amount'],
            'method' => $validated['payment_method'],
            'status' => 0,
            'transaction_code' => $transactionCode,
            'note' => $validated['description'] ?? null,
            'currency' => $validated['currency'],
        ]);

        return $this
            ->httpResponse()
            ->setNextRoute('deposit.show', $deposit->id)
            ->setMessage(trans('core/base::layouts.add_bank_account') . ' ' . trans('core/base::layouts.success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = auth('customer')->user();
        $deposit = DepositHistory::find($id);
        // dd($deposit);
        return view('deposits_of_money.checkout-success', compact('customer', 'deposit'));
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
    public function checkStatus(Request $request, string $id): JsonResponse
    {
        $deposit = DepositHistory::find($id);

        if (!$deposit) {
            return response()->json([
                'message' => 'Deposit not found',
            ], 404);
        }

        return response()->json([
            'status' => $deposit->status,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
