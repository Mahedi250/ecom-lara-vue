<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::active()->root()->with('children')->get();

        return CategoryResource::collection($categories);
    }
}
