<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private readonly CartService $cartService) {}

    public function placeOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $cart = $this->cartService->getCart();

            if ($cart->items->isEmpty()) {
                throw new \RuntimeException('Cart is empty.');
            }

            $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
            $discount = 0;
            $coupon = null;

            if (!empty($data['coupon_code'])) {
                $coupon = Coupon::active()->where('code', $data['coupon_code'])->firstOrFail();
                $discount = $coupon->calculateDiscount($subtotal);
            }

            $shippingCost = $data['shipping_cost'] ?? 0;
            $tax = $data['tax'] ?? 0;
            $total = $subtotal - $discount + $shippingCost + $tax;

            $order = Order::create([
                'user_id' => Auth::id() ?? null,
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::Pending,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $total,
                'coupon_code' => $coupon?->code,
                'notes' => $data['notes'] ?? null,
            ]);

            // Eager-load variant attributes for snapshot
            $cart->load('items.variant.attributeValues.attribute', 'items.variant.attributeValues.attributeValue');

            foreach ($cart->items as $item) {
                $variantAttrs = null;
                if ($item->variant) {
                    $variantAttrs = $item->variant->attributeValues->map(fn($av) => [
                        'attribute' => $av->attribute->name,
                        'value'     => $av->attributeValue->label ?? $av->attributeValue->value,
                    ])->values()->all();
                }

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->variant?->sku ?? $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                    'options' => $variantAttrs ? ['attributes' => $variantAttrs] : null,
                ]);

                if ($item->product->manage_stock) {
                    if ($item->variant) {
                        $item->variant->decrement('stock', $item->quantity);
                    } else {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
            }

            $this->createAddress($order, $data['shipping_address'], 'shipping');
            if (!empty($data['billing_address'])) {
                $this->createAddress($order, $data['billing_address'], 'billing');
            }

            if ($coupon) {
                $coupon->increment('used_count');
                if (Auth::id()) {
                    $coupon->usages()->create([
                        'user_id' => Auth::id(),
                        'order_id' => $order->id,
                    ]);
                }
            }

            $cart->items()->delete();

            event(new OrderPlaced($order));

            return $order->load(['items', 'shippingAddress']);
        });
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        $order->update(['status' => $status]);
        return $order;
    }

    private function createAddress(Order $order, array $data, string $type): void
    {
        $order->addresses()->create(array_merge($data, ['type' => $type]));
    }
}
