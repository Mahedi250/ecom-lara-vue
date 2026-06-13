<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(): CartResource
    {
        return new CartResource($this->cartService->getCart());
    }

    public function store(Request $request): CartResource
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
        ]);

        $this->cartService->addItem($request->product_id, $request->integer('quantity', 1), $request->variant_id);

        return new CartResource($this->cartService->getCart());
    }

    public function update(Request $request, CartItem $cartItem): CartResource
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:0']]);
        $this->cartService->updateItem($cartItem, $request->integer('quantity'));

        return new CartResource($this->cartService->getCart());
    }

    public function destroy(CartItem $cartItem): JsonResponse
    {
        $this->cartService->removeItem($cartItem);

        return response()->json(['message' => 'Item removed.']);
    }
}
