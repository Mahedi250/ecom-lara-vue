<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function show(Page $page): Response
    {
        abort_if($page->status !== 'published', 404);

        return Inertia::render('Pages/Show', [
            'page' => [
                'title' => $page->title,
                'content' => $page->content,
                'meta_title' => $page->getSeoTitle(),
                'meta_description' => $page->getSeoDescription(),
            ],
        ]);
    }
}
