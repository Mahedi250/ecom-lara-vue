<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'product_sku' => $this->product_sku,
            'product_slug' => $this->whenLoaded('product', fn() => $this->product?->slug),
            'product_thumbnail' => $this->whenLoaded('product', function () {
                if (! $this->product) return null;
                $pr = new ProductResource($this->product);
                return $pr->resolve()['thumbnail'] ?? null;
            }),
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'total_price' => (float) $this->total_price,
            'options' => $this->options,
        ];
    }
}
