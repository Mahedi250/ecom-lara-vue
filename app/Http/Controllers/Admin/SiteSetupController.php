<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SiteSetupController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/SiteSetup/Index', [
            'slides' => HeroSlide::orderBy('sort_order')->get(),
            'assets' => [
                'logo'    => ($v = Setting::get('site_logo'))    ? Storage::disk('public')->url($v) : null,
                'icon'    => ($v = Setting::get('site_icon'))    ? Storage::disk('public')->url($v) : null,
                'favicon' => ($v = Setting::get('favicon'))      ? Storage::disk('public')->url($v) : null,
            ],
        ]);
    }

    public function storeSlide(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'image'       => 'required|string',
            'href'        => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        HeroSlide::create($data);

        return back()->with('success', 'Slide added.');
    }

    public function updateSlide(Request $request, HeroSlide $slide): RedirectResponse
    {
        $data = $request->validate([
            'image'       => 'nullable|string',
            'href'        => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $slide->update($data);

        return back()->with('success', 'Slide updated.');
    }

    public function destroySlide(HeroSlide $slide): RedirectResponse
    {
        $slide->delete();

        return back()->with('success', 'Slide deleted.');
    }

    public function updateAssets(Request $request): RedirectResponse
    {
        $request->validate([
            'site_logo' => 'nullable|string',
            'site_icon' => 'nullable|string',
            'favicon'   => 'nullable|string',
        ]);

        if ($request->filled('site_logo')) Setting::set('site_logo', $request->site_logo, 'branding');
        if ($request->filled('site_icon')) Setting::set('site_icon', $request->site_icon, 'branding');
        if ($request->filled('favicon'))   Setting::set('favicon',   $request->favicon,   'branding');

        return back()->with('success', 'Site assets saved.');
    }

    public function reorderSlides(Request $request): RedirectResponse
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $index => $id) {
            HeroSlide::where('id', $id)->update(['sort_order' => $index]);
        }

        return back()->with('success', 'Slides reordered.');
    }
}
