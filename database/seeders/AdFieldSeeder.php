<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdFieldSeeder extends Seeder
{
    public function run(): void
    {
        // SUBCATEGORÍA: EMPLEO (CATEGORÍA: EMPLEOS)
        $jobFields = [
            ['name' => "Rubro", 'type' => 'multiple'], 
            ['name' => "Razon Social", 'type' => 'string'],
            ['name' => "Cargo", 'type' => 'string'],
            ['name' => "Funciones", 'type' => 'string'],
            ['name' => "Requisitos", 'type' => 'string'],
            ['name' => "Beneficios", 'type' => 'string'],
        ];


        // OBTENER SUBCATEGORIA EMPLEO
        $subEmpleo = DB::table('ad_subcategories')
            ->where('name', 'Empleo')
            ->where('ad_categories_id', function ($q) {
                $q->select('id')
                  ->from('ad_categories')
                  ->where('name', 'Empleos');
            })
            ->first();

        if ($subEmpleo) {
            foreach ($jobFields as $field) {
                DB::table('fields_subcategory_ads')->insert([
                    'ad_subcategories_id' => $subEmpleo->id,
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // CAMPOS GENÉRICOS — AGRUPADOS POR CATEGORÍA
        $categorySubFields = [

            // Comprar / Traspaso
            "Comprar / Traspaso" => [
                "Inmobiliaria" => ["Metros Cuadrados", "Baños", "Cuartos", "Antigüedad"],
                "Vehiculos" => ["Marca", "Modelo", "Año", "Kilometraje"],
                "Casa" => ["Metros Cuadrados", "Habitaciones", "Pisos"],
                "Departamento" => ["Metros Cuadrados", "Piso", "Habitaciones"],
                "Oficina" => ["Área", "Baños"],
                "Terreno / Lotes / Chacra" => ["Área Total", "Ubicación"],
                "Local Comercial" => ["Área", "Baños"],
                "Puesto" => ["Área", "Ubicación"],
                "Otros Inmuebles" => ["Descripción Detallada"],
            ],

            // Alquileres / Anticresis
            "Alquileres / Anticresis" => [
                "Inmobiliaria" => ["Metros Cuadrados", "Baños", "Cuartos", "Antigüedad"],
                "Vehiculos" => ["Marca", "Modelo", "Año", "Kilometraje"],
                "Casa" => ["Metros Cuadrados", "Habitaciones", "Pisos"],
                "Departamento / Mini" => ["Metros Cuadrados", "Habitaciones", "Piso"],
                "Cuarto / Oficina" => ["Metros Cuadrados", "Baño", "Piso"],
                "Garaje / Estacionamiento" => ["Área", "Tipo"],
                "Local Comercial / Puesto" => ["Área", "Ubicación"],
                "Otros Inmuebles" => ["Descripción Detallada"],
            ],

            // Servicios
            "Servicios" => [
                "Oficios" => ["Tipo de Servicio", "Experiencia", "Disponibilidad"],
                "Profesionales" => ["Profesión", "Especialidad", "Experiencia"],
                "Privados" => ["Tipo de Servicio", "Horario"],
            ],
        ];

        // ASIGNAR CAMPOS A CADA SUBCATEGORIA
        foreach ($categorySubFields as $categoryName => $subcategories) {

            $category = DB::table('ad_categories')
                ->where('name', $categoryName)
                ->first();

            if (!$category) continue;

            foreach ($subcategories as $subName => $fields) {

                  $subcat = DB::table('ad_subcategories')
                    ->where('name', $subName)
                    ->where('ad_categories_id', $category->id)
                    ->first();

                if ($subcat) {
                    foreach ($fields as $field) {
                        DB::table('fields_subcategory_ads')->insert([
                            'ad_subcategories_id' => $subcat->id,
                            'name'  => $field,
                            'type'  => 'string',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
