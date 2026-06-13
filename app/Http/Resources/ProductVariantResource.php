<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'price' => $this->price ? (float) $this->price : null,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'effective_price' => (float) $this->effective_price,
            'stock' => $this->stock,
            'is_active' => $this->is_active,
            'image' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'attributes_map' => $this->whenLoaded('attributeValues', fn() =>
                $this->attributeValues->mapWithKeys(fn($av) => [
                    (string) $av->attribute_id => $av->attribute_value_id,
                ])->toArray()
            ),
            'attributes' => $this->whenLoaded('attributeValues', fn() =>
                $this->attributeValues->map(fn($av) => [
                    'attribute_id'       => $av->attribute_id,
                    'attribute_value_id' => $av->attribute_value_id,
                    'attribute'          => $av->attribute->name,
                    'value'              => $av->attributeValue->label ?? $av->attributeValue->value,
                    'color_code'         => $av->attributeValue->color_code ?? null,
                ])
            ),
        ];
    }
}
