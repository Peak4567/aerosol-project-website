<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure default settings
        $defaults = [
            'site_name' => 'Aerosol',
            'site_description' => 'Aerosol นวัตกรรม AeroFogPonic Farming',
            'maintenance_mode' => '0',
            'site_logo' => '',
        ];

        foreach ($defaults as $key => $val) {
            Setting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $val]
            );
        }

        // Ensure default admin user
        User::updateOrCreate(
            ['username' => 'aerosol'],
            [
                'email' => 'aerosol@example.com',
                'level' => 'admin',
                'password' => Hash::make('Aerosol@2026'),
            ]
        );
    }
}
