<?php

namespace Tests\Feature;

use App\Models\JokiOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkerFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_worker_account_and_worker_sees_its_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('dashboard.admin'))
            ->post(route('dashboard.admin.workers.store'), [
                'name' => 'Worker Kire',
                'email' => 'worker@example.com',
                'password' => 'workerpass123',
                'password_confirmation' => 'workerpass123',
            ])
            ->assertRedirect(route('dashboard.admin'))
            ->assertSessionHas('success');

        $worker = User::where('email', 'worker@example.com')->firstOrFail();
        $this->assertSame('worker', $worker->role);
        $this->assertTrue(password_verify('workerpass123', $worker->password));

        $this->post(route('logout'));

        $this->post(route('login.store'), [
            'email' => 'worker@example.com',
            'password' => 'workerpass123',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertRedirect(route('dashboard.worker'));
        $this->get(route('dashboard.worker'))
            ->assertOk()
            ->assertSee('PANEL WORKER JOKI')
            ->assertSee('Belum ada tugas untukmu');
    }

    public function test_only_admin_can_create_worker_accounts_and_public_registration_cannot_escalate_role(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('dashboard.admin.workers.store'), [
                'name' => 'Worker Kire',
                'email' => 'worker@example.com',
                'password' => 'workerpass123',
                'password_confirmation' => 'workerpass123',
            ])
            ->assertForbidden();

        $this->post(route('logout'));

        $this->post(route('register.store'), [
            'name' => 'Public Signup',
            'email' => 'public@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'worker',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'public@example.com',
            'role' => 'user',
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'worker@example.com']);
    }

    public function test_admin_can_assign_only_quoted_joki_orders_to_worker_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $worker = User::factory()->create(['role' => 'worker']);
        $regularUser = User::factory()->create(['role' => 'user']);
        $pendingOrder = $this->createJokiOrder($regularUser, 'pending');
        $quotedOrder = $this->createJokiOrder($regularUser, 'quoted');

        $this->actingAs($admin)
            ->patch(route('dashboard.admin.joki-orders.assign', $pendingOrder), [
                'worker_id' => $worker->id,
            ])
            ->assertSessionHasErrors('worker_id', null, 'assignment');

        $this->assertNull($pendingOrder->fresh()->worker_id);

        $this->patch(route('dashboard.admin.joki-orders.assign', $quotedOrder), [
            'worker_id' => $regularUser->id,
        ])->assertSessionHasErrors('worker_id', null, 'assignment');

        $this->patch(route('dashboard.admin.joki-orders.assign', $quotedOrder), [
            'worker_id' => $worker->id,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('joki_orders', [
            'id' => $quotedOrder->id,
            'worker_id' => $worker->id,
        ]);
    }

    public function test_worker_can_only_view_assigned_orders_and_advance_task_status(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $otherWorker = User::factory()->create(['role' => 'worker']);
        $customer = User::factory()->create(['role' => 'user']);
        $assignedOrder = $this->createJokiOrder($customer, 'quoted', $worker);
        $otherOrder = $this->createJokiOrder($customer, 'quoted', $otherWorker);

        $this->actingAs($worker)
            ->get(route('dashboard.worker'))
            ->assertOk()
            ->assertSee($assignedOrder->order_code)
            ->assertDontSee($otherOrder->order_code);

        $this->patch(route('dashboard.worker.joki-orders.update', $otherOrder), [
            'status' => 'processing',
        ])->assertNotFound();

        $this->patch(route('dashboard.worker.joki-orders.update', $assignedOrder), [
            'status' => 'completed',
        ])->assertSessionHasErrors('status');

        $this->assertSame('quoted', $assignedOrder->fresh()->status);

        $this->patch(route('dashboard.worker.joki-orders.update', $assignedOrder), [
            'status' => 'processing',
        ])->assertSessionHas('success');

        $this->patch(route('dashboard.worker.joki-orders.update', $assignedOrder), [
            'status' => 'completed',
        ])->assertSessionHas('success');

        $this->assertSame('completed', $assignedOrder->fresh()->status);
        $this->get(route('dashboard.user'))->assertForbidden();
        $this->get(route('dashboard.admin'))->assertForbidden();
        $this->get(route('joki.mobile-legends'))->assertForbidden();
    }

    private function createJokiOrder(User $user, string $status, ?User $worker = null): JokiOrder
    {
        return $user->jokiOrders()->create([
            'order_code' => 'JK'.strtoupper(str()->random(10)),
            'worker_id' => $worker?->id,
            'game' => 'Mobile Legends',
            'service_type' => 'rank',
            'player_nickname' => 'KirePlayer',
            'player_id' => '123456789',
            'zone_id' => '1234',
            'current_rank' => 'Epic',
            'target_rank' => 'Mythic',
            'quoted_price' => $status === 'pending' ? null : 75000,
            'status' => $status,
        ]);
    }
}
