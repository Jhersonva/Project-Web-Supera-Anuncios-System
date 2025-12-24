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
                'privacy_text' => 'El sistema [Nombre del Sistema] es una plataforma de anuncios donde los usuarios pueden publicar y consultar anuncios de trabajo, ventas y servicios para adultos. El uso del sistema implica la aceptación del tratamiento de los datos personales proporcionados por los usuarios. Se recopilan únicamente los datos necesarios para el funcionamiento de la plataforma, como información de contacto, datos incluidos en los anuncios y datos técnicos básicos. Estos datos se utilizan para permitir la publicación de anuncios, facilitar la comunicación entre usuarios, garantizar la seguridad del sistema y cumplir con la normativa legal vigente. Los anunciantes son responsables del contenido publicado y declaran ser mayores de edad. El sistema no vende ni comercializa datos personales y solo podrá compartirlos cuando exista una obligación legal o sea necesario para el funcionamiento técnico del servicio. Los datos se conservarán durante el tiempo necesario para cumplir con su finalidad. El sistema aplica medidas razonables de seguridad para proteger la información. Los usuarios pueden solicitar el acceso, corrección o eliminación de sus datos. El sistema se reserva el derecho de modificar esta política en cualquier momento.',
                'contains_explicit_content' => true,
                'requires_adult' => true,
                'is_active' => true,
            ]
        );
    }
}
