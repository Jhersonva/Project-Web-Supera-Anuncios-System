<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrivacyPolicySetting;

class PrivacyPolicySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PrivacyPolicySetting::updateOrCreate(
            ['id' => 1], // configuración única
            [
                'privacy_text' => 'Aquí va el texto inicial de la política de privacidad. 
                Puedes editar este contenido desde el panel de administración.',
                'contains_explicit_content' => true,
                'requires_adult' => true,
                'is_active' => true,
            ]
        );
    }
}
