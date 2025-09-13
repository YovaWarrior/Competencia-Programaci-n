<?php


namespace App\Http\Controllers;

use App\Models\Estacion;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MapaController extends Controller
{
    public function index()
    {
        $estaciones = Estacion::where('estado', 'activa')
            ->withCount(['bicicletas as bicicletas_disponibles' => function($query) {
                $query->where('estado', 'disponible');
            }])
            ->get();
        
        $rutas = [];
        if (Auth::check()) {
            $rutas = Auth::user()->rutas()->with(['estacionInicio', 'estacionFin'])->get();
        }
        
        return view('mapa.index', compact('estaciones', 'rutas'));
    }

    public function estacionesApi()
    {
        $estaciones = Estacion::where('estado', 'activa')
            ->withCount([
                'bicicletas as total_bicicletas',
                'bicicletas as bicicletas_disponibles' => function($query) {
                    $query->where('estado', 'disponible');
                },
                'bicicletas as bicicletas_tradicionales' => function($query) {
                    $query->where('tipo', 'tradicional')->where('estado', 'disponible');
                },
                'bicicletas as bicicletas_electricas' => function($query) {
                    $query->where('tipo', 'electrica')->where('estado', 'disponible');
                }
            ])
            ->get()
            ->map(function($estacion) {
                return [
                    'id' => $estacion->id,
                    'nombre' => $estacion->nombre,
                    'codigo' => $estacion->codigo,
                    'descripcion' => $estacion->descripcion,
                    'latitud' => (float) $estacion->latitud,
                    'longitud' => (float) $estacion->longitud,
                    'direccion' => $estacion->direccion,
                    'tipo' => $estacion->tipo,
                    'capacidad_total' => $estacion->capacidad_total,
                    'tiene_cargador_electrico' => $estacion->tiene_cargador_electrico,
                    'horario_operacion' => $estacion->horario_operacion,
                    'bicicletas' => [
                        'total' => $estacion->total_bicicletas,
                        'disponibles' => $estacion->bicicletas_disponibles,
                        'tradicionales' => $estacion->bicicletas_tradicionales,
                        'electricas' => $estacion->bicicletas_electricas,
                    ],
                    'estado_capacidad' => $this->getEstadoCapacidad($estacion),
                ];
            });

        return response()->json($estaciones);
    }

    public function rutasApi()
    {
        if (!Auth::check()) {
            return response()->json([]);
        }

        $rutas = Auth::user()->rutas()->with(['estacionInicio', 'estacionFin'])->get()
            ->map(function($ruta) {
                return [
                    'id' => $ruta->id,
                    'nombre' => $ruta->nombre,
                    'descripcion' => $ruta->descripcion,
                    'puntos_ruta' => $ruta->puntos_ruta,
                    'distancia_km' => $ruta->distancia_km,
                    'tiempo_estimado_minutos' => $ruta->tiempo_estimado_minutos,
                    'dificultad' => $ruta->dificultad,
                    'favorita' => $ruta->favorita,
                    'veces_usada' => $ruta->veces_usada,
                    'co2_reducido_estimado' => $ruta->co2_reducido_estimado,
                    'estacion_inicio' => [
                        'id' => $ruta->estacionInicio->id,
                        'nombre' => $ruta->estacionInicio->nombre,
                        'latitud' => (float) $ruta->estacionInicio->latitud,
                        'longitud' => (float) $ruta->estacionInicio->longitud,
                    ],
                    'estacion_fin' => [
                        'id' => $ruta->estacionFin->id,
                        'nombre' => $ruta->estacionFin->nombre,
                        'latitud' => (float) $ruta->estacionFin->latitud,
                        'longitud' => (float) $ruta->estacionFin->longitud,
                    ],
                ];
            });

        return response()->json($rutas);
    }

    private function getEstadoCapacidad($estacion)
    {
        $porcentaje = $estacion->bicicletas_disponibles / max($estacion->capacidad_total, 1) * 100;
        
        if ($porcentaje >= 70) return 'alta';
        if ($porcentaje >= 30) return 'media';
        return 'baja';
    }

    public function calcularRuta(Request $request)
    {
        $validated = $request->validate([
            'origen' => 'required|array',
            'origen.lat' => 'required|numeric',
            'origen.lng' => 'required|numeric',
            'destino' => 'required|array',
            'destino.lat' => 'required|numeric',
            'destino.lng' => 'required|numeric',
        ]);

        // Aquí podrías integrar con Google Maps API o similar
        // Por ahora retornamos una ruta simple
        $puntos = [
            $validated['origen'],
            $validated['destino']
        ];

        // Calcular distancia aproximada (fórmula haversine simplificada)
        $distancia = $this->calcularDistancia(
            $validated['origen']['lat'], $validated['origen']['lng'],
            $validated['destino']['lat'], $validated['destino']['lng']
        );

        return response()->json([
            'puntos_ruta' => $puntos,
            'distancia_km' => round($distancia, 2),
            'tiempo_estimado_minutos' => max(5, round($distancia / 15 * 60)), // 15 km/h promedio
            'co2_reducido_estimado' => round($distancia * 0.23, 2), // 0.23 kg CO2 por km
        ]);
    }

    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radio de la Tierra en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return $distance;
    }
}