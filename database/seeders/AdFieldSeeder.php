<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdFieldSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | DEFINICIÓN DE CAMPOS POR SUBCATEGORÍA
        |--------------------------------------------------------------------------
        */

        $fieldsByCategory = [

            // ============== EMPLEOS =================
            'EMPLEOS' => [
                'Mineria' => ['Requisitos', 'Beneficios'],
                'Construccion De Obras e Infraestructuras' => ['Requisitos', 'Beneficios'],
                'Construcion De Casas' => ['Requisitos', 'Beneficios'],
                'Sector Comidas' => ['Requisitos', 'Beneficios'],
                'Discotecas y Restobar' => ['Requisitos', 'Beneficios'],
                'Empleadas, Cocinera, Niñeras y Limpieza' => ['Requisitos', 'Beneficios'],
                // Neght Club, Disco Bar → SIN CAMPOS
            ],

            // ============== INMUEBLES =================
            'INMUEBLES' => [
                'Ventas' => ['Cuartos', 'Baños', 'Area'],
                'Alquiler' => ['Cuartos', 'Baños', 'Area'],
                'Traspaso' => ['Area'],
                'Anticresis' => ['Cuartos', 'Baños', 'Area'],
                'Otros Inmuebles' => ['Area'],
            ],

            // ============== VEHÍCULOS =================
            'VEHICULOS, MOTOS, MAQUINARIAS, EQUIPOS Y OTROS' => [
                'Venta' => ['Marca', 'Año Modelo', 'Kilometraje', 'Transmision'],
                'Alquiler' => ['Marca', 'Año Modelo', 'Kilometraje', 'Transmision'],
                // Otros → SIN CAMPOS
            ],

            // ============== SERVICIOS =================
            'SERVICIOS' => [
                'Privados' => ['Edad', 'Nacionalidad'],
                // Oficios y Profesionales → SIN CAMPOS
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | INSERCIÓN
        |--------------------------------------------------------------------------
        */

        foreach ($fieldsByCategory as $categoryName => $subs) {

            $categoryId = DB::table('ad_categories')
                ->where('name', $categoryName)
                ->value('id');

            if (!$categoryId) continue;

            foreach ($subs as $subName => $fields) {

                $sub = DB::table('ad_subcategories')
                    ->where('name', $subName)
                    ->where('ad_categories_id', $categoryId)
                    ->first();

                if (!$sub) continue;

                foreach ($fields as $field) {
                    DB::table('fields_subcategory_ads')->insert([
                        'ad_subcategories_id' => $sub->id,
                        'name' => $field,
                        'type' => 'string',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}