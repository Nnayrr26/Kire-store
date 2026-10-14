<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_and_authenticates_a_user_without_role_escalation(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Pemain Baru',
            'email' => 'pemain@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'pemain@example.com')->firstOrFail();

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('user', $user->role);
        $this->assertTrue(password_verify('password123', $user->password));
        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('dashboard.user'));
    }

    public function test_registered_users_cannot_open_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('dashboard.admin'))
            ->assertForbidden();
    }

    public function test_admin_dashboard_shows_user_overview(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['name' => 'Pemain Baru', 'role' => 'user']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('dashboard.admin'));

        $this->actingAs($admin)
            ->get(route('dashboard.admin'))
            ->assertOk()
            ->assertSee('Pengguna terbaru')
            ->assertSee('Pemain Baru');
    }

    public function test_users_are_redirected_to_their_dashboard_after_login_and_can_log_out(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'password123',
            'role' => 'user',
        ]);

        $this->post(route('login.store'), [
            'email' => 'login@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertRedirect(route('dashboard.user'));

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard.user'))->assertRedirect(route('login'));
        $this->get(route('dashboard.admin'))->assertRedirect(route('login'));
    }

    public function test_authentication_pages_and_user_dashboard_render(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Selamat datang');
        $this->get(route('register'))->assertOk()->assertSee('Buat akun');

        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('dashboard.user'))
            ->assertOk()
            ->assertSee('DASHBOARD PENGGUNA');
    }

    public function test_database_seeder_creates_an_admin_account(): void
    {
        $this->seed();

        $admin = User::where('email', config('kirestore.admin.email'))->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(password_verify(config('kirestore.admin.password'), $admin->password));
    }
}
