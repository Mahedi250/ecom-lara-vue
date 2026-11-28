<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_page_lists_active_root_categories_with_children(): void
    {
        $electronics = Category::create(['name' => 'Electronics', 'slug' => 'electronics', 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Hidden', 'slug' => 'hidden', 'is_active' => false]);
        Category::create(['name' => 'Phones', 'slug' => 'phones', 'is_active' => true, 'parent_id' => $electronics->id]);

        $this->get('/categories')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Categories/Index')
                ->has('categories.data', 1)
                ->where('categories.data.0.name', 'Electronics')
                ->has('categories.data.0.children', 1)
                ->where('categories.data.0.children.0.name', 'Phones'));
    }

    public function test_categories_page_renders_empty_state_when_none_exist(): void
    {
        $this->get('/categories')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Categories/Index')
                ->has('categories.data', 0));
    }
}
