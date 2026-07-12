<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Resolve variant attributes: prefer live DB data, fall back to snapshot in options
        $variantAttributes = $this->whenLoaded('variant', function () {
            if (! $this->variant) return null;
            return $this->variant->attributeValues->map(fn($av) => [
                'attribute' => $av->attribute->name,
                'value'     => $av->attributeValue->label ?? $av->attributeValue->value,
            ])->values()->all();
        });

        if (! $variantAttributes && ! empty($this->options['attributes'])) {
            $variantAttributes = $this->options['attributes'];
        }

        return [
            'id'                => $this->id,
            'product_id'        => $this->product_id,
            'product_name'      => $this->product_name,
            'product_sku'       => $this->product_sku,
            'product_slug'      => $this->whenLoaded('product', fn() => $this->product?->slug),
            'product_thumbnail' => $this->whenLoaded('product', function () {
                if (! $this->product) return null;
                $pr = new ProductResource($this->product);
                return $pr->resolve()['thumbnail'] ?? null;
            }),
            'variant_id'        => $this->product_variant_id,
            'variant_sku'       => $this->whenLoaded('variant', fn() => $this->variant?->sku),
            'variant_image'     => $this->whenLoaded('variant', function () {
                if (! $this->variant?->image) return null;
                return Storage::disk('public')->url($this->variant->image);
            }),
            'variant_attributes' => $variantAttributes,
            'quantity'          => $this->quantity,
            'unit_price'        => (float) $this->unit_price,
            'total_price'       => (float) $this->total_price,
            'options'           => $this->options,
        ];
    }
}
