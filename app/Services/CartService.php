<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): Cart
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            $sessionId = Session::get('cart_session_id');
            if ($sessionId) {
                $this->mergeGuestCart($cart, $sessionId);
                Session::forget('cart_session_id');
            }

            return $cart->load('items.product', 'items.variant');
        }

        $sessionId = Session::get('cart_session_id') ?? Session::getId();
        Session::put('cart_session_id', $sessionId);

        return Cart::firstOrCreate(['session_id' => $sessionId])
                   ->load('items.product', 'items.variant');
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $cart = $this->getCart();
        $product = Product::findOrFail($productId);
        $price = $variantId
            ? ProductVariant::findOrFail($variantId)->effective_price
            : $product->effective_price;

        $item = $cart->items()
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            return $item->fresh();
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
            'price' => $price,
        ]);
    }

    public function updateItem(CartItem $item, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            $item->delete();
            return $item;
        }

        $item->update(['quantity' => $quantity]);
        return $item->fresh();
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(): void
    {
        $this->getCart()->items()->delete();
    }

    private function mergeGuestCart(Cart $userCart, string $sessionId): void
    {
        $guestCart = Cart::where('session_id', $sessionId)->with('items')->first();
        if (!$guestCart) return;

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()
                ->where('product_id', $guestItem->product_id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
            } else {
                $userCart->items()->create($guestItem->only(['product_id', 'product_variant_id', 'quantity', 'price']));
            }
        }

        $guestCart->delete();
    }
}
