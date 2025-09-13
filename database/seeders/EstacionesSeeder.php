<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estacion;

class EstacionesSeeder extends Seeder
{
    public function run()
    {
        $estaciones = [
            [
                'nombre' => 'Estación Centro Histórico',
                'codigo' => 'ECO-PB001',
                'descripcion' => 'Estación principal en el corazón de Puerto Barrios.',
                'latitud' => 15.7278,
                'longitud' => -88.5944,
                'direccion' => 'Parque Central, 6ta Avenida y 5ta Calle, Puerto Barrios',
                'tipo' => 'mixta',
                'capacidad_total' => 24,
                'capacidad_disponible' => 20,
                'tiene_cargador_electrico' => true,
                'estado' => 'activa',
                'horario_operacion' => ['inicio' => '05:00', 'fin' => '22:00']
            ],
            [
                'nombre' => 'Estación Mercado Municipal',
                'codigo' => 'ECO-PB002',
                'descripcion' => 'Ubicada cerca del mercado central.',
                'latitud' => 15.7265,
                'longitud' => -88.5955,
                'direccion' => 'A 2 cuadras del Mercado Municipal, Puerto Barrios',
                'tipo' => 'seleccion',
                'capacidad_total' => 20,
                'capacidad_disponible' => 18,
                'tiene_cargador_electrico' => false,
                'estado' => 'activa',
                'horario_operacion' => ['inicio' => '04:30', 'fin' => '20:00']
            ],
            [
                'nombre' => 'Estación Muelle Municipal',
                'codigo' => 'ECO-PB003',
                'descripcion' => 'Estación turística principal, conecta con ferrys a Livingston.',
                'latitud' => 15.7290,
                'longitud' => -88.5920,
                'direccion' => 'Muelle Municipal, Puerto Barrios',
                'tipo' => 'carga',
                'capacidad_total' => 30,
                'capacidad_disponible' => 25,
                'tiene_cargador_electrico' => true,
                'estado' => 'activa',
                'horario_operacion' => ['inicio' => '05:00', 'fin' => '23:00']
            ],
            [
                'nombre' => 'Estación Santo Tomás de Castilla',
                'codigo' => 'ECO-PB004',
                'descripcion' => 'Estación en el puerto comercial más importante.',
                'latitud' => 15.7050,
                'longitud' => -88.6100,
                'direccion' => 'Puerto Santo Tomás de Castilla',
                'tipo' => 'mixta',
                'capacidad_total' => 18,
                'capacidad_disponible' => 15,
                'tiene_cargador_electrico' => true,
                'estado' => 'activa',
                'horario_operacion' => ['inicio' => '06:00', 'fin' => '22:00']
            ],
            [
                'nombre' => 'Estación Aeropuerto',
                'codigo' => 'ECO-PB005',
                'descripcion' => 'Conecta con el Aeropuerto de Puerto Barrios.',
                'latitud' => 15.7450,
                'longitud' => -88.5800,
                'direccion' => 'Aeropuerto de Puerto Barrios',
                'tipo' => 'carga',
                'capacidad_total' => 16,
                'capacidad_disponible' => 12,
                'tiene_cargador_electrico' => true,
                'estado' => 'activa',
                'horario_operacion' => ['inicio' => '05:30', 'fin' => '21:30']
            ],
            [
                'nombre' => 'Estación C.C. Pradera',
                'codigo' => 'ECO-PB006',
                'descripcion' => 'Estación en el centro comercial principal.',
                'latitud' => 15.7200,
                'longitud' => -88.5850,
                'direccion' => 'Centro Comercial Pradera Puerto Barrios',
                'tipo' => 'seleccion',
                'capacidad_total' => 22,
                'capacidad_disponible' => 19,
                'tiene_cargador_electrico' => true,
                'estado' => 'activa',
                'horario_operacion' => ['inicio' => '08:00', 'fin' => '22:00']
            ],
            [
                'nombre' => 'Estación Amatique Bay',
                'codigo' => 'ECO-PB007',
                'descripcion' => 'Estación exclusiva cerca del resort Amatique Bay.',
                'latitud' => 15.7100,
                'longitud' => -88.5700,
                'direccion' => 'Cerca de Amatique Bay Resort',
                'tipo' => 'carga',
                'capacidad_total' => 12,
                'capacidad_disponible' => 10,
                'tiene_cargador_electrico' => true,
                'estado' => 'activa',
                'horario_operacion' => ['inicio' => '06:00', 'fin' => '20:00']
            ],
            [
                'nombre' => 'Estación USAC Cunizab',
                'codigo' => 'ECO-PB008',
                'descripcion' => 'Estación universitaria para estudiantes.',
                'latitud' => 15.7150,
                'longitud' => -88.5900,
                'direccion' => 'Centro Universitario de Izabal CUNIZAB-USAC',
                'tipo' => 'mixta',
                'capacidad_total' => 20,
                'capacidad_disponible' => 16,
                'tiene_cargador_electrico' => true,
                'estado' => 'activa',
                'horario_operacion' => ['inicio' => '06:00', 'fin' => '21:00']
            ]
        ];

        foreach ($estaciones as $estacion) {
            Estacion::create($estacion);
        }
    }
}