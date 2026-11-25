<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function cartWithProduct(User $user, float $price = 1000, int $quantity = 1): Cart
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TEST-001',
            'price' => $price,
            'stock' => 50,
            'status' => ProductStatus::Active,
            'manage_stock' => true,
        ]);

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $price,
        ]);

        return $cart;
    }

    public function test_checkout_page_renders_for_a_user_with_items(): void
    {
        $user = User::factory()->create();
        $this->cartWithProduct($user);

        $this->actingAs($user)
            ->get('/checkout')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Checkout/Index')
                ->has('cart.items', 1));
    }

    public function test_order_can_be_placed_with_only_name_phone_and_address(): void
    {
        $user = User::factory()->create();
        $this->cartWithProduct($user);

        $this->actingAs($user)
            ->post('/orders', [
                'shipping_address' => [
                    'name' => 'Test User',
                    'phone' => '01700000000',
                    'address_line1' => 'House 1, Road 2, Dhaka',
                    'city' => 'N/A',
                    'country' => 'BD',
                ],
                'payment_method' => 'cod',
            ])
            ->assertRedirect();

        $order = Order::firstOrFail();

        $this->assertSame('1000.00', $order->subtotal);
        $this->assertSame('120.00', $order->shipping_cost);
        $this->assertSame('1120.00', $order->total);

        $this->assertDatabaseHas('order_addresses', [
            'order_id' => $order->id,
            'type' => 'shipping',
            'name' => 'Test User',
            'phone' => '01700000000',
            'address_line1' => 'House 1, Road 2, Dhaka',
            'city' => 'N/A',
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'gateway' => 'cod',
        ]);
    }

    public function test_pickup_order_has_free_shipping(): void
    {
        $user = User::factory()->create();
        $this->cartWithProduct($user, 1000);

        $this->actingAs($user)
            ->post('/orders', [
                'shipping_address' => [
                    'name' => 'Test User',
                    'phone' => '01700000000',
                    'address_line1' => 'Collect from store',
                    'city' => 'N/A',
                    'country' => 'BD',
                ],
                'payment_method' => 'pickup',
            ])
            ->assertRedirect();

        $order = Order::firstOrFail();

        $this->assertSame('0.00', $order->shipping_cost);
        $this->assertSame('1000.00', $order->total);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'gateway' => 'pickup',
        ]);
    }

    public function test_name_phone_and_address_are_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/orders', [])
            ->assertSessionHasErrors([
                'shipping_address.name',
                'shipping_address.phone',
                'shipping_address.address_line1',
                'payment_method',
            ]);
    }
}
