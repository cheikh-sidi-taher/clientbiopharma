<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_is_displayed_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('settings.index'))->assertOk();
    }

    public function test_non_admin_does_not_have_company_settings_form(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('Agent terrain');

        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertOk();
        $response->assertDontSee('Raison sociale');
    }

    public function test_admin_can_update_application_settings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(SettingsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->put(route('settings.application.update'), [
            'company_name' => 'Test Pharma SARL',
            'company_address' => 'Rue 123',
            'company_phone' => '+222 00 00 00',
            'company_email' => 'info@test.mr',
        ]);

        $response->assertRedirect(route('settings.index'));
        $this->assertSame('Test Pharma SARL', Setting::get('company_name'));
        $this->assertSame('Rue 123', Setting::get('company_address'));
    }

    public function test_non_admin_cannot_update_application_settings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(SettingsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('Commercial');

        $this->actingAs($user)
            ->put(route('settings.application.update'), [
                'company_name' => 'Hack',
                'company_address' => '',
                'company_phone' => '',
                'company_email' => '',
            ])
            ->assertForbidden();

        $this->assertNotSame('Hack', Setting::get('company_name'));
    }
}
