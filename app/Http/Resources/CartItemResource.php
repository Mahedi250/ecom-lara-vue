<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => $this->whenLoaded('product', fn() => (new ProductResource($this->product))->resolve()),
            'variant' => $this->whenLoaded('variant', fn() => (new ProductVariantResource($this->variant))->resolve()),
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'subtotal' => (float) ($this->price * $this->quantity),
        ];
    }
}
