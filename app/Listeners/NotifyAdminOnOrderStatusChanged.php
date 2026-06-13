<?php

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Models\User;
use App\Notifications\AdminOrderNotification;

class NotifyAdminOnOrderStatusChanged
{
    public function handle(OrderStatusChanged $event): void
    {
        $eventType = match ($event->order->status) {
            OrderStatus::Processing => 'order.processing',
            OrderStatus::Delivered  => 'order.delivered',
            default                 => null,
        };

        if (!$eventType) return;

        User::role('admin')->each(
            fn(User $admin) => $admin->notify(new AdminOrderNotification($event->order, $eventType))
        );
    }
}
