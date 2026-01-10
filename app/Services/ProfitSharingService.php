<?php

namespace App\Services;

use App\Models\CustomerNotification;
use App\Models\Ranking;
use App\Models\RewardHistory;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfitSharingService
{
    public function shareAmount(float $totalFunds): bool
    {
        return $this->distribute($totalFunds);
    }

    public function shareProfitForOrder(Order $order): bool
    {
        if ($order->profit_shared) {
            return false;
        }

        $paymentAmount = $order->payment ? (float) $order->payment->amount : 0;
        $totalAmount = max($paymentAmount, (float) $order->amount);

        if ($totalAmount <= 0) {
            return false;
        }

        try {
            $shared = $this->distribute($totalAmount, $order);

            if ($shared) {
                $order->profit_shared = true;
                $order->profit_shared_at = Carbon::now();
                $order->save();
            }

            return $shared;
        } catch (\Exception $exception) {
            Log::error('Cannot share profit for order ' . $order->getKey() . ': ' . $exception->getMessage());
        }

        return false;
    }

    protected function distribute(float $totalFunds, ?Order $order = null): bool
    {
        $ranks = Ranking::orderBy('rank_lavel', 'desc')->get();

        if ($ranks->isEmpty()) {
            return false;
        }

        $rankMap = $ranks->keyBy('id');
        $rankUserCounts = $ranks->mapWithKeys(function (Ranking $rank) {
            return [$rank->id => Customer::where('rank_id', $rank->id)->count()];
        })->toArray();

        if (array_sum($rankUserCounts) === 0) {
            return false;
        }

        $accumulated = [];

        foreach ($ranks as $rank) {
            $rankPercentage = $rank->percentage_reward / 100;
            $amountToShare = $totalFunds * $rankPercentage;

            if ($amountToShare <= 0) {
                continue;
            }

            $usersAtOrAboveRank = 0;
            foreach ($ranks as $higherRank) {
                if ($higherRank->rank_lavel >= $rank->rank_lavel) {
                    $usersAtOrAboveRank += $rankUserCounts[$higherRank->id] ?? 0;
                }
            }

            if ($usersAtOrAboveRank <= 0) {
                continue;
            }

            $amountPerUser = $amountToShare / $usersAtOrAboveRank;

            foreach ($ranks as $higherRank) {
                if ($higherRank->rank_lavel >= $rank->rank_lavel && ($rankUserCounts[$higherRank->id] ?? 0) > 0) {
                    $accumulated[$higherRank->id] = ($accumulated[$higherRank->id] ?? 0) + $amountPerUser;
                }
            }
        }

        if (empty($accumulated)) {
            return false;
        }

        $this->applyShares($accumulated, $rankMap);

        return true;
    }

    protected function applyShares(array $accumulated, \Illuminate\Support\Collection $rankMap): void
    {
        DB::transaction(function () use ($accumulated, $rankMap): void {
            foreach ($accumulated as $rankId => $totalAmountPerUser) {
                /** @var \App\Models\Ranking|null $rank */
                $rank = $rankMap->get($rankId);
                $customers = Customer::where('rank_id', $rankId)->get();

                foreach ($customers as $customer) {
                    $totalPayment = $customer->payments()
                        ->where('status', 'completed')
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('amount');

                    $walletField = $totalPayment < (float) setting('monthly_repurchase') ? 'walet_2' : 'walet_1';

                    $customer->update([
                        $walletField => DB::raw("COALESCE({$walletField}, 0) + {$totalAmountPerUser}"),
                    ]);

                    $customer->refresh();

                    $notificationData = [
                        'title' => 'core/base::layouts.profit_sharing_notification',
                        'variables' => json_encode([
                            'amount' => $totalAmountPerUser,
                            'text_rank' => $customer->rank->rank_name ?? ($rank->rank_name ?? null),
                            'balance' => $customer->{$walletField},
                        ]),
                        'customer_id' => $customer->id,
                        'url' => '/marketing/dashboard',
                    ];

                    if ($walletField === 'walet_2') {
                        $notificationData['dessription'] = 'profit_share_received_point_wallet';
                    } else {
                        $notificationData['dessription'] = 'profit_share_received';
                    }

                    CustomerNotification::create($notificationData);

                    RewardHistory::create([
                        'customer_id' => $customer->id,
                        'rank_id' => $rankId,
                        'reward' => $totalAmountPerUser,
                        'date_reward' => Carbon::now(),
                        'rank_name' => $customer->rank->rank_name ?? ($rank->rank_name ?? ''),
                    ]);
                }
            }
        });
    }
}
