<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function index(): Response
    {
        $categories = Category::with('parent')
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories->map(fn($c) => [
                'id'             => $c->id,
                'name'           => $c->name,
                'slug'           => $c->slug,
                'parent_id'      => $c->parent_id,
                'is_active'      => $c->is_active,
                'products_count' => $c->products_count,
                'image'          => $c->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($c->image) : null,
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Create', [
            'parents' => Category::active()->whereNull('parent_id')->get(['id', 'name']),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->except(['image', 'existing_image']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->mediaService->upload($request->file('image'), 'categories');
        } elseif ($request->filled('existing_image')) {
            $data['image'] = $request->existing_image;
        }

        Category::create($data);
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => fn () => (new CategoryResource($category))->resolve(),
            'parents' => Category::active()
                ->whereNull('parent_id')
                ->where('id', '!=', $category->id)
                ->get(['id', 'name']),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->except(['image', 'existing_image']);

        if ($request->hasFile('image')) {
            if ($category->image) $this->mediaService->delete($category->image);
            $data['image'] = $this->mediaService->upload($request->file('image'), 'categories');
        } elseif ($request->filled('existing_image')) {
            $data['image'] = $request->existing_image;
        }

        $category->update($data);
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
