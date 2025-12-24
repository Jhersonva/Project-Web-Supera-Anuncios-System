<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComplaintBookSetting;

class ComplaintBookSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ComplaintBookSetting::updateOrCreate(
            ['id' => 1], 
            [
                'business_name' => 'Nombre de la Empresa',
                'ruc' => '10000000000',
                'address' => 'Dirección de la empresa',
                'legal_text' => 'El Libro de Reclamaciones del sistema [Nombre del Sistema] está a disposición de todos los usuarios con el fin de recibir quejas, reclamos o sugerencias relacionadas con el uso de la plataforma. Mediante este mecanismo, el usuario puede manifestar cualquier disconformidad respecto al funcionamiento del sistema, la publicación de anuncios o la prestación del servicio. Las reclamaciones serán registradas y evaluadas de manera objetiva, buscando una solución conforme a la normativa vigente. La presentación de una reclamación no impide al usuario ejercer otros derechos que le correspondan por ley. El sistema se compromete a responder dentro de un plazo razonable y a adoptar las medidas necesarias para mejorar el servicio cuando corresponda.',
                'notification_email' => 'contacto@empresa.com',
            ]
        );
    }
}
