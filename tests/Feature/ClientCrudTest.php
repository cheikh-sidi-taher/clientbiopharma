<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCrudTest extends TestCase
{
    use RefreshDatabase;

    private function seedRoles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function makeZoneAndPharmacy(): Pharmacy
    {
        $zone = Zone::create([
            'name' => 'Zone test',
            'status' => 'active',
        ]);

        return Pharmacy::create([
            'zone_id' => $zone->id,
            'name' => 'Pharmacie test',
            'type' => 'privée',
            'interest_status' => 'intéressé',
        ]);
    }

    public function test_commercial_can_create_client_from_list(): void
    {
        $this->seedRoles();

        $pharmacy = $this->makeZoneAndPharmacy();
        $commercial = User::factory()->create();
        $commercial->assignRole('Commercial');

        $response = $this->actingAs($commercial)->post(route('clients.store'), [
            'pharmacy_id' => $pharmacy->id,
            'payment_terms' => 'Net 30',
            'credit_limit' => '10000.50',
            'commercial_id' => $commercial->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'pharmacy_id' => $pharmacy->id,
            'commercial_id' => $commercial->id,
        ]);
        $this->assertSame('client', $pharmacy->fresh()->interest_status);
    }

    public function test_viewer_cannot_create_client(): void
    {
        $this->seedRoles();

        $pharmacy = $this->makeZoneAndPharmacy();
        $agent = User::factory()->create();
        $agent->assignRole('Agent terrain');
        $commercial = User::factory()->create();
        $commercial->assignRole('Commercial');

        $this->actingAs($agent)->post(route('clients.store'), [
            'pharmacy_id' => $pharmacy->id,
            'payment_terms' => 'Net 30',
            'credit_limit' => '100',
            'commercial_id' => $commercial->id,
        ])->assertForbidden();
    }

    public function test_commercial_can_update_client(): void
    {
        $this->seedRoles();

        $pharmacy = $this->makeZoneAndPharmacy();
        $commercial = User::factory()->create();
        $commercial->assignRole('Commercial');

        $client = Client::create([
            'pharmacy_id' => $pharmacy->id,
            'payment_terms' => 'Comptant',
            'credit_limit' => 500,
            'commercial_id' => $commercial->id,
            'status' => 'actif',
            'created_by' => $commercial->id,
        ]);
        $pharmacy->update(['interest_status' => 'client', 'partnership_type' => 'client_direct']);

        $response = $this->actingAs($commercial)->put(route('clients.update', $client), [
            'payment_terms' => 'Net 45',
            'credit_limit' => '1200',
            'commercial_id' => $commercial->id,
            'status' => 'inactif',
        ]);

        $response->assertRedirect(route('clients.show', $client));
        $this->assertSame('inactif', $client->fresh()->status);
        $this->assertSame('Net 45', $client->fresh()->payment_terms);
    }

    public function test_commercial_can_delete_client_and_pharmacy_is_freed(): void
    {
        $this->seedRoles();

        $pharmacy = $this->makeZoneAndPharmacy();
        $commercial = User::factory()->create();
        $commercial->assignRole('Commercial');

        $client = Client::create([
            'pharmacy_id' => $pharmacy->id,
            'payment_terms' => 'Comptant',
            'credit_limit' => 0,
            'commercial_id' => $commercial->id,
            'status' => 'actif',
            'created_by' => $commercial->id,
        ]);
        $pharmacy->update(['interest_status' => 'client', 'partnership_type' => 'client_direct']);

        $this->actingAs($commercial)
            ->delete(route('clients.destroy', $client))
            ->assertRedirect(route('clients.index'));

        $this->assertNull(Client::find($client->id));
        $this->assertSame('intéressé', $pharmacy->fresh()->interest_status);
    }

    public function test_clients_index_supports_search_and_filters(): void
    {
        $this->seedRoles();

        $zone = Zone::create([
            'name' => 'Zone Nord',
            'status' => 'active',
        ]);
        $pharmacy = Pharmacy::create([
            'zone_id' => $zone->id,
            'name' => 'Pharma Unique',
            'type' => 'privée',
            'interest_status' => 'intéressé',
        ]);
        $commercial = User::factory()->create(['name' => 'Jean Commercial']);
        $commercial->assignRole('Commercial');

        $client = Client::create([
            'pharmacy_id' => $pharmacy->id,
            'payment_terms' => 'Net 60 jours',
            'credit_limit' => 100,
            'commercial_id' => $commercial->id,
            'status' => 'actif',
            'created_by' => $commercial->id,
        ]);

        $viewer = User::factory()->create();
        $viewer->assignRole('Commercial');

        $this->actingAs($viewer)->get(route('clients.index', [
            'search' => 'Net 60',
        ]))->assertOk()->assertSee((string) $client->id);

        $this->actingAs($viewer)->get(route('clients.index', [
            'zone_id' => $zone->id,
        ]))->assertOk()->assertSee('Pharma Unique');

        $this->actingAs($viewer)->get(route('clients.index', [
            'commercial_id' => $commercial->id,
        ]))->assertOk()->assertSee('Pharma Unique');

        $this->actingAs($viewer)->get(route('clients.index', [
            'status' => 'inactif',
        ]))->assertOk()->assertDontSee('Pharma Unique');
    }
}
