<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'company_name' => 'Biopharma Mauritanie',
            'company_address' => 'Nouakchott, Mauritanie',
            'company_phone' => '+222 XX XX XX XX',
            'company_email' => 'contact@biopharma.mr',
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
