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
            ['id' => 1], // configuración única
            [
                'business_name' => 'Nombre de la Empresa',
                'ruc' => '10000000000',
                'address' => 'Dirección de la empresa',
                'legal_text' => 'Texto legal del libro de reclamaciones conforme a la normativa vigente.',
                'notification_email' => 'contacto@empresa.com',
            ]
        );
    }
}
