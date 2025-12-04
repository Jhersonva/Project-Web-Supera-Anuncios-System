<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name_method'       => 'Yape',
                'type'              => 'billetera',
                'logo'              => 'images/payment_methods/yape.png',
                'holder_name'       => 'Jherson Villa',
                'cell_phone_number' => '999999999',
                'account_number'    => null,
                'cci'               => null,
                'qr'                => 'yape_qr.png',
                'active'            => true,
            ],
            [
                'name_method'       => 'Plin',
                'type'              => 'billetera',
                'logo'              => 'images/payment_methods/plin.webp',
                'holder_name'       => 'Jherson Villa',
                'cell_phone_number' => '988888888',
                'account_number'    => null,
                'cci'               => null,
                'qr'                => 'plin_qr.png',
                'active'            => true,
            ],
            [
                'name_method'       => 'BCP',
                'type'              => 'banco',
                'logo'              => 'images/payment_methods/bcp.jpg',
                'holder_name'       => 'Jherson Villa',
                'cell_phone_number' => null,
                'account_number'    => '1234567890123',
                'cci'               => '00212345678901234567',
                'qr'                => 'bcp_qr.png',
                'active'            => true,
            ],
            [
                'name_method'       => 'Interbank',
                'type'              => 'banco',
                'logo'              => 'images/payment_methods/interbank.png',
                'holder_name'       => 'Jherson Villa',
                'cell_phone_number' => null,
                'account_number'    => '4567891234567',
                'cci'               => '00345678912345678901',
                'qr'                => 'interbank_qr.png',
                'active'            => true,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
