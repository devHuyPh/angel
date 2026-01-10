<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NewNotification extends Command
{
    protected $signature = 'notification:check';
    protected $description = 'Kiểm tra và cập nhật thông báo mới cho khách hàng';

    public function handle()
    {
        $newNotifications = CustomerNotification::where('readed', 0)
            ->where('created_at', '>=', Carbon::now()->subMinutes(5)) // Lọc thông báo mới trong 5 phút gần nhất
            ->get();

        if ($newNotifications->isEmpty()) {
            $this->info('Không có thông báo mới.');
            return;
        }

        foreach ($newNotifications as $notification) {
            Log::info("Thông báo mới cho khách hàng ID {$notification->customer_id}: {$notification->title}");
        }

        $this->info('Kiểm tra thông báo hoàn tất!');
    }
}
