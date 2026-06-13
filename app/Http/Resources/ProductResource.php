<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    private function resolveThumbnail(string $path): string
    {
        if (str_starts_with($path, 'http')) return $path;
        if (Storage::disk('public')->exists($path)) return Storage::disk('public')->url($path);
        return asset($path);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'effective_price' => (float) $this->effective_price,
            'stock' => $this->stock,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'is_featured' => $this->is_featured,
            'manage_stock' => $this->manage_stock,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'in_stock' => $this->isInStock(),
            'thumbnail' => $this->thumbnail ? $this->resolveThumbnail($this->thumbnail) : null,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'category' => $this->whenLoaded('category', fn() => (new CategoryResource($this->category))->resolve()),
            'brand' => $this->whenLoaded('brand', fn() => (new BrandResource($this->brand))->resolve()),
            'images' => $this->whenLoaded('images', fn() => ProductImageResource::collection($this->images)->resolve()),
            'variants' => $this->whenLoaded('variants', fn() => ProductVariantResource::collection($this->variants)->resolve()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
