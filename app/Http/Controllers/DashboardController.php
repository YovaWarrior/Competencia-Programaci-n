<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bicicleta;
use App\Models\Estacion;
use App\Models\UsoBicicleta;
use App\Models\Recompensa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Estadísticas del usuario
        $stats = [
            'recorridos_totales' => $user->usosBicicletas()->where('estado', 'completado')->count(),
            'co2_reducido_total' => $user->co2_reducido_total,
            'puntos_verdes' => $user->puntos_verdes,
            'tiempo_total_minutos' => $user->usosBicicletas()->where('estado', 'completado')->sum('duracion_minutos'),
        ];

        // Membresía activa
        $membresiaActiva = $user->membresiaActiva;
        
        // Últimos recorridos
        $ultimosRecorridos = $user->usosBicicletas()
            ->with(['bicicleta', 'estacionInicio', 'estacionFin'])
            ->latest()
            ->take(5)
            ->get();

        // Recompensas disponibles
        $recompensasDisponibles = Recompensa::where('activa', true)
            ->where('puntos_requeridos', '<=', $user->puntos_verdes)
            ->where('stock', '>', 0)
            ->take(3)
            ->get();

        // Uso actual (si hay alguno en curso)
        $usoEnCurso = $user->usosBicicletas()->where('estado', 'en_curso')->first();

        return view('dashboard.index', compact(
            'stats', 'membresiaActiva', 'ultimosRecorridos', 
            'recompensasDisponibles', 'usoEnCurso'
        ));
    }

    public function stats()
    {
        $user = Auth::user();
        
        // Estadísticas por mes (últimos 6 meses)
        $statsMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('Y-m');
            
            $recorridos = $user->usosBicicletas()
                ->whereYear('fecha_hora_inicio', $date->year)
                ->whereMonth('fecha_hora_inicio', $date->month)
                ->where('estado', 'completado')
                ->get();
            
            $statsMonth[] = [
                'month' => $date->format('M Y'),
                'recorridos' => $recorridos->count(),
                'co2_reducido' => $recorridos->sum('co2_reducido'),
                'minutos' => $recorridos->sum('duracion_minutos'),
            ];
        }

        return view('dashboard.stats', compact('statsMonth'));
    }
}