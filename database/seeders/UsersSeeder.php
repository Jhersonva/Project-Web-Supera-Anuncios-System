<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([

            // ADMIN
            [
                'role_id' => 1,
                'full_name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('12345678'),
                'dni' => '00000001',
                'phone' => '900000001',
                'locality' => 'Lima',
                'whatsapp' => '900000001',
                'call_phone' => '900000001',
                'contact_email' => 'admin@admin.com',
                'address' => 'Av. Principal 123',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ADVERTISING USER
            [
                'role_id' => 2, 
                'full_name' => 'Pepe Pérez',
                'email' => 'usuario@usuario.com',
                'password' => Hash::make('12345678'),
                'dni' => '00000002',
                'phone' => '900000002',
                'locality' => 'Arequipa',
                'whatsapp' => '900000002',
                'call_phone' => '900000002',
                'contact_email' => 'user@user.com',
                'address' => 'Calle Secundaria 456',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
