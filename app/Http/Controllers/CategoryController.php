<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::active()
            ->root()
            ->with(['children' => fn ($query) => $query
                ->active()
                ->orderBy('sort_order')
                ->withCount(['products' => fn ($q) => $q->active()]),
            ])
            ->withCount(['products' => fn ($query) => $query->active()])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Categories/Index', [
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
