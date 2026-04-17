<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ZonesSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@biopharma.mr'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['Admin']);

        $superviseur = User::updateOrCreate(
            ['email' => 'superviseur@biopharma.mr'],
            [
                'name' => 'Superviseur',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superviseur->syncRoles(['Superviseur']);

        $agent = User::updateOrCreate(
            ['email' => 'agent@biopharma.mr'],
            [
                'name' => 'Agent Terrain',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $agent->syncRoles(['Agent terrain']);

        $commercial = User::updateOrCreate(
            ['email' => 'commercial@biopharma.mr'],
            [
                'name' => 'Commercial',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $commercial->syncRoles(['Commercial']);
    }
}
