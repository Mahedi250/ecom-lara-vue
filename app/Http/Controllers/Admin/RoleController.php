<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::withCount('users', 'permissions')
                ->whereNotIn('name', AdminMenu::storefrontRoles())
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Roles/Create', [
            'menuItems' => $this->menuItems(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        AdminMenu::syncToDatabase();

        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" created.");
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('Admin/Roles/Edit', [
            'role' => ['id' => $role->id, 'name' => $role->name, 'is_system' => in_array($role->name, ['admin', 'staff'])],
            'rolePermissions' => $role->permissions()->pluck('name')->all(),
            'menuItems' => $this->menuItems(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        // Storefront roles (customer/vendor) are managed by the system —
        // editing them here could grant panel access to every customer.
        if (in_array($role->name, AdminMenu::storefrontRoles())) {
            abort(403, 'Storefront roles cannot be edited here.');
        }

        $data = $this->validated($request, $role);

        AdminMenu::syncToDatabase();

        if ($role->name !== 'admin') {
            $role->update(['name' => $data['name']]);
        }
        $role->syncPermissions($data['permissions'] ?? []);

        return back()->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['admin', 'staff'])) {
            return back()->with('error', "The \"{$role->name}\" role is a system role and cannot be deleted.");
        }

        if (in_array($role->name, AdminMenu::storefrontRoles())) {
            return back()->with('error', "The \"{$role->name}\" role is used by the storefront and cannot be deleted.");
        }

        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role?->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(AdminMenu::permissions())],
        ]);
    }

    /**
     * The registry tree rendered by the access editor.
     */
    private function menuItems(): array
    {
        return collect(AdminMenu::items())
            ->map(fn ($item) => [
                'key' => $item['key'],
                'label' => $item['label'],
                'group' => $item['group'],
                'permission' => $item['permission'],
                'actions' => collect($item['actions'] ?? [])
                    ->map(fn ($label, $name) => ['name' => $name, 'label' => $label])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }
}
