<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Product::query()
            ->with(['category', 'brand', 'images'])
            ->when($filters['search'] ?? null, fn($q, $s) => $q->search($s))
            ->when($filters['category_id'] ?? null, fn($q, $id) => $q->where('category_id', $id))
            ->when($filters['brand_id'] ?? null, fn($q, $id) => $q->where('brand_id', $id))
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($filters['featured'] ?? null, fn($q) => $q->featured())
            ->when(isset($filters['min_price']), fn($q) => $q->where('price', '>=', $filters['min_price']))
            ->when(isset($filters['max_price']), fn($q) => $q->where('price', '<=', $filters['max_price']))
            ->orderBy(in_array($filters['sort_by'] ?? '', ['price', 'name', 'created_at', 'views']) ? $filters['sort_by'] : 'created_at', ($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage);
    }

    public function create(array $data, ?UploadedFile $thumbnail = null, array $images = [], array $variantImages = []): Product
    {
        return DB::transaction(function () use ($data, $thumbnail, $images, $variantImages) {
            if ($thumbnail) {
                $data['thumbnail'] = $thumbnail->store('products/thumbnails', 'public');
            } elseif (!empty($data['existing_thumbnail'])) {
                $data['thumbnail'] = $data['existing_thumbnail'];
            }
            unset($data['existing_thumbnail']);

            $product = Product::create($data);

            if (!empty($data['attributes'])) {
                $this->syncAttributes($product, $data['attributes']);
            }

            if (!empty($data['variants'])) {
                $this->syncVariants($product, $data['variants'], $variantImages);
            }

            foreach ($images as $index => $image) {
                $path = $image->store('products/images', 'public');
                $product->images()->create([
                    'path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }

            return $product->load(['category', 'brand', 'images', 'attributeValues.attribute']);
        });
    }

    public function update(Product $product, array $data, ?UploadedFile $thumbnail = null, array $images = [], array $variantImages = []): Product
    {
        return DB::transaction(function () use ($product, $data, $thumbnail, $images, $variantImages) {
            if ($thumbnail) {
                if ($product->thumbnail) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $data['thumbnail'] = $thumbnail->store('products/thumbnails', 'public');
            } elseif (!empty($data['existing_thumbnail'])) {
                $data['thumbnail'] = $data['existing_thumbnail'];
            }
            unset($data['existing_thumbnail']);

            $product->update($data);

            if (isset($data['attributes'])) {
                $this->syncAttributes($product, $data['attributes']);
            }

            if (isset($data['variants'])) {
                $this->syncVariants($product, $data['variants'], $variantImages);
            }

            foreach ($images as $index => $image) {
                $path = $image->store('products/images', 'public');
                $product->images()->create([
                    'path' => $path,
                    'sort_order' => $product->images()->count() + $index,
                ]);
            }

            return $product->fresh(['category', 'brand', 'images', 'attributeValues.attribute']);
        });
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    private function syncVariants(Product $product, array $variants, array $variantImages = []): void
    {
        $incomingIds = collect($variants)->pluck('id')->filter()->values()->all();
        $product->variants()->whereNotIn('id', $incomingIds)->delete();

        foreach ($variants as $index => $row) {
            $id    = $row['id'] ?? null;
            $attrs = $row['attributes'] ?? [];
            $data  = collect($row)->except(['id', 'attributes'])->all();

            if (isset($variantImages[$index]) && $variantImages[$index] instanceof UploadedFile) {
                $data['image'] = $variantImages[$index]->store('products/variants', 'public');
            }

            if ($id && $variant = $product->variants()->find((int) $id)) {
                $variant->update($data);
            } else {
                $variant = $product->variants()->create($data);
            }

            $variant->attributeValues()->delete();
            foreach ($attrs as $attrId => $valueId) {
                if ($valueId) {
                    $variant->attributeValues()->create([
                        'attribute_id'       => (int) $attrId,
                        'attribute_value_id' => (int) $valueId,
                    ]);
                }
            }
        }
    }

    private function syncAttributes(Product $product, array $attributes): void
    {
        $product->attributeValues()->delete();

        foreach ($attributes as $attributeId => $valueId) {
            if (is_array($valueId)) {
                foreach ($valueId as $vid) {
                    $product->attributeValues()->create([
                        'attribute_id' => $attributeId,
                        'attribute_value_id' => is_numeric($vid) ? $vid : null,
                        'custom_value' => !is_numeric($vid) ? $vid : null,
                    ]);
                }
            } else {
                $product->attributeValues()->create([
                    'attribute_id' => $attributeId,
                    'attribute_value_id' => is_numeric($valueId) ? $valueId : null,
                    'custom_value' => !is_numeric($valueId) ? $valueId : null,
                ]);
            }
        }
    }
}
