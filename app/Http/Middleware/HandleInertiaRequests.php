<?php

namespace App\Http\Middleware;

use App\Models\Category;
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
                    'logo_url'        => $logo    ? \Illuminate\Support\Facades\Storage::disk('public')->url($logo)    : null,
                    'icon_url'        => $icon    ? \Illuminate\Support\Facades\Storage::disk('public')->url($icon)    : null,
                    'favicon_url'     => $favicon ? \Illuminate\Support\Facades\Storage::disk('public')->url($favicon) : null,
                    'store_name'      => \App\Models\Setting::get('store_name', config('app.name')),
                    'store_email'     => \App\Models\Setting::get('store_email'),
                    'store_phone'     => \App\Models\Setting::get('store_phone'),
                    'store_address'   => \App\Models\Setting::get('store_address'),
                    'currency_symbol' => \App\Models\Setting::get('currency_symbol', '৳'),
                    'meta_title'      => \App\Models\Setting::get('meta_title'),
                    'meta_description'=> \App\Models\Setting::get('meta_description'),
                    'ga_id'           => \App\Models\Setting::get('ga_id'),
                    'social' => [
                        'facebook'  => \App\Models\Setting::get('facebook'),
                        'instagram' => \App\Models\Setting::get('instagram'),
                        'twitter'   => \App\Models\Setting::get('twitter'),
                        'youtube'   => \App\Models\Setting::get('youtube'),
                    ],
                ];
            }),
            'notification_sound' => fn() => (bool) \App\Models\Setting::get('sound_enabled', true),
            'guest_checkout_enabled' => fn() => filter_var(\App\Models\Setting::get('guest_checkout', '1'), FILTER_VALIDATE_BOOLEAN),
            'shipping_settings' => fn() => [
                'default_cost'    => (float) \App\Models\Setting::get('default_cost', 120),
                'free_threshold'  => (float) \App\Models\Setting::get('free_threshold', 2000),
            ],
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
