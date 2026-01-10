<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\CustomerNotification;
use App\Models\VendorNotifications;
use Botble\Base\Models\AdminNotification;
use Botble\Ecommerce\Models\Customer;

class ResetExpiredRanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-expired-ranks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        Customer::query()
            ->whereNotNull('rank_expires_at')
            ->where('rank_expires_at', '<=', $now)
            ->whereNotNull('rank_id')
            ->orderBy('id')
            ->chunkById(100, function ($customers) use ($now) {
                foreach ($customers as $customer) {
                    $oldRankId   = $customer->rank_id;
                    $expiredAt   = $customer->rank_expires_at;

                    // Nếu có relationship rank() thì lấy tên rank cho đẹp
                    // (nếu không có thì cứ dùng rank_id)
                    $oldRankName = $customer->rank->rank_name ?? null;
                    $customer->update([
                        'rank_id'=>null,
                        'rank_reset_at'=>$now
                    ]);
                   CustomerNotification::create([
                        'title' => 'core/base::layouts.rank_reset_notification',
                        'dessription' => 'rank_reset_notifications',
                        'variables' => json_encode([
                            'old_rank_id'   => $oldRankId,
                            'old_rank_name' => $oldRankName,
                            'expired_at'    => optional($expiredAt)->toDateTimeString(),
                            'reset_at'      => $now->toDateTimeString(),
                            'message'       => 'Hạng của bạn đã hết hạn và hệ thống đã đưa về mặc định. Vui lòng tái tiêu dùng để được xét lên hạng lại.',
                        ]),
                        'customer_id' => $customer->id,
                        'url' => '/marketing/dashboard',
                    ]);
                    
                }
            });

        return Command::SUCCESS;
    }
}
