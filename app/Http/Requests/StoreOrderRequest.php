<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coupon_code' => ['nullable', 'string', function (string $attribute, mixed $value, Closure $fail) {
                $coupon = Coupon::active()->where('code', $value)->first();

                if (!$coupon) {
                    $fail('The coupon code is invalid or has expired.');
                    return;
                }

                if (Auth::check() && $coupon->usage_limit_per_user
                    && $coupon->usages()->where('user_id', Auth::id())->count() >= $coupon->usage_limit_per_user) {
                    $fail('You have already used this coupon.');
                }
            }],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'string', 'in:cod,pickup,online'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.name' => ['required', 'string', 'max:255'],
            'shipping_address.email' => ['nullable', 'email'],
            'shipping_address.phone' => ['required', 'string', 'max:20'],
            'shipping_address.address_line1' => ['required', 'string', 'max:255'],
            'shipping_address.address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['nullable', 'string', 'max:100'],
            'shipping_address.state' => ['nullable', 'string', 'max:100'],
            'shipping_address.postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_address.country' => ['required', 'string', 'max:2'],
            'billing_address' => ['nullable', 'array'],
            'billing_address.name' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.address_line1' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.city' => ['nullable', 'string', 'max:100'],
            'billing_address.country' => ['required_with:billing_address', 'string', 'max:2'],
        ];
    }
}
