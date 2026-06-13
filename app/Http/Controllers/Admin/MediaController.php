<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function index(Request $request): Response|JsonResponse
    {
        $media = Media::latest()->paginate($request->integer('per_page', 40));

        if ($request->expectsJson() && !$request->header('X-Inertia')) {
            return response()->json(['media' => $media]);
        }

        return Inertia::render('Admin/Media/Index', [
            'media' => $media,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'max:4096'],
        ]);

        $path = $this->mediaService->upload($request->file('file'), 'media');
        $media = Media::where('path', $path)->first();

        return response()->json([
            'id'   => $media->id,
            'url'  => $media->url,
            'path' => $media->path,
            'name' => $media->original_name,
        ]);
    }

    public function destroy(Media $medium): RedirectResponse
    {
        $this->mediaService->delete($medium->path, $medium->disk);

        return back()->with('success', 'File deleted.');
    }
}
