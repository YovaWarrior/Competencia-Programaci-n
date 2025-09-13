<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MembresiasSeeder::class,
            EstacionesSeeder::class,
            BicicletasSeeder::class,
            RecompensasSeeder::class,
            AdminUserSeeder::class,
            RutasSeeder::class,
        ]);
    }
}