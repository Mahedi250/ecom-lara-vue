<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (filter_var(Setting::get('maintenance_mode'), FILTER_VALIDATE_BOOLEAN)) {
            // Admin users and admin routes pass through
            if ($request->user()?->hasRole('admin') || str_starts_with($request->path(), 'admin')) {
                return $next($request);
            }

            return Inertia::render('Maintenance')
                ->toResponse($request)
                ->setStatusCode(503);
        }

        return $next($request);
    }
}
