<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            UsersSeeder::class,
            AdCategorySeeder::class,
            AdSubcategorySeeder::class,
            AdFieldSeeder::class,
            PaymentMethodSeeder::class,
            SystemSettingsSeeder::class,
        ]);
    }
}
