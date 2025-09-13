<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruta;

class RutasSeeder extends Seeder
{
    public function run()
    {
        $rutas = [
            [
                'user_id' => 2, // María
                'nombre' => 'Ruta Turística Centro-Muelle',
                'descripcion' => 'Recorrido perfecto para turistas que van del centro histórico al muelle municipal.',
                'puntos_ruta' => [
                    ['lat' => 15.7278, 'lng' => -88.5944],
                    ['lat' => 15.7285, 'lng' => -88.5930],
                    ['lat' => 15.7290, 'lng' => -88.5920]
                ],
                'estacion_inicio_id' => 1, // Centro Histórico
                'estacion_fin_id' => 3, // Muelle Municipal
                'distancia_km' => 1.2,
                'tiempo_estimado_minutos' => 8,
                'dificultad' => 'facil',
                'favorita' => true,
                'veces_usada' => 15,
                'co2_reducido_estimado' => 0.28
            ],
            [
                'user_id' => 3, // Carlos
                'nombre' => 'Ruta Comercial',
                'descripcion' => 'Ideal para ir de compras: del mercado al centro comercial.',
                'puntos_ruta' => [
                    ['lat' => 15.7265, 'lng' => -88.5955],
                    ['lat' => 15.7230, 'lng' => -88.5900],
                    ['lat' => 15.7200, 'lng' => -88.5850]
                ],
                'estacion_inicio_id' => 2, // Mercado Municipal
                'estacion_fin_id' => 6, // C.C. Pradera
                'distancia_km' => 2.1,
                'tiempo_estimado_minutos' => 12,
                'dificultad' => 'facil',
                'favorita' => false,
                'veces_usada' => 8,
                'co2_reducido_estimado' => 0.49
            ],
            [
                'user_id' => 4, // Ana
                'nombre' => 'Ruta Universitaria',
                'descripcion' => 'Conecta el centro con la universidad USAC.',
                'puntos_ruta' => [
                    ['lat' => 15.7278, 'lng' => -88.5944],
                    ['lat' => 15.7200, 'lng' => -88.5920],
                    ['lat' => 15.7150, 'lng' => -88.5900]
                ],
                'estacion_inicio_id' => 1, // Centro Histórico
                'estacion_fin_id' => 8, // USAC Cunizab
                'distancia_km' => 1.8,
                'tiempo_estimado_minutos' => 10,
                'dificultad' => 'facil',
                'favorita' => true,
                'veces_usada' => 22,
                'co2_reducido_estimado' => 0.42
            ],
            [
                'user_id' => 2, // María
                'nombre' => 'Ruta Aeropuerto Express',
                'descripcion' => 'Conexión rápida del centro al aeropuerto.',
                'puntos_ruta' => [
                    ['lat' => 15.7278, 'lng' => -88.5944],
                    ['lat' => 15.7350, 'lng' => -88.5870],
                    ['lat' => 15.7450, 'lng' => -88.5800]
                ],
                'estacion_inicio_id' => 1, // Centro Histórico
                'estacion_fin_id' => 5, // Aeropuerto
                'distancia_km' => 3.2,
                'tiempo_estimado_minutos' => 18,
                'dificultad' => 'moderada',
                'favorita' => false,
                'veces_usada' => 5,
                'co2_reducido_estimado' => 0.75
            ],
            [
                'user_id' => 4, // Ana
                'nombre' => 'Ruta Resort Luxury',
                'descripcion' => 'Para llegar al exclusivo Amatique Bay Resort.',
                'puntos_ruta' => [
                    ['lat' => 15.7200, 'lng' => -88.5850],
                    ['lat' => 15.7150, 'lng' => -88.5775],
                    ['lat' => 15.7100, 'lng' => -88.5700]
                ],
                'estacion_inicio_id' => 6, // C.C. Pradera
                'estacion_fin_id' => 7, // Amatique Bay
                'distancia_km' => 2.5,
                'tiempo_estimado_minutos' => 15,
                'dificultad' => 'moderada',
                'favorita' => true,
                'veces_usada' => 12,
                'co2_reducido_estimado' => 0.58
            ]
        ];

        foreach ($rutas as $ruta) {
            Ruta::create($ruta);
        }
    }
}
