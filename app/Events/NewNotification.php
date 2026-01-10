<?php
namespace App\Events;

use App\Models\CustomerNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(CustomerNotification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return ['notifications'];
    }

    public function broadcastAs()
    {
        return 'new-notification';
    }
}
