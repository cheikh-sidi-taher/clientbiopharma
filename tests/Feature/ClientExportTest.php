<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_export_clients_can_download_excel(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $zone = Zone::create(['name' => 'Z', 'status' => 'active']);
        $pharmacy = Pharmacy::create([
            'zone_id' => $zone->id,
            'name' => 'P1',
            'type' => 'privée',
            'interest_status' => 'client',
        ]);
        $commercial = User::factory()->create();
        $commercial->assignRole('Commercial');

        Client::create([
            'pharmacy_id' => $pharmacy->id,
            'payment_terms' => 'Net 30',
            'credit_limit' => 100,
            'commercial_id' => $commercial->id,
            'status' => 'actif',
            'created_by' => $commercial->id,
        ]);

        $response = $this->actingAs($commercial)->get(route('clients.export', ['format' => 'excel']));

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function test_agent_cannot_export_clients(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $agent = User::factory()->create();
        $agent->assignRole('Agent terrain');

        $this->actingAs($agent)
            ->get(route('clients.export', ['format' => 'pdf']))
            ->assertForbidden();
    }
}
