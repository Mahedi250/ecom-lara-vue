<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductApiController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(): AnonymousResourceCollection
    {
        $products = $this->productService->paginate(
            request()->only(['search', 'category_id', 'brand_id', 'min_price', 'max_price', 'sort_by', 'sort_dir']),
            request()->integer('per_page', 24)
        );

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        abort_if($product->status->value !== 'active', 404);
        $product->load(['category', 'brand', 'images', 'variants.attributeValues.attribute']);

        return new ProductResource($product);
    }
}
