<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Customer',
            'phone' => '01700123456',
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $overrides);
    }

    public function test_customer_can_register_with_phone_only_no_email(): void
    {
        $this->post('/register', $this->payload())
            ->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('phone', '01700123456')->firstOrFail();
        $this->assertNull($user->email);
        $this->assertTrue($user->hasRole('customer'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_phone_is_normalized_to_digits(): void
    {
        $this->post('/register', $this->payload([
            'phone' => '+880 1700-1234-58',
        ]))->assertRedirect(route('dashboard', absolute: false));

        $this->assertSame('8801700123458', User::where('phone', '8801700123458')->first()->phone);
    }

    public function test_phone_is_required_for_registration(): void
    {
        $payload = $this->payload();
        unset($payload['phone']);

        $this->post('/register', $payload)->assertSessionHasErrors('phone');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_phone_must_be_unique(): void
    {
        User::factory()->create(['phone' => '01700123456']);

        $this->post('/register', $this->payload(['phone' => '8801700123456']))
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_registered_customer_can_login_with_their_phone(): void
    {
        $this->post('/register', $this->payload())->assertRedirect();

        auth()->logout();

        $this->post('/login', [
            'email' => '01700-123456', // formatted variant of the registered phone
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs(User::where('phone', '01700123456')->first());
    }

    public function test_phone_only_user_can_update_profile_without_email(): void
    {
        $this->post('/register', $this->payload(['phone' => '01700123999']))->assertRedirect();

        $user = User::where('phone', '01700123999')->first();

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Updated Name',
            ])
            ->assertRedirect('/profile');

        $this->assertSame('Updated Name', $user->fresh()->name);
        $this->assertNull($user->fresh()->email);
    }
}
