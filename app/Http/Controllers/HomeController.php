<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $featuredProducts = Product::active()
            ->featured()
            ->with(['category', 'images'])
            ->latest()
            ->paginate(10);

        $latestProducts = Product::active()
            ->with(['category', 'images'])
            ->latest()
            ->paginate(10);

        $categories = Category::active()
            ->root()
            ->with('children')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'parent_id', 'image'])
            ->map(fn ($cat) => [
                'id'       => $cat->id,
                'name'     => $cat->name,
                'image'    => $cat->image ? Storage::disk('public')->url($cat->image) : null,
                'children' => $cat->children->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            ]);

        $brands = Brand::active()
            ->withCount('products')
            ->having('products_count', '>', 0)
            ->orderByDesc('products_count')
            ->get(['id', 'name', 'slug', 'logo'])
            ->map(fn ($b) => [
                'id'   => $b->id,
                'name' => $b->name,
                'slug' => $b->slug,
                'logo' => $b->logo ? Storage::disk('public')->url($b->logo) : null,
            ]);

        $heroSlides = HeroSlide::active()->orderBy('sort_order')->get()->map(fn($s) => [
            'id'          => $s->id,
            'title'       => $s->title,
            'subtitle'    => $s->subtitle,
            'tag'         => $s->tag,
            'button_text' => $s->button_text ?? 'Shop Now',
            'href'        => $s->href ?? '/products',
            'image_url'   => $s->image_url,
            'bg_color'    => $s->bg_color ?? '#4f46e5',
        ]);

        return Inertia::render('Welcome', [
            'featuredProducts' => ProductResource::collection($featuredProducts),
            'latestProducts'   => ProductResource::collection($latestProducts),
            'categories'       => $categories,
            'brands'           => $brands,
            'heroSlides'       => $heroSlides,
        ]);
    }
}
