<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_cannot_access_users_list(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('Agent terrain');

        $this->actingAs($user)->get(route('users.index'))->assertForbidden();
    }

    public function test_superviseur_can_view_users_list(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('Superviseur');

        $this->actingAs($user)->get(route('users.index'))->assertOk();
    }

    public function test_superviseur_cannot_create_user(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('Superviseur');

        $this->actingAs($user)->get(route('users.create'))->assertForbidden();
    }

    public function test_admin_can_create_user_with_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Nouveau Test',
            'email' => 'nouveau@example.com',
            'password' => 'MotDePasse-123!',
            'password_confirmation' => 'MotDePasse-123!',
            'role' => 'Commercial',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'nouveau@example.com']);
        $this->assertTrue(User::where('email', 'nouveau@example.com')->first()?->hasRole('Commercial'));
    }

    public function test_registration_returns_404_when_disabled(): void
    {
        config(['biopharma.allow_registration' => false]);

        $this->get('/register')->assertNotFound();
    }
}
