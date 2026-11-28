<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_create_a_role_with_permissions(): void
    {
        $admin = User::factory()->create()->assignRole('admin');

        $this->actingAs($admin)
            ->post('/admin/roles', [
                'name' => 'Accountant',
                'permissions' => ['orders.view', 'orders.edit', 'coupons.view'],
            ])
            ->assertRedirect('/admin/roles');

        $role = Role::where('name', 'Accountant')->firstOrFail();
        $this->assertTrue($role->hasPermissionTo('orders.view'));
        $this->assertTrue($role->hasPermissionTo('orders.edit'));
        $this->assertFalse($role->hasPermissionTo('products.view'));

        // A user with this role can access orders but not products management pages
        $accountant = User::factory()->create()->assignRole('Accountant');
        $this->actingAs($accountant)->get('/admin/orders')->assertOk();
        $this->actingAs($accountant)
            ->get('/admin/products')
            ->assertRedirect()
            ->assertSessionHas('error', 'You are not authorized to access that page.');
    }

    public function test_admin_can_update_role_permissions(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $role = Role::where('name', 'staff')->first();

        $this->actingAs($admin)
            ->patch('/admin/roles/'.$role->id, [
                'name' => 'staff',
                'permissions' => ['products.view'],
            ])
            ->assertRedirect();

        $this->assertTrue($role->fresh()->hasPermissionTo('products.view'));
        $this->assertFalse($role->fresh()->hasPermissionTo('orders.view'));
    }

    public function test_only_admin_can_manage_roles(): void
    {
        $staff = User::factory()->create()->assignRole('staff');

        $this->actingAs($staff)
            ->get('/admin/roles')
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->actingAs($staff)->get('/admin/roles/create')->assertRedirect()->assertSessionHas('error');
        $this->actingAs($staff)
            ->post('/admin/roles', ['name' => 'Hacked'])
            ->assertRedirect();

        $this->assertDatabaseMissing('roles', ['name' => 'Hacked']);
    }

    public function test_saving_a_role_auto_creates_missing_registry_permissions(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $role = Role::create(['name' => 'Temp Role']);

        // Simulate a fresh deploy where the seeder hasn't run yet:
        // the permission exists in the registry but not in the database.
        Permission::where('name', 'media.view')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($admin)
            ->patch('/admin/roles/'.$role->id, [
                'name' => 'Temp Role',
                'permissions' => ['media.view'],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('permissions', ['name' => 'media.view']);
        $this->assertTrue($role->fresh()->hasPermissionTo('media.view'));
    }

    public function test_storefront_roles_do_not_appear_in_access_editor(): void
    {
        $admin = User::factory()->create()->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/roles')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Roles/Index')
                ->has('roles', 2) // admin + staff only
                ->where('roles.0.name', 'admin')
                ->where('roles.1.name', 'staff'));
    }

    public function test_storefront_roles_cannot_be_edited_or_deleted(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $customer = Role::where('name', 'customer')->first();

        // Cannot grant panel permissions to the customer role
        $this->actingAs($admin)
            ->patch('/admin/roles/'.$customer->id, [
                'name' => 'customer',
                'permissions' => ['products.view'],
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertFalse($customer->hasPermissionTo('products.view'));

        // Cannot delete it either (registration depends on it)
        $this->actingAs($admin)
            ->delete('/admin/roles/'.$customer->id)
            ->assertRedirect();

        $this->assertDatabaseHas('roles', ['name' => 'customer']);
    }

    public function test_custom_role_user_can_login_to_admin_panel(): void
    {
        // Reproduces the "Order Manager" case: no admin/staff role, but has
        // admin-menu permissions — must be able to log in at /admin/login.
        $role = Role::create(['name' => 'Order Manager']);
        $role->syncPermissions(['orders.view', 'orders.edit']);

        $manager = User::factory()->create([
            'phone' => '0167744439',
            'password' => 'secret-password',
        ]);
        $manager->assignRole($role);

        $this->post('/admin/login', [
            'email' => '0167744439',
            'password' => 'secret-password',
        ])->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs($manager);
    }

    public function test_user_without_any_admin_permission_cannot_login_to_admin_panel(): void
    {
        $customer = User::factory()->create(['password' => 'secret-password']);

        $this->post('/admin/login', [
            'email' => $customer->email,
            'password' => 'secret-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_system_roles_cannot_be_deleted(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $adminRole = Role::where('name', 'admin')->first();

        $this->actingAs($admin)
            ->delete('/admin/roles/'.$adminRole->id)
            ->assertRedirect();

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    public function test_custom_roles_can_be_deleted(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $role = Role::create(['name' => 'Temp']);

        $this->actingAs($admin)
            ->delete('/admin/roles/'.$role->id)
            ->assertRedirect('/admin/roles');

        $this->assertDatabaseMissing('roles', ['name' => 'Temp']);
    }
}
