<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class StatusPesananBerubah extends Notification
{
    public function __construct(public Order $order, public $labelStatus)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'judul' => $this->order->title,
            'status' => $this->labelStatus,
        ];
    }
}
