<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminMenu;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserCredentialsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_update_a_users_password(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $victim = User::factory()->create(['password' => Hash::make('old-password')]);

        $this->actingAs($admin)
            ->patch('/admin/users/'.$victim->id.'/password', [
                'password' => 'new-secret-pass',
                'password_confirmation' => 'new-secret-pass',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('new-secret-pass', $victim->fresh()->password));
        $this->assertFalse(Hash::check('old-password', $victim->fresh()->password));
    }

    public function test_password_must_be_confirmed_and_min_8_chars(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $victim = User::factory()->create();

        $this->actingAs($admin)
            ->patch('/admin/users/'.$victim->id.'/password', [
                'password' => 'short',
                'password_confirmation' => 'short',
            ])
            ->assertSessionHasErrors('password');

        // Mismatched confirmation
        $this->actingAs($admin)
            ->patch('/admin/users/'.$victim->id.'/password', [
                'password' => 'long-enough-password',
                'password_confirmation' => 'different-password',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_users_without_credentials_permission_cannot_change_passwords(): void
    {
        // Staff role has no users.credentials permission
        $staff = User::factory()->create()->assignRole('staff');
        $victim = User::factory()->create();

        $this->actingAs($staff)
            ->patch('/admin/users/'.$victim->id.'/password', [
                'password' => 'new-secret-pass',
                'password_confirmation' => 'new-secret-pass',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_role_with_credentials_permission_can_change_passwords(): void
    {
        $role = Role::create(['name' => 'Support']);
        $role->syncPermissions(['users.view', 'users.credentials']);

        $support = User::factory()->create()->assignRole($role);
        $victim = User::factory()->create();

        $this->actingAs($support)
            ->patch('/admin/users/'.$victim->id.'/password', [
                'password' => 'new-secret-pass',
                'password_confirmation' => 'new-secret-pass',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('new-secret-pass', $victim->fresh()->password));
    }

    public function test_registry_contains_credentials_permission(): void
    {
        $this->assertContains('users.credentials', AdminMenu::permissions());
    }

    public function test_admin_can_create_a_new_user(): void
    {
        $admin = User::factory()->create()->assignRole('admin');

        $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'New Manager',
                'email' => 'manager@example.com',
                'phone' => '01700000099',
                'password' => 'strong-password',
                'password_confirmation' => 'strong-password',
                'status' => 'active',
                'roles' => ['staff'],
            ])
            ->assertRedirect();

        $user = User::where('email', 'manager@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('strong-password', $user->password));
        $this->assertTrue($user->hasRole('staff'));
        $this->assertSame('01700000099', $user->phone);
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('active', $user->status->value);
    }

    public function test_user_creation_validates_unique_email_phone_and_password(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        User::factory()->create(['email' => 'taken@example.com', 'phone' => '01700000100']);

        $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Dup',
                'email' => 'taken@example.com',
                'phone' => '01700000100',
                'password' => 'short',
                'password_confirmation' => 'short',
                'status' => 'active',
            ])
            ->assertSessionHasErrors(['email', 'phone', 'password']);
    }

    public function test_users_without_create_permission_cannot_create_users(): void
    {
        $staff = User::factory()->create()->assignRole('staff');

        $this->actingAs($staff)
            ->post('/admin/users', [
                'name' => 'Nope',
                'email' => 'nope@example.com',
                'password' => 'strong-password',
                'password_confirmation' => 'strong-password',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseMissing('users', ['email' => 'nope@example.com']);
    }

    public function test_user_show_page_receives_unwrapped_user_and_roles(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $victim = User::factory()->create()->assignRole('staff');

        $this->actingAs($admin)
            ->get('/admin/users/'.$victim->id)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Users/Show')
                ->where('user.id', $victim->id)
                ->where('user.roles.0', 'staff')
                ->has('roles', 2)); // admin + staff only — customer/vendor are storefront roles
    }

    public function test_create_page_only_offers_admin_panel_roles(): void
    {
        $admin = User::factory()->create()->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/users/create')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Users/Create')
                ->has('roles', 2)
                ->where('roles.0.name', 'admin')
                ->where('roles.1.name', 'staff'));
    }
}
