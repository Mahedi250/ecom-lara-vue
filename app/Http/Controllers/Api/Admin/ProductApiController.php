<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductApiController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(): AnonymousResourceCollection
    {
        $products = $this->productService->paginate(request()->all(), request()->integer('per_page', 20));

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $product = $this->productService->create(
            $request->except(['thumbnail', 'images']),
            $request->file('thumbnail'),
            $request->file('images', [])
        );

        return new ProductResource($product);
    }

    public function show(Product $product): ProductResource
    {
        $product->load(['category', 'brand', 'images', 'variants', 'attributeValues.attribute']);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $this->productService->update(
            $product,
            $request->except(['thumbnail', 'images']),
            $request->file('thumbnail'),
            $request->file('images', [])
        );

        return new ProductResource($product->fresh());
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return response()->json(['message' => 'Product deleted.']);
    }
}
