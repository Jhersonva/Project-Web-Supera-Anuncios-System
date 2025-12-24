<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // ALERTS
        DB::table('alerts')->insert([
            [
                'logo' => 'warning.png',
                'title' => 'Contenido sensible',
                'description' => 'Este contenido puede no ser apto para todo público.',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // ADULT CONTENT VIEW TERMS
        DB::table('adult_content_view_terms')->insert([
            [
                'icon' => 'eye.png',
                'title' => 'Mayor de edad',
                'description' => 'Debes confirmar que eres mayor de edad para ver este contenido.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // ADULT CONTENT PUBLISH TERMS
        DB::table('adult_content_publish_terms')->insert([
            [
                'icon' => 'upload.png',
                'title' => 'Contenido permitido',
                'description' => 'No se permite publicar contenido ilegal u ofensivo.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}