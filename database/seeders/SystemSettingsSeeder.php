<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('system_settings')->insert([
            'company_name' => 'Mi Empresa',
            'company_description' => 'Descripción inicial del sistema',
            'logo' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
