<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case PartiallyPaid = 'partially_paid';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::Unpaid => 'Unpaid',
            self::Paid => 'Paid',
            self::PartiallyPaid => 'Partially Paid',
            self::Refunded => 'Refunded',
        };
    }
}
