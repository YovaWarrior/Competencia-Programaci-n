<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bicicleta;
use Carbon\Carbon;

class BicicletasSeeder extends Seeder
{
    public function run()
    {
        $marcas = ['Trek', 'Giant', 'Specialized', 'Cannondale', 'Merida'];
        $modelosTradicionales = ['CityRide', 'UrbanCruiser', 'EcoLife', 'ComfortPlus'];
        $modelosElectricos = ['E-Power', 'ElectroGlide', 'PowerBoost', 'E-Urban'];
        
        $bicicletas = [];
        $contador = 1;

        // Crear 60 bicicletas tradicionales
        for ($i = 1; $i <= 60; $i++) {
            $marca = $marcas[array_rand($marcas)];
            $modelo = $modelosTradicionales[array_rand($modelosTradicionales)];
            $estacion = rand(1, 8); // Distribuir en las 8 estaciones
            
            $bicicletas[] = [
                'codigo' => 'ECO-TR-' . str_pad($contador, 3, '0', STR_PAD_LEFT),
                'tipo' => 'tradicional',
                'marca' => $marca,
                'modelo' => $modelo,
                'ano_fabricacion' => rand(2020, 2024),
                'estado' => $this->getRandomEstado(),
                'estacion_actual_id' => $estacion,
                'nivel_bateria' => null,
                'kilometraje_total' => rand(100, 5000),
                'ultimo_mantenimiento' => Carbon::now()->subDays(rand(1, 90)),
                'proximo_mantenimiento' => Carbon::now()->addDays(rand(30, 180)),
                'reportes_danos' => null,
                'bloqueada' => false,
                'motivo_bloqueo' => null,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $contador++;
        }

        // Crear 40 bicicletas eléctricas
        for ($i = 1; $i <= 40; $i++) {
            $marca = $marcas[array_rand($marcas)];
            $modelo = $modelosElectricos[array_rand($modelosElectricos)];
            $estacion = rand(1, 8);
            
            $bicicletas[] = [
                'codigo' => 'ECO-EL-' . str_pad($contador - 60, 3, '0', STR_PAD_LEFT),
                'tipo' => 'electrica',
                'marca' => $marca,
                'modelo' => $modelo,
                'ano_fabricacion' => rand(2021, 2024),
                'estado' => $this->getRandomEstado(),
                'estacion_actual_id' => $estacion,
                'nivel_bateria' => rand(20, 100),
                'kilometraje_total' => rand(50, 3000),
                'ultimo_mantenimiento' => Carbon::now()->subDays(rand(1, 60)),
                'proximo_mantenimiento' => Carbon::now()->addDays(rand(30, 120)),
                'reportes_danos' => null,
                'bloqueada' => rand(0, 10) === 0, // 10% bloqueadas
                'motivo_bloqueo' => rand(0, 10) === 0 ? 'Batería dañada' : null,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $contador++;
        }

        Bicicleta::insert($bicicletas);
    }

    private function getRandomEstado()
    {
        $estados = ['disponible', 'en_uso', 'mantenimiento', 'danada'];
        $probabilidades = [70, 15, 10, 5]; // 70% disponible, 15% en uso, etc.
        
        $random = rand(1, 100);
        $acumulado = 0;
        
        foreach ($probabilidades as $index => $prob) {
            $acumulado += $prob;
            if ($random <= $acumulado) {
                return $estados[$index];
            }
        }
        
        return 'disponible';
    }
}