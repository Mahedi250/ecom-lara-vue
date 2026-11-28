<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\AdminMenu;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePanelAccess
{
    /**
     * Whether the user may enter the admin panel: built-in admin/staff roles,
     * or any custom role granted at least one admin-menu permission.
     */
    public static function allows(User $user): bool
    {
        return $user->hasRole(['admin', 'staff'])
            || collect(AdminMenu::permissions())->contains(
                fn ($permission) => $user->checkPermissionTo($permission)
            );
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user && self::allows($user), 403, 'You do not have access to the admin panel.');

        return $next($request);
    }
}
