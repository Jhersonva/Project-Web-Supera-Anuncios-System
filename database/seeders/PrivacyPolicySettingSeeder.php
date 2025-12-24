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
            ['id' => 1], 
            [
                'privacy_text' => 'El período de prueba del sistema de anuncios se ofrece únicamente con fines de evaluación. Durante este tiempo, algunas funciones pueden estar limitadas o sujetas a cambios sin previo aviso.
                    El usuario se compromete a utilizar el sistema de forma legal, ética y conforme a la normativa vigente. Queda prohibido publicar contenido engañoso, ilegal u ofensivo.
                    La plataforma no garantiza resultados específicos (ventas, clics, impresiones u otros) durante el período de prueba. Al finalizar la prueba, el acceso podrá ser suspendido automáticamente salvo que se contrate un plan activo.
                    El uso del sistema durante la prueba implica la aceptación total de estos términos.',
            ]
        );
    }
}
