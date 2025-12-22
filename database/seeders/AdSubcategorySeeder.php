<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdSubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $structure = [

            [
                'category_name' => 'Empleos',
                'items' => [
                    ["Mina", 1.00],
                ]
            ],

            [
                'category_name' => 'Comprar / Traspaso',
                'items' => [
                    ["Inmobiliaria", 3.00],
                    ["Vehiculos", 3.00],
                    ["Casa", 3.00],
                    ["Departamento", 3.00],
                    ["Oficina", 3.00],
                    ["Terreno / Lotes / Chacra", 3.00],
                    ["Local Comercial", 3.00],
                    ["Puesto", 3.00],
                    ["Otros Inmuebles", 3.00],
                ]
            ],

            [
                'category_name' => 'Alquileres / Anticresis',
                'items' => [
                    ["Inmobiliaria", 3.00],
                    ["Vehiculos", 3.00],
                    ["Casa", 3.00],
                    ["Departamento / Mini", 3.00],
                    ["Cuarto / Oficina", 3.00],
                    ["Garaje / Estacionamiento", 3.00],
                    ["Local Comercial / Puesto", 3.00],
                    ["Otros Inmuebles", 3.00],
                ]
            ],

            [
                'category_name' => 'Servicios',
                'items' => [
                    ["Oficios", 2.00],
                    ["Profesionales", 2.50],
                    ["Privados", 2.00],
                ]
            ],

        ];

        foreach ($structure as $entry) {

            $category = DB::table('ad_categories')
                ->where('name', $entry['category_name'])
                ->first();

            if (!$category) {
                continue;
            }

            foreach ($entry['items'] as [$name, $price]) {
                DB::table('ad_subcategories')->insert([
                    'ad_categories_id' => $category->id,
                    'name'             => $name,
                    'price'            => $price,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
    }
}