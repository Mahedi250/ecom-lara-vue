<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Support\AdminMenu;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function staff(): User
    {
        return User::factory()->create()->assignRole('staff');
    }

    public function test_staff_can_access_sections_their_role_grants(): void
    {
        $this->actingAs($this->staff())
            ->get('/admin/products')
            ->assertOk();

        $this->actingAs($this->staff())
            ->get('/admin/orders')
            ->assertOk();
    }

    public function test_staff_cannot_access_sections_outside_their_role(): void
    {
        $staff = $this->staff();

        // No settings.view / settings.edit permission -> bounced back with popup
        $this->actingAs($staff)
            ->get('/admin/settings')
            ->assertRedirect()
            ->assertSessionHas('error', 'You are not authorized to access that page.');

        $this->actingAs($staff)->post('/admin/settings')->assertRedirect()->assertSessionHas('error');

        // No users.delete permission
        $victim = User::factory()->create();
        $this->actingAs($staff)->delete('/admin/users/'.$victim->id)->assertRedirect()->assertSessionHas('error');

        // No coupons.create permission
        $this->actingAs($staff)->get('/admin/coupons/create')->assertRedirect()->assertSessionHas('error');
    }

    public function test_visitor_without_panel_access_is_sent_to_storefront(): void
    {
        $customer = User::factory()->create()->assignRole('customer');

        $this->actingAs($customer)
            ->get('/admin/settings')
            ->assertRedirect('/');
    }

    public function test_admin_bypasses_all_permission_checks(): void
    {
        $admin = User::factory()->create()->assignRole('admin');

        $this->actingAs($admin)->get('/admin/settings')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/coupons/create')->assertOk();
    }

    public function test_orders_status_update_requires_orders_edit_permission(): void
    {
        // A custom role that can view orders but not update them
        $viewerRole = Role::create(['name' => 'Order Viewer']);
        $viewerRole->syncPermissions(['orders.view']);

        $viewer = User::factory()->create()->assignRole($viewerRole);
        $order = Order::create([
            'user_id' => $viewer->id,
            'order_number' => 'ORD-TEST-1',
            'subtotal' => 100,
            'total' => 100,
        ]);

        $this->actingAs($viewer)
            ->patch('/admin/orders/'.$order->id.'/status', ['status' => 'processing'])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        // Grant orders.edit — now allowed
        $viewer->givePermissionTo('orders.edit');
        $this->actingAs($viewer)
            ->patch('/admin/orders/'.$order->id.'/status', ['status' => 'processing'])
            ->assertRedirect();
    }

    public function test_seeder_syncs_all_registry_permissions_to_admin_role(): void
    {
        $admin = Role::where('name', 'admin')->first();

        foreach (AdminMenu::permissions() as $permission) {
            $this->assertTrue(
                $admin->hasPermissionTo($permission),
                "Admin role is missing permission [{$permission}]."
            );
        }
    }
}
