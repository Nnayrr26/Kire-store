<?php

namespace Tests\Feature;

use App\Models\JokiOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JokiOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_open_the_joki_order_form_and_view_the_player_list(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('joki.mobile-legends'))
            ->assertOk()
            ->assertSee('Joki Mobile Legends')
            ->assertSee('Nickname pemain')
            ->assertSee('Rank saat ini')
            ->assertSee('Jangan masukkan password atau kode OTP');

        $this->get(route('dashboard.user'))
            ->assertOk()
            ->assertSee('Daftar pemain joki')
            ->assertSee('Belum ada pemain terdaftar');
    }

    public function test_user_can_register_a_player_for_joki_with_a_pending_quote(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('joki.mobile-legends.store'), [
            'service_type' => 'rank',
            'player_nickname' => 'KirePlayer',
            'player_id' => '123456789',
            'zone_id' => '1234',
            'current_rank' => 'Epic',
            'target_rank' => 'Mythic',
            'notes' => 'Main malam hari',
            'quoted_price' => 1,
            'status' => 'completed',
        ]);

        $order = JokiOrder::firstOrFail();

        $response->assertRedirect(route('dashboard.user'));
        $response->assertSessionHas('success');
        $this->assertSame($user->id, $order->user_id);
        $this->assertSame('KirePlayer', $order->player_nickname);
        $this->assertSame('123456789', $order->player_id);
        $this->assertSame('Epic', $order->current_rank);
        $this->assertSame('Mythic', $order->target_rank);
        $this->assertSame('Main malam hari', $order->notes);
        $this->assertSame('pending', $order->status);
        $this->assertNull($order->quoted_price);

        $this->get(route('dashboard.user'))
            ->assertOk()
            ->assertSee('KirePlayer')
            ->assertSee('Epic')
            ->assertSee('Mythic')
            ->assertSee('Menunggu penawaran harga');
    }

    public function test_invalid_player_or_identical_rank_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->from(route('joki.mobile-legends'))
            ->post(route('joki.mobile-legends.store'), [
                'service_type' => 'rank',
                'player_nickname' => 'KirePlayer',
                'player_id' => 'invalid',
                'zone_id' => '1234',
                'current_rank' => 'Epic',
                'target_rank' => 'Mythic',
            ])
            ->assertRedirect(route('joki.mobile-legends'))
            ->assertSessionHasErrors('player_id');

        $this->post(route('joki.mobile-legends.store'), [
            'service_type' => 'rank',
            'player_nickname' => 'KirePlayer',
            'player_id' => '123456789',
            'zone_id' => '1234',
            'current_rank' => 'Epic',
            'target_rank' => 'Epic',
        ])->assertSessionHasErrors('target_rank');

        $this->assertDatabaseCount('joki_orders', 0);
    }

    public function test_only_admin_can_set_the_quote_and_update_a_joki_order(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $user->jokiOrders()->create([
            'order_code' => 'JKABC1234567',
            'game' => 'Mobile Legends',
            'service_type' => 'rank',
            'player_nickname' => 'KirePlayer',
            'player_id' => '123456789',
            'zone_id' => '1234',
            'current_rank' => 'Epic',
            'target_rank' => 'Mythic',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.admin'))
            ->assertOk()
            ->assertSee('Daftar pemain Mobile Legends')
            ->assertSee('KirePlayer')
            ->assertSee('Harga penawaran');

        $this->actingAs($admin)
            ->patch(route('dashboard.admin.joki-orders.update', $order), [
                'status' => 'quoted',
            ])
            ->assertSessionHasErrors('quoted_price');

        $this->assertDatabaseHas('joki_orders', [
            'id' => $order->id,
            'status' => 'pending',
            'quoted_price' => null,
        ]);

        $this->patch(route('dashboard.admin.joki-orders.update', $order), [
            'status' => 'quoted',
            'quoted_price' => 75000,
        ])->assertRedirect();

        $this->assertDatabaseHas('joki_orders', [
            'id' => $order->id,
            'status' => 'quoted',
            'quoted_price' => 75000,
        ]);
    }

    public function test_guest_and_regular_users_cannot_access_admin_joki_order_actions(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $order = $user->jokiOrders()->create([
            'order_code' => 'JKABC1234567',
            'game' => 'Mobile Legends',
            'service_type' => 'rank',
            'player_nickname' => 'KirePlayer',
            'player_id' => '123456789',
            'zone_id' => '1234',
            'current_rank' => 'Epic',
            'target_rank' => 'Mythic',
        ]);

        $this->get(route('joki.mobile-legends'))->assertRedirect(route('login'));

        $this->actingAs($user)
            ->patch(route('dashboard.admin.joki-orders.update', $order), [
                'status' => 'completed',
                'quoted_price' => 1000,
            ])
            ->assertForbidden();
    }
}
