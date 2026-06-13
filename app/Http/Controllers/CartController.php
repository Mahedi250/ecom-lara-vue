<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(): Response
    {
        $cart = $this->cartService->getCart();

        return Inertia::render('Cart/Index', [
            'cart' => fn() => (new CartResource($cart))->resolve(),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
        ]);

        $this->cartService->addItem(
            $request->product_id,
            $request->integer('quantity', 1),
            $request->variant_id
        );

        return back()->with('success', 'Item added to cart.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:0']]);

        $this->cartService->updateItem($cartItem, $request->integer('quantity'));

        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $cartItem): RedirectResponse
    {
        $this->cartService->removeItem($cartItem);

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();

        return back()->with('success', 'Cart cleared.');
    }

    public function data(): JsonResponse
    {
        $cart = $this->cartService->getCart();

        return response()->json(new CartResource($cart));
    }
}
