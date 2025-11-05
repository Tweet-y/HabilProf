<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
        // Asegúrate que la siguiente línea exista:
            \Database\Seeders\CargaSeeder::class, 
        // Si tienes otros seeders (como los seeders de usuarios por defecto), agrégalos también.
        ]);
    }
}
