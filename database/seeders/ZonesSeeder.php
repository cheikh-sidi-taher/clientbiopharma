<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZonesSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Tevragh Zeina', 'description' => 'Zone nord-ouest de Nouakchott', 'target_pharmacies' => 30],
            ['name' => 'Ksar',          'description' => 'Centre historique de Nouakchott',  'target_pharmacies' => 25],
            ['name' => 'Sebkha',        'description' => 'Zone sud-ouest',                   'target_pharmacies' => 20],
            ['name' => 'El Mina',       'description' => 'Zone ouest proche mer',             'target_pharmacies' => 20],
            ['name' => 'Arafat',        'description' => 'Zone est de Nouakchott',            'target_pharmacies' => 35],
            ['name' => 'Dar Naim',      'description' => 'Zone nord-est',                     'target_pharmacies' => 25],
            ['name' => 'Toujounine',    'description' => 'Zone périphérique nord',            'target_pharmacies' => 20],
        ];

        foreach ($zones as $zone) {
            Zone::firstOrCreate(['name' => $zone['name']], $zone + ['status' => 'active']);
        }
    }
}
