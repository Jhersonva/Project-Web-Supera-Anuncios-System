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
                'is_property' => 0,
            ],

            'comprar' => [
                'name' => 'Comprar / Traspaso',
                'icon' => 'fa-cart-shopping',
                'is_property' => 1,
            ],

            'alquiler' => [
                'name' => 'Alquileres / Anticresis',
                'icon' => 'fa-key',
                'is_property' => 1,
            ],

            'servicios' => [
                'name' => 'Servicios',
                'icon' => 'fa-screwdriver-wrench',
                'is_property' => 0,
            ]
        ];

        foreach ($categories as $slug => $data) {
            DB::table('ad_categories')->insert([
                'name'        => $data['name'],
                'icon'        => $data['icon'],
                'is_property' => $data['is_property'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}