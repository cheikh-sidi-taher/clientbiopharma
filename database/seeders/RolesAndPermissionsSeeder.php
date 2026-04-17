<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    private const GUARD = 'web';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = [
            'view zones',
            'manage zones',
            'view pharmacies',
            'manage pharmacies',
            'export pharmacies',
            'manage planning',
            'view visits',
            'view clients',
            'manage clients',
            'view reports',
            'export reports',
        ];

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => self::GUARD]
            );
        }

        $all = Permission::all();

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => self::GUARD]);
        $admin->syncPermissions($all);

        $superviseur = Role::firstOrCreate(['name' => 'Superviseur', 'guard_name' => self::GUARD]);
        $superviseur->syncPermissions($all);

        $agent = Role::firstOrCreate(['name' => 'Agent terrain', 'guard_name' => self::GUARD]);
        $agent->syncPermissions([
            'view zones',
            'view pharmacies',
            'manage planning',
            'view visits',
            'view clients',
        ]);

        $commercial = Role::firstOrCreate(['name' => 'Commercial', 'guard_name' => self::GUARD]);
        $commercial->syncPermissions([
            'view zones',
            'view pharmacies',
            'view clients',
            'manage clients',
            'view reports',
            'export reports',
            'export pharmacies',
        ]);
    }
}
