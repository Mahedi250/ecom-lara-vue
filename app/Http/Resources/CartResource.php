<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total' => (float) $this->total,
            'item_count' => $this->item_count,
            'items' => $this->whenLoaded('items', fn() => CartItemResource::collection($this->items)->resolve()),
        ];
    }
}
