<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reservation;

    public function __construct($reservation)
    {
        // Nhận dữ liệu reservation khi staff update
        $this->reservation = $reservation;
    }

    public function broadcastOn()
    {
        // Phát tín hiệu trên kênh riêng của từng đơn hàng
        return new Channel('orders.' . $this->reservation->id);
    }

    public function broadcastAs()
    {
        // Tên sự kiện để phía Javascript dễ gọi
        return 'status.updated';
    }
}