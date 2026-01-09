<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdSubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $structure = [

            // ================= EMPLEOS =================
            'EMPLEOS' => [
                ['Mineria', 6.00],
                ['Restaurante', 5.00],
                ['Gasfitero', 4.00],
                ['Conductor', 3.00],
                ['Construccion De Obras e Infraestructuras', 5.00],
                ['Construcion De Casas', 3.50],
                ['Sector Comidas', 3.50],
                ['Discotecas y Restobar', 4.00],
                ['Neght Club, Disco Bar', 5.00],
                ['Empleadas, Cocinera, Niñeras y Limpieza', 2.50],
            ],

            // ================= INMUEBLES =================
            'INMUEBLES' => [
                ['Ventas', 3.00],
                ['Alquiler', 3.00],
                ['Traspaso', 3.00],
                ['Anticresis', 3.00],
                ['Otros Inmuebles', 3.00],
            ],

            // ================= VEHÍCULOS =================
            'VEHICULOS, MOTOS, MAQUINARIAS, EQUIPOS Y OTROS' => [
                ['Venta', 3.00],
                ['Alquiler', 3.00],
                ['Otros', 3.00],
            ],

            // ================= SERVICIOS =================
            'SERVICIOS' => [
                ['Oficios', 2.50],
                ['Profesionales', 2.50],
                ['Privados', 5.00],
            ],
        ];

        foreach ($structure as $categoryName => $subs) {

            $categoryId = DB::table('ad_categories')
                ->where('name', $categoryName)
                ->value('id');

            if (!$categoryId) continue;

            foreach ($subs as [$name, $price]) {
                DB::table('ad_subcategories')->insert([
                    'ad_categories_id' => $categoryId,
                    'name' => $name,
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}