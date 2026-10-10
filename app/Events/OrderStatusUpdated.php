<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        // Phát tín hiệu trên 2 channels:
        // 1. Channel riêng cho từng đơn hàng (khách hàng nghe lắng)
        // 2. Channel chung cho admin/nhân viên
        return [
            new Channel('orders.' . $this->order->id),
            new Channel('admin-orders-updates'),
        ];
    }

    public function broadcastAs()
    {
        return 'order.status.updated';
    }
}
