<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(): Response
    {
        $products = $this->productService->paginate(
            request()->only(['search', 'category_id', 'brand_id', 'min_price', 'max_price', 'sort_by', 'sort_dir']),
            request()->integer('per_page', 24)
        );

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($products),
            'categories' => Category::active()->root()->with('children')->get(),
            'brands' => Brand::active()->get(['id', 'name', 'slug']),
            'filters' => request()->only(['search', 'category_id', 'brand_id', 'min_price', 'max_price', 'featured', 'sort_by', 'sort_dir']),
        ]);
    }

    public function show(Product $product): Response
    {
        abort_if($product->status->value !== 'active', 404);

        $product->load(['category', 'brand', 'images', 'variants.attributeValues.attribute', 'variants.attributeValues.attributeValue']);
        $product->increment('views');

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        return Inertia::render('Products/Show', [
            'product' => fn() => (new ProductResource($product))->resolve(),
            'related' => ProductResource::collection($related),
        ]);
    }
}
