<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\User;
use App\Notifications\AdminOrderNotification;

class NotifyAdminOnOrderPlaced
{
    public function handle(OrderPlaced $event): void
    {
        User::role('admin')->each(
            fn(User $admin) => $admin->notify(new AdminOrderNotification($event->order, 'order.placed'))
        );
    }
}
