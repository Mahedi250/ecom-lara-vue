<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(): Response
    {
        $products = $this->productService->paginate(
            request()->only(['search', 'category_id', 'brand_id', 'status', 'featured', 'min_price', 'max_price', 'sort_by', 'sort_dir']),
            request()->integer('per_page', 20)
        );

        return Inertia::render('Admin/Products/Index', [
            'products' => ProductResource::collection($products),
            'filters' => request()->only(['search', 'category_id', 'brand_id', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::active()->get(['id', 'name', 'parent_id']),
            'brands' => Brand::active()->get(['id', 'name']),
            'productTypes' => ProductType::where('is_active', true)->get(['id', 'name']),
            'attributes' => Attribute::with('values')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->create(
            $request->except(['thumbnail', 'images']),
            $request->file('thumbnail'),
            $request->file('images', []),
            $this->extractVariantImages($request)
        );

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): Response
    {
        $product->load(['category', 'brand', 'images', 'variants.attributeValues.attribute', 'variants.attributeValues.attributeValue']);

        return Inertia::render('Admin/Products/Show', [
            'product' => fn () => (new ProductResource($product))->resolve(),
        ]);
    }

    public function edit(Product $product): Response
    {
        $product->load(['images', 'variants.attributeValues.attribute', 'variants.attributeValues.attributeValue', 'attributeValues']);

        return Inertia::render('Admin/Products/Edit', [
            'product' => fn () => (new ProductResource($product))->resolve(),
            'categories' => Category::active()->get(['id', 'name', 'parent_id']),
            'brands' => Brand::active()->get(['id', 'name']),
            'productTypes' => ProductType::where('is_active', true)->get(['id', 'name']),
            'attributes' => Attribute::with('values')->orderBy('sort_order')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update(
            $product,
            $request->except(['thumbnail', 'images']),
            $request->file('thumbnail'),
            $request->file('images', []),
            $this->extractVariantImages($request)
        );

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    private function extractVariantImages($request): array
    {
        $images = [];
        foreach ($request->file('variants', []) as $index => $files) {
            if (isset($files['imageFile']) && $files['imageFile'] instanceof UploadedFile) {
                $images[$index] = $files['imageFile'];
            }
        }
        return $images;
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
