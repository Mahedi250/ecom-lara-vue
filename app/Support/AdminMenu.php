<?php

namespace App\Support;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AdminMenu
{
    /**
     * All registry items as defined in config/admin-menu.php.
     */
    public static function items(): array
    {
        return config('admin-menu.items', []);
    }

    /**
     * All permission names (view + actions) defined in the registry.
     */
    public static function permissions(): array
    {
        return collect(self::items())
            ->flatMap(fn ($item) => array_merge(
                [$item['permission']],
                array_keys($item['actions'] ?? [])
            ))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Create any registry permissions missing from the database.
     * Keeps the system self-healing when new menu items are added.
     */
    public static function syncToDatabase(): void
    {
        foreach (self::permissions() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Storefront-only roles (e.g. auto-assigned on customer registration).
     * These never appear in, and are never editable from, the access editor.
     */
    public static function storefrontRoles(): array
    {
        return config('admin-menu.storefront_roles', ['customer', 'vendor']);
    }

    /**
     * Sidebar items the user is allowed to see.
     */
    public static function forUser(User $user): array
    {
        return collect(self::items())
            ->filter(fn ($item) => empty($item['permission'])
                || $user->hasRole('admin')
                || $user->checkPermissionTo($item['permission']))
            ->map(fn ($item) => [
                'key' => $item['key'],
                'label' => $item['label'],
                'group' => $item['group'],
                'route' => $item['route'],
            ])
            ->values()
            ->all();
    }
}
