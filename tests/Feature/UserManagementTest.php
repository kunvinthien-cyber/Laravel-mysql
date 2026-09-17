<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_shop_owner_account(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post('/users', [
                'name' => 'Shop Owner',
                'email' => 'owner@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'owner',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Shop Owner',
            'email' => 'owner@example.com',
            'role' => 'owner',
        ]);

        $this->assertTrue(Hash::check('password123', User::where('email', 'owner@example.com')->value('password')));
        $this->assertDatabaseHas('shops', [
            'name' => 'Shop Owner',
            'owner_id' => User::where('email', 'owner@example.com')->value('id'),
            'status' => 'active',
        ]);
    }

    public function test_non_admin_cannot_manage_user_accounts(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
        ]);

        $this->actingAs($owner)
            ->get('/users')
            ->assertForbidden();
    }

    public function test_admin_cannot_create_another_admin_account(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from('/users/create')
            ->post('/users', [
                'name' => 'Second Admin',
                'email' => 'second-admin@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'admin',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', [
            'email' => 'second-admin@example.com',
        ]);
    }

    public function test_admin_can_suspend_and_activate_a_shop(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'owner']);
        $shop = Shop::create([
            'owner_id' => $owner->id,
            'name' => 'Test Shop',
            'status' => 'active',
        ]);
        $owner->update(['shop_id' => $shop->id]);

        $this->actingAs($admin)
            ->patch(route('shops.suspend', $shop))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'status' => 'suspended',
        ]);
    }

    public function test_suspended_owner_is_logged_out_when_opening_the_dashboard(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $shop = Shop::create([
            'owner_id' => $owner->id,
            'name' => 'Suspended Shop',
            'status' => 'suspended',
        ]);
        $owner->update(['shop_id' => $shop->id]);

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_update_a_shop_without_an_owner(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $shop = Shop::create([
            'name' => 'Orphan Shop',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->put(route('shops.update', $shop), [
                'name' => 'Updated Shop',
                'phone' => '012345678',
                'email' => 'shop@example.com',
                'address' => 'Phnom Penh',
                'status' => 'active',
                'owner_name' => 'Pending Owner',
                'owner_email' => 'pending@example.com',
            ])
            ->assertRedirect(route('shops.index'));

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Updated Shop',
            'email' => 'shop@example.com',
        ]);
    }

    public function test_owner_settings_update_shop_information(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $shop = Shop::create([
            'owner_id' => $owner->id,
            'name' => 'Old Shop',
            'status' => 'active',
        ]);
        $owner->update(['shop_id' => $shop->id]);

        $this->actingAs($owner)
            ->post(route('settings.update'), [
                'shop_name' => 'New Shop',
                'shop_phone' => '098765432',
                'shop_email' => 'new-shop@example.com',
                'shop_address' => 'Siem Reap',
                'currency_symbol' => '$',
                'tax_rate' => '10',
            ])
            ->assertRedirect(route('settings.index'));

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'New Shop',
            'phone' => '098765432',
            'email' => 'new-shop@example.com',
        ]);
        $this->assertDatabaseHas('settings', [
            'shop_id' => $shop->id,
            'key' => 'tax_rate',
            'value' => '10',
        ]);
    }

    public function test_admin_can_create_a_store_with_an_owner_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('shops.store'), [
                'name' => 'New Store',
                'phone' => '012345678',
                'email' => 'store@example.com',
                'owner_name' => 'New Owner',
                'owner_email' => 'new-owner@example.com',
                'owner_password' => 'password123',
                'owner_password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('shops.index'));

        $owner = User::where('email', 'new-owner@example.com')->first();

        $this->assertNotNull($owner);
        $this->assertSame('owner', $owner->role);
        $this->assertNotNull($owner->shop_id);
        $this->assertDatabaseHas('shops', [
            'id' => $owner->shop_id,
            'name' => 'New Store',
            'owner_id' => $owner->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('settings', [
            'shop_id' => $owner->shop_id,
            'key' => 'shop_name',
            'value' => 'New Store',
        ]);
    }

    public function test_owner_can_view_shop_reports(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $shop = Shop::create([
            'owner_id' => $owner->id,
            'name' => 'Reporting Shop',
            'status' => 'active',
        ]);
        $owner->update(['shop_id' => $shop->id]);

        $this->actingAs($owner)
            ->get(route('reports.index'))
            ->assertOk();
    }

    public function test_owner_can_create_staff_for_their_shop(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $shop = Shop::create([
            'owner_id' => $owner->id,
            'name' => 'Staff Shop',
            'status' => 'active',
        ]);
        $owner->update(['shop_id' => $shop->id]);

        $this->actingAs($owner)
            ->post(route('staff.store'), [
                'name' => 'Cashier One',
                'email' => 'cashier-one@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'cashier',
            ])
            ->assertRedirect(route('staff.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'cashier-one@example.com',
            'role' => 'cashier',
            'shop_id' => $shop->id,
        ]);
    }

    public function test_staff_can_view_orders_but_cannot_mutate_products_or_orders(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $shop = Shop::create([
            'owner_id' => $owner->id,
            'name' => 'Permissions Shop',
            'status' => 'active',
        ]);
        $owner->update(['shop_id' => $shop->id]);

        $staff = User::factory()->create([
            'role' => 'staff',
            'shop_id' => $shop->id,
        ]);
        $order = Order::create([
            'shop_id' => $shop->id,
            'total' => 20,
            'status' => 'completed',
        ]);

        $this->actingAs($staff)
            ->get(route('orders.index'))
            ->assertOk();

        $this->actingAs($staff)
            ->get(route('products.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('orders.edit', $order))
            ->assertForbidden();

        $this->actingAs($staff)
            ->delete(route('orders.destroy', $order))
            ->assertForbidden();
    }

    public function test_owner_can_download_a_pdf_report(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $shop = Shop::create([
            'owner_id' => $owner->id,
            'name' => 'PDF Shop',
            'status' => 'active',
        ]);
        $owner->update(['shop_id' => $shop->id]);

        Order::create([
            'shop_id' => $shop->id,
            'total' => 25,
            'status' => 'completed',
        ]);

        $this->actingAs($owner)
            ->get(route('reports.pdf'))
            ->assertDownload('sales-report.pdf');
    }
}
