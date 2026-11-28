<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function cartWithProduct(User $user, float $price = 1000): Cart
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product-'.uniqid(),
            'sku' => 'TEST-'.uniqid(),
            'price' => $price,
            'stock' => 50,
            'status' => 'active',
            'manage_stock' => true,
        ]);

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $price,
        ]);

        return $cart;
    }

    private function coupon(array $attributes = []): Coupon
    {
        return Coupon::create(array_merge([
            'code' => 'SAVE10',
            'name' => 'Save Ten',
            'type' => 'percentage',
            'value' => 10,
            'minimum_order_amount' => 0,
            'maximum_discount' => null,
            'usage_limit_per_user' => 1,
            'is_active' => true,
        ], $attributes));
    }

    public function test_validate_api_returns_discount_for_valid_fixed_coupon(): void
    {
        $this->coupon(['code' => 'FLAT100', 'type' => 'fixed', 'value' => 100]);

        $this->postJson('/api/v1/coupons/validate', ['code' => 'FLAT100', 'order_total' => 1000])
            ->assertOk()
            ->assertJsonPath('discount', 100);
    }

    public function test_validate_api_caps_percentage_discount_at_maximum(): void
    {
        $this->coupon(['code' => 'BIG50', 'value' => 50, 'maximum_discount' => 100]);

        $this->postJson('/api/v1/coupons/validate', ['code' => 'BIG50', 'order_total' => 1000])
            ->assertOk()
            ->assertJsonPath('discount', 100);
    }

    public function test_validate_api_rejects_invalid_code(): void
    {
        $this->postJson('/api/v1/coupons/validate', ['code' => 'NOPE', 'order_total' => 1000])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Invalid coupon code.');
    }

    public function test_validate_api_rejects_order_below_minimum_amount(): void
    {
        $this->coupon(['minimum_order_amount' => 500]);

        $this->postJson('/api/v1/coupons/validate', ['code' => 'SAVE10', 'order_total' => 100])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Minimum order amount is 500.00.');
    }

    public function test_order_applies_coupon_discount_and_records_usage(): void
    {
        $user = User::factory()->create();
        $this->cartWithProduct($user, 1000);
        $this->coupon(['value' => 10]); // 10% of 1000 = 100

        $this->actingAs($user)
            ->post('/orders', [
                'shipping_address' => [
                    'name' => 'Test User',
                    'phone' => '01700000000',
                    'address_line1' => 'House 1, Road 2',
                    'city' => 'N/A',
                    'country' => 'BD',
                ],
                'payment_method' => 'cod',
                'coupon_code' => 'SAVE10',
            ])
            ->assertRedirect();

        $order = Order::firstOrFail();

        $this->assertSame('100.00', $order->discount);
        $this->assertSame('120.00', $order->shipping_cost);
        $this->assertSame('1020.00', $order->total);
        $this->assertSame('SAVE10', $order->coupon_code);

        $this->assertDatabaseHas('coupons', ['code' => 'SAVE10', 'used_count' => 1]);
        $this->assertDatabaseHas('coupon_usages', [
            'coupon_id' => $order->coupon_code ? Coupon::where('code', 'SAVE10')->first()->id : 0,
            'user_id' => $user->id,
            'order_id' => $order->id,
        ]);
    }

    public function test_order_rejects_invalid_coupon_code_with_validation_error(): void
    {
        $user = User::factory()->create();
        $this->cartWithProduct($user);

        $this->actingAs($user)
            ->post('/orders', [
                'shipping_address' => [
                    'name' => 'Test User',
                    'phone' => '01700000000',
                    'address_line1' => 'House 1, Road 2',
                    'city' => 'N/A',
                    'country' => 'BD',
                ],
                'payment_method' => 'cod',
                'coupon_code' => 'DOES-NOT-EXIST',
            ])
            ->assertSessionHasErrors('coupon_code');

        $this->assertNull(Order::first());
    }

    public function test_per_user_usage_limit_blocks_second_use(): void
    {
        $user = User::factory()->create();
        $this->coupon(['usage_limit_per_user' => 1]);

        $payload = [
            'shipping_address' => [
                'name' => 'Test User',
                'phone' => '01700000000',
                'address_line1' => 'House 1, Road 2',
                'city' => 'N/A',
                'country' => 'BD',
            ],
            'payment_method' => 'cod',
            'coupon_code' => 'SAVE10',
        ];

        // First order succeeds
        $this->cartWithProduct($user);
        $this->actingAs($user)->post('/orders', $payload)->assertRedirect();

        // Second order with the same coupon is rejected
        $this->cartWithProduct($user, 500);
        $this->actingAs($user)
            ->post('/orders', $payload)
            ->assertSessionHasErrors('coupon_code');
    }
}
