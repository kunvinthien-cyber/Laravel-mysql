<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosCheckoutPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private function createCashierWithShop(): User
    {
        $user = User::factory()->create([
            'role' => 'cashier',
        ]);

        $shop = Shop::create([
            'name' => 'Test Shop',
            'status' => 'active',
        ]);

        $user->update(['shop_id' => $shop->id]);

        return $user->fresh();
    }

    public function test_cashier_can_checkout_with_payment_method_and_customer_points_are_recorded(): void
    {
        $user = $this->createCashierWithShop();

        $customer = Customer::create([
            'shop_id' => $user->shop_id,
            'name' => 'Sok Dara',
            'email' => 'sokdara@example.com',
            'phone' => '012 345 678',
            'address' => 'Phnom Penh',
            'points' => 40,
        ]);

        $product = Product::create([
            'shop_id' => $user->shop_id,
            'name' => 'Coffee',
            'price' => 10.00,
            'stock' => 5,
        ]);

        $this->actingAs($user)
            ->postJson('/pos/checkout', [
                'customer_id' => $customer->id,
                'payment_method' => 'aba',
                'cart' => [
                    ['id' => $product->id, 'qty' => 2],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'cashier_id' => $user->id,
            'payment_method' => 'aba',
        ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'points' => 60,
        ]);
    }

    public function test_customer_can_be_created_without_name_phone_or_email(): void
    {
        $user = $this->createCashierWithShop();

        $this->actingAs($user)
            ->postJson('/customers', [
                'name' => '',
                'phone' => '',
                'email' => '',
                'points' => 0,
                'address' => '',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('customers', [
            'name' => 'Walk-in customer',
            'phone' => null,
            'email' => null,
            'points' => 0,
        ]);
    }

    public function test_cashier_can_checkout_without_selecting_a_customer(): void
    {
        $user = $this->createCashierWithShop();

        $product = Product::create([
            'shop_id' => $user->shop_id,
            'name' => 'Coffee',
            'price' => 10.00,
            'stock' => 5,
        ]);

        $this->actingAs($user)
            ->postJson('/pos/checkout', [
                'payment_method' => 'cash',
                'cart' => [
                    ['id' => $product->id, 'qty' => 1],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('orders', [
            'customer_id' => null,
            'payment_method' => 'cash',
        ]);
    }

    public function test_checkout_uses_the_authenticated_shops_tax_rate(): void
    {
        $user = $this->createCashierWithShop();
        $product = Product::create([
            'shop_id' => $user->shop_id,
            'name' => 'Taxed Coffee',
            'price' => 10.00,
            'stock' => 5,
        ]);
        Setting::create([
            'shop_id' => $user->shop_id,
            'key' => 'tax_rate',
            'value' => '10',
        ]);

        $this->actingAs($user)
            ->postJson('/pos/checkout', [
                'payment_method' => 'cash',
                'cart' => [['id' => $product->id, 'qty' => 2]],
            ])
            ->assertOk();

        $this->assertDatabaseHas('orders', [
            'shop_id' => $user->shop_id,
            'total' => 22.00,
        ]);
    }
}
