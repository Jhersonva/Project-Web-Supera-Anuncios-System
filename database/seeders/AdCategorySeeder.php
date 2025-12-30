<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'empleos' => [
                'name' => 'Empleos',
                'icon' => 'fa-briefcase',
            ],

            'inmuebles' => [
                'name' => 'Inmuebles',
                'icon' => 'fa-building',
            ],

            'vehiculos_maquinarias' => [
                'name' => 'Vehículos / Maquinarias y Otros',
                'icon' => 'fa-car',
            ],

            'servicios' => [
                'name' => 'Servicios',
                'icon' => 'fa-screwdriver-wrench',
            ],
        ];

        foreach ($categories as $slug => $data) {
            DB::table('ad_categories')->insert([
                'name'        => $data['name'],
                'icon'        => $data['icon'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}