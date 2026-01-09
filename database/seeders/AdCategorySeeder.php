<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'EMPLEOS', 'icon' => 'fa-briefcase'],
            ['name' => 'INMUEBLES', 'icon' => 'fa-building'],
            ['name' => 'VEHICULOS, MOTOS, MAQUINARIAS, EQUIPOS Y OTROS', 'icon' => 'fa-car'],
            ['name' => 'SERVICIOS', 'icon' => 'fa-screwdriver-wrench'],
        ];

        foreach ($categories as $category) {
            DB::table('ad_categories')->insert([
                'name' => $category['name'],
                'icon' => $category['icon'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}