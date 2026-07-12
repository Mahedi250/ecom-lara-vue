<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->getRoleNames(),
                ] : null,
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'nav_categories' => fn() => Cache::remember('nav_categories', 3600, fn() =>
                Category::active()->root()->get(['id', 'name'])->toArray()
            ),
            'site_settings' => fn() => \Illuminate\Support\Facades\Cache::remember('site_settings_shared', 3600, function () {
                $logo    = \App\Models\Setting::get('site_logo');
                $icon    = \App\Models\Setting::get('site_icon');
                $favicon = \App\Models\Setting::get('favicon');
                return [
                    'logo_url'    => $logo    ? \Illuminate\Support\Facades\Storage::disk('public')->url($logo)    : null,
                    'icon_url'    => $icon    ? \Illuminate\Support\Facades\Storage::disk('public')->url($icon)    : null,
                    'favicon_url' => $favicon ? \Illuminate\Support\Facades\Storage::disk('public')->url($favicon) : null,
                ];
            }),
            'notification_sound' => fn() => (bool) \App\Models\Setting::get('notification.sound_enabled', true),
            'published_pages' => fn() => Cache::remember('published_pages_shared', 3600, fn() =>
                Page::published()->orderBy('title')->get(['title', 'slug'])->toArray()
            ),
            'cart_count' => function () use ($request) {
                if ($request->user()) {
                    return \App\Models\Cart::where('user_id', $request->user()->id)
                        ->withSum('items', 'quantity')
                        ->first()?->items_sum_quantity ?? 0;
                }
                $sessionId = $request->session()->get('cart_session_id');
                if (!$sessionId) return 0;
                return \App\Models\Cart::where('session_id', $sessionId)
                    ->withSum('items', 'quantity')
                    ->first()?->items_sum_quantity ?? 0;
            },
        ];
    }
}
