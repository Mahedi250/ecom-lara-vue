<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function index(): Response
    {
        $brands = Brand::withCount('products')->paginate(20);

        return Inertia::render('Admin/Brands/Index', [
            'brands' => BrandResource::collection($brands),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Brands/Create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->except(['logo', 'existing_logo']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->mediaService->upload($request->file('logo'), 'brands');
        } elseif ($request->filled('existing_logo')) {
            $data['logo'] = $request->existing_logo;
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created.');
    }

    public function edit(Brand $brand): Response
    {
        return Inertia::render('Admin/Brands/Edit', [
            'brand' => fn () => (new BrandResource($brand))->resolve(),
        ]);
    }

    public function update(StoreBrandRequest $request, Brand $brand): RedirectResponse
    {
        $data = $request->except(['logo', 'existing_logo']);

        if ($request->hasFile('logo')) {
            if ($brand->logo) $this->mediaService->delete($brand->logo);
            $data['logo'] = $this->mediaService->upload($request->file('logo'), 'brands');
        } elseif ($request->filled('existing_logo')) {
            $data['logo'] = $request->existing_logo;
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->logo) $this->mediaService->delete($brand->logo);
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted.');
    }
}
