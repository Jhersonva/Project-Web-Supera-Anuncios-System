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
                'icon' => 'fa-briefcase'
            ],

            'comprar' => [
                'name' => 'Comprar / Traspaso',
                'icon' => 'fa-cart-shopping'
            ],

            'alquiler' => [
                'name' => 'Alquileres / Anticresis',
                'icon' => 'fa-key'
            ],

            'servicios' => [
                'name' => 'Servicios',
                'icon' => 'fa-screwdriver-wrench'
            ]
        ];

        foreach ($categories as $slug => $data) {
            DB::table('ad_categories')->insert([
                'name'  => $data['name'],
                'icon'  => $data['icon'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}