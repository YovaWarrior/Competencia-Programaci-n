<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recompensa;

class RecompensasSeeder extends Seeder
{
    public function run()
    {
        $recompensas = [
            [
                'nombre' => '15 Minutos Gratis',
                'descripcion' => 'Disfruta de 15 minutos adicionales sin costo en tu próximo recorrido.',
                'puntos_requeridos' => 50,
                'tipo' => 'tiempo_gratis',
                'valor' => 15.00,
                'stock' => 1000,
                'activa' => true,
                'fecha_vencimiento' => null,
                'imagen' => null
            ],
            [
                'nombre' => '30 Minutos Gratis',
                'descripcion' => 'Media hora extra para explorar Puerto Barrios sin costo adicional.',
                'puntos_requeridos' => 100,
                'tipo' => 'tiempo_gratis',
                'valor' => 30.00,
                'stock' => 500,
                'activa' => true,
                'fecha_vencimiento' => null,
                'imagen' => null
            ],
            [
                'nombre' => 'Descuento 10% Próxima Membresía',
                'descripcion' => 'Obtén un 10% de descuento en la renovación de tu membresía.',
                'puntos_requeridos' => 200,
                'tipo' => 'descuento',
                'valor' => 10.00,
                'stock' => 300,
                'activa' => true,
                'fecha_vencimiento' => '2025-12-31',
                'imagen' => null
            ],
            [
                'nombre' => 'Botella EcoBici',
                'descripcion' => 'Botella reutilizable oficial de EcoBici Puerto Barrios.',
                'puntos_requeridos' => 150,
                'tipo' => 'merchandising',
                'valor' => 25.00,
                'stock' => 100,
                'activa' => true,
                'fecha_vencimiento' => null,
                'imagen' => null
            ],
            [
                'nombre' => 'Camiseta EcoBici',
                'descripcion' => 'Camiseta oficial de algodón orgánico con diseño exclusivo.',
                'puntos_requeridos' => 300,
                'tipo' => 'merchandising',
                'valor' => 50.00,
                'stock' => 50,
                'activa' => true,
                'fecha_vencimiento' => null,
                'imagen' => null
            ],
            [
                'nombre' => 'Tour Punta de Palma',
                'descripcion' => 'Experiencia guiada a la playa más hermosa del Caribe guatemalteco.',
                'puntos_requeridos' => 500,
                'tipo' => 'experiencia',
                'valor' => 150.00,
                'stock' => 20,
                'activa' => true,
                'fecha_vencimiento' => '2025-11-30',
                'imagen' => null
            ],
            [
                'nombre' => 'Descuento 20% Próxima Membresía',
                'descripcion' => 'Descuento especial del 20% para usuarios leales.',
                'puntos_requeridos' => 400,
                'tipo' => 'descuento',
                'valor' => 20.00,
                'stock' => 100,
                'activa' => true,
                'fecha_vencimiento' => '2025-12-31',
                'imagen' => null
            ],
            [
                'nombre' => 'Kit Ciclista EcoBici',
                'descripcion' => 'Kit completo: casco, luces LED y candado de seguridad.',
                'puntos_requeridos' => 800,
                'tipo' => 'merchandising',
                'valor' => 200.00,
                'stock' => 25,
                'activa' => true,
                'fecha_vencimiento' => null,
                'imagen' => null
            ]
        ];

        foreach ($recompensas as $recompensa) {
            Recompensa::create($recompensa);
        }
    }
}
