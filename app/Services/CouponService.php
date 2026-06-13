<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;

class CouponService
{
    public function validate(string $code, float $orderTotal): array
    {
        $coupon = Coupon::active()->where('code', $code)->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code.'];
        }

        if ($orderTotal < $coupon->minimum_order_amount) {
            return [
                'valid' => false,
                'message' => "Minimum order amount is {$coupon->minimum_order_amount}.",
            ];
        }

        if ($coupon->usage_limit_per_user && Auth::check()) {
            $used = $coupon->usages()->where('user_id', Auth::id())->count();
            if ($used >= $coupon->usage_limit_per_user) {
                return ['valid' => false, 'message' => 'Coupon usage limit reached.'];
            }
        }

        $discount = $coupon->calculateDiscount($orderTotal);

        return [
            'valid' => true,
            'discount' => $discount,
            'coupon' => $coupon,
        ];
    }
}
