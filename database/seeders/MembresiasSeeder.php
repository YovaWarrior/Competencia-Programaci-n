<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membresia;

class MembresiasSeeder extends Seeder
{
    public function run()
    {
        $membresias = [
            // MEMBRESÍAS TRADICIONALES
            [
                'nombre' => 'EcoBici Tradicional Mensual',
                'descripcion' => 'Acceso ilimitado a bicicletas tradicionales por 30 días. Ideal para uso diario en la ciudad.',
                'tipo_bicicleta' => 'tradicional',
                'duracion' => 'mensual',
                'precio' => 75.00,
                'duracion_dias' => 30,
                'minutos_incluidos' => 1800, // 30 horas
                'tarifa_minuto_extra' => 0.50,
                'beneficios' => [
                    'Acceso 24/7 a todas las estaciones',
                    '30 horas mensuales incluidas',
                    'Mapa de rutas ecológicas',
                    'Puntos verdes por cada recorrido',
                    'Reporte personal de CO₂ ahorrado'
                ],
                'activa' => true
            ],
            [
                'nombre' => 'EcoBici Tradicional Anual',
                'descripcion' => 'Plan anual para bicicletas tradicionales con descuento del 20%. Perfecto para usuarios frecuentes.',
                'tipo_bicicleta' => 'tradicional',
                'duracion' => 'anual',
                'precio' => 720.00,
                'duracion_dias' => 365,
                'minutos_incluidos' => 21600, // 360 horas
                'tarifa_minuto_extra' => 0.40,
                'beneficios' => [
                    'Acceso 24/7 a todas las estaciones',
                    '360 horas anuales incluidas',
                    '20% descuento vs plan mensual',
                    'Prioridad en reservas',
                    'Puntos verdes DOBLES',
                    'Descuentos en tiendas afiliadas'
                ],
                'activa' => true
            ],

            // MEMBRESÍAS ELÉCTRICAS
            [
                'nombre' => 'EcoBici Eléctrica Mensual',
                'descripcion' => 'Acceso a bicicletas eléctricas para recorridos más largos y cómodos.',
                'tipo_bicicleta' => 'electrica',
                'duracion' => 'mensual',
                'precio' => 150.00,
                'duracion_dias' => 30,
                'minutos_incluidos' => 900, // 15 horas
                'tarifa_minuto_extra' => 1.00,
                'beneficios' => [
                    'Acceso a bicicletas eléctricas',
                    'Estaciones de carga incluidas',
                    '15 horas mensuales incluidas',
                    'Mayor velocidad y comodidad',
                    'Puntos verdes x1.5'
                ],
                'activa' => true
            ],
            [
                'nombre' => 'EcoBici Eléctrica Anual',
                'descripcion' => 'Plan anual premium para bicicletas eléctricas con máximos beneficios.',
                'tipo_bicicleta' => 'electrica',
                'duracion' => 'anual',
                'precio' => 1440.00,
                'duracion_dias' => 365,
                'minutos_incluidos' => 10800, // 180 horas
                'tarifa_minuto_extra' => 0.80,
                'beneficios' => [
                    'Acceso a bicicletas eléctricas',
                    '180 horas anuales incluidas',
                    '20% descuento vs plan mensual',
                    'Reserva anticipada',
                    'Puntos verdes DOBLES',
                    'Seguro incluido'
                ],
                'activa' => true
            ],

            // MEMBRESÍAS PREMIUM
            [
                'nombre' => 'EcoBici Premium Mensual',
                'descripcion' => 'Acceso completo a bicicletas tradicionales Y eléctricas.',
                'tipo_bicicleta' => 'ambas',
                'duracion' => 'mensual',
                'precio' => 200.00,
                'duracion_dias' => 30,
                'minutos_incluidos' => 2400, // 40 horas
                'tarifa_minuto_extra' => 0.75,
                'beneficios' => [
                    'Acceso a TODAS las bicicletas',
                    'Flexibilidad total',
                    '40 horas mensuales incluidas',
                    'Puntos verdes x2',
                    'Soporte VIP 24/7'
                ],
                'activa' => true
            ],
            [
                'nombre' => 'EcoBici Premium Anual',
                'descripcion' => 'Plan anual VIP con acceso ilimitado a todo el ecosistema EcoBici.',
                'tipo_bicicleta' => 'ambas',
                'duracion' => 'anual',
                'precio' => 1920.00,
                'duracion_dias' => 365,
                'minutos_incluidos' => 28800, // 480 horas
                'tarifa_minuto_extra' => 0.50,
                'beneficios' => [
                    'Acceso ILIMITADO a todas las bicicletas',
                    '480 horas anuales incluidas',
                    '20% descuento vs plan mensual',
                    'Puntos verdes TRIPLE',
                    'Experiencias exclusivas',
                    'Seguro premium incluido'
                ],
                'activa' => true
            ]
        ];

        foreach ($membresias as $membresia) {
            Membresia::create($membresia);
        }
    }
}