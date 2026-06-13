<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class AdminOrderNotification extends Notification
{
    public function __construct(
        public readonly Order $order,
        public readonly string $event, // 'order.placed' | 'order.processing' | 'order.delivered'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event'        => $this->event,
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount'       => (float) $this->order->total,
            'url'          => route('admin.orders.show', $this->order->id),
            'message'      => $this->buildMessage(),
        ];
    }

    private function buildMessage(): string
    {
        return match ($this->event) {
            'order.placed'      => "New order #{$this->order->order_number} received",
            'order.processing'  => "Order #{$this->order->order_number} confirmed",
            'order.delivered'   => "Order #{$this->order->order_number} delivered",
            default             => "Order #{$this->order->order_number} updated",
        };
    }
}
