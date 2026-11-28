<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_login_with_email(): void
    {
        $user = User::factory()->create(['phone' => '01700000001']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_phone(): void
    {
        $user = User::factory()->create(['phone' => '01700000002']);

        $this->post('/login', [
            'email' => ' 01700-000002 ', // formatted with spaces/dashes
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_country_code_prefix(): void
    {
        $user = User::factory()->create(['phone' => '8801700000003']);

        $this->post('/login', [
            'email' => '01700000003', // typed without country code
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_login_with_phone(): void
    {
        $admin = User::factory()->create(['phone' => '01700000004'])->assignRole('admin');

        $this->post('/admin/login', [
            'email' => '01700000004',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_cannot_login_with_wrong_password_via_phone(): void
    {
        $user = User::factory()->create(['phone' => '01700000005']);

        $this->post('/login', [
            'email' => '01700000005',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_phone_must_be_unique_on_profile_update(): void
    {
        $user = User::factory()->create(['phone' => '01700000006']);
        $other = User::factory()->create(['phone' => '01700000007']);

        $this->actingAs($other)
            ->patch('/profile', [
                'name' => $other->name,
                'email' => $other->email,
                'phone' => '01700000006', // already taken by $user
            ])
            ->assertSessionHasErrors('phone');

        $this->assertSame('01700000007', $other->fresh()->phone);
    }

    public function test_profile_phone_is_normalized_to_digits(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '+880 1700-000008',
            ])
            ->assertRedirect('/profile');

        $this->assertSame('8801700000008', $user->fresh()->phone);
    }
}
