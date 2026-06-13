<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
{
    public function __construct(private readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order Confirmation - {$this->order->order_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your order #{$this->order->order_number} has been placed successfully.")
            ->line("Total: {$this->order->total}")
            ->action('View Order', url("/orders/{$this->order->id}"))
            ->line('Thank you for your purchase!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'message' => "Your order #{$this->order->order_number} has been placed.",
        ];
    }
}
