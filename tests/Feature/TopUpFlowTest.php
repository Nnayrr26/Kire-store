<?php

namespace Tests\Feature;

use App\Models\TopUpOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopUpFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_mobile_legends_packages(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('topup.mobile-legends'))
            ->assertOk()
            ->assertSee('Mobile Legends')
            ->assertSee('86 Diamonds')
            ->assertSee('User ID')
            ->assertSee('Zone ID');
    }

    public function test_user_can_create_a_pending_order_using_the_server_side_package_price(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('topup.mobile-legends.store'), [
            'player_id' => '123456789',
            'zone_id' => '1234',
            'package_key' => 'ml-86',
            'price' => 1,
            'diamonds' => 999999,
            'status' => 'completed',
        ]);

        $order = TopUpOrder::firstOrFail();

        $response->assertRedirect(route('dashboard.user'));
        $response->assertSessionHas('success');
        $this->assertSame($user->id, $order->user_id);
        $this->assertSame('Mobile Legends', $order->game);
        $this->assertSame('123456789', $order->player_id);
        $this->assertSame('1234', $order->zone_id);
        $this->assertSame('86 Diamonds', $order->package_name);
        $this->assertSame(86, $order->diamonds);
        $this->assertSame(20000, $order->price);
        $this->assertSame('pending', $order->status);

        $this->get(route('dashboard.user'))
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertSee('Menunggu konfirmasi');
    }

    public function test_invalid_player_details_or_unknown_packages_are_rejected(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->from(route('topup.mobile-legends'))
            ->post(route('topup.mobile-legends.store'), [
                'player_id' => 'player-name',
                'zone_id' => '12',
                'package_key' => 'ml-86',
            ])
            ->assertRedirect(route('topup.mobile-legends'))
            ->assertSessionHasErrors('player_id');

        $this->post(route('topup.mobile-legends.store'), [
            'player_id' => '123456789',
            'zone_id' => '12',
            'package_key' => 'not-a-package',
        ])->assertSessionHasErrors('package_key');

        $this->assertDatabaseCount('top_up_orders', 0);
    }

    public function test_admin_can_update_an_order_and_regular_user_cannot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $user->topUpOrders()->create([
            'order_code' => 'KSABC123456',
            'game' => 'Mobile Legends',
            'player_id' => '123456789',
            'zone_id' => '1234',
            'package_key' => 'ml-86',
            'package_name' => '86 Diamonds',
            'diamonds' => 86,
            'price' => 20000,
        ]);

        $this->actingAs($user)
            ->patch(route('dashboard.admin.orders.update', $order), ['status' => 'completed'])
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('dashboard.admin.orders.update', $order), ['status' => 'processing'])
            ->assertRedirect();

        $this->assertDatabaseHas('top_up_orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.admin'))
            ->assertOk()
            ->assertSee('Pesanan top up')
            ->assertSee('KSABC123456');
    }

    public function test_guest_cannot_view_or_submit_a_top_up_order(): void
    {
        $this->get(route('topup.mobile-legends'))->assertRedirect(route('login'));
        $this->post(route('topup.mobile-legends.store'))->assertRedirect(route('login'));
        $this->assertDatabaseCount('top_up_orders', 0);
    }
}
