<?php

namespace App\Http\Controllers;

use App\Models\UsoBicicleta;
use App\Models\Bicicleta;
use App\Models\User;
use App\Models\UserMembresia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function uso(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        
        // Reporte de uso por día
        $usosPorDia = UsoBicicleta::select(
                DB::raw('DATE(fecha_hora_inicio) as fecha'),
                DB::raw('COUNT(*) as total_usos'),
                DB::raw('SUM(duracion_minutos) as total_minutos'),
                DB::raw('SUM(co2_reducido) as total_co2')
            )
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Bicicletas más usadas
        $bicicletasMasUsadas = UsoBicicleta::select('bicicleta_id', DB::raw('COUNT(*) as total_usos'))
            ->with('bicicleta')
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->groupBy('bicicleta_id')
            ->orderBy('total_usos', 'desc')
            ->take(10)
            ->get();

        // Usuarios más activos
        $usuariosMasActivos = UsoBicicleta::select('user_id', DB::raw('COUNT(*) as total_usos'))
            ->with('user')
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->groupBy('user_id')
            ->orderBy('total_usos', 'desc')
            ->take(10)
            ->get();
        
        return view('reportes.uso', compact(
            'usosPorDia', 'bicicletasMasUsadas', 'usuariosMasActivos', 
            'fechaInicio', 'fechaFin'
        ));
    }

    public function ingresos(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        
        // Ingresos por membresías
        $ingresosMembresías = UserMembresia::select(
                DB::raw('DATE(fecha_inicio) as fecha'),
                DB::raw('SUM(monto_pagado) as total_ingresos'),
                DB::raw('COUNT(*) as total_membresías')
            )
            ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'pagado')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Ingresos por minutos extra
        $ingresosExtra = UsoBicicleta::select(
                DB::raw('DATE(fecha_hora_inicio) as fecha'),
                DB::raw('SUM(costo_extra) as total_extra'),
                DB::raw('SUM(minutos_extra) as total_minutos_extra')
            )
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->where('costo_extra', '>', 0)
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Totales
        $totalMembresías = $ingresosMembresías->sum('total_ingresos');
        $totalExtra = $ingresosExtra->sum('total_extra');
        $totalGeneral = $totalMembresías + $totalExtra;
        
        return view('reportes.ingresos', compact(
            'ingresosMembresías', 'ingresosExtra', 'totalMembresías', 
            'totalExtra', 'totalGeneral', 'fechaInicio', 'fechaFin'
        ));
    }

    public function co2(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        
        // CO2 reducido por día
        $co2PorDia = UsoBicicleta::select(
                DB::raw('DATE(fecha_hora_inicio) as fecha'),
                DB::raw('SUM(co2_reducido) as total_co2'),
                DB::raw('COUNT(*) as total_recorridos'),
                DB::raw('SUM(distancia_recorrida) as total_distancia')
            )
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // CO2 por tipo de bicicleta
        $co2PorTipo = UsoBicicleta::join('bicicletas', 'uso_bicicletas.bicicleta_id', '=', 'bicicletas.id')
            ->select(
                'bicicletas.tipo',
                DB::raw('SUM(uso_bicicletas.co2_reducido) as total_co2'),
                DB::raw('COUNT(*) as total_usos')
            )
            ->whereBetween('uso_bicicletas.fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('uso_bicicletas.estado', 'completado')
            ->groupBy('bicicletas.tipo')
            ->get();

        // Totales
        $totalCo2 = $co2PorDia->sum('total_co2');
        $totalRecorridos = $co2PorDia->sum('total_recorridos');
        $totalDistancia = $co2PorDia->sum('total_distancia');
        
        // Equivalencias impactantes
        $equivalencias = [
            'arboles_plantados' => round($totalCo2 / 21.8, 1), // 1 árbol = 21.8 kg CO2/año
            'autos_un_dia' => round($totalCo2 / 4.6, 1), // Auto promedio = 4.6 kg CO2/día
            'km_auto_evitados' => round($totalDistancia, 1),
        ];
        
        return view('reportes.co2', compact(
            'co2PorDia', 'co2PorTipo', 'totalCo2', 'totalRecorridos', 
            'totalDistancia', 'equivalencias', 'fechaInicio', 'fechaFin'
        ));
    }

    public function bicicletasHistorial(Request $request, $bicicletaId)
    {
        $bicicleta = Bicicleta::findOrFail($bicicletaId);
        
        $historial = UsoBicicleta::with(['user', 'estacionInicio', 'estacionFin'])
            ->where('bicicleta_id', $bicicletaId)
            ->where('estado', 'completado')
            ->latest('fecha_hora_inicio')
            ->paginate(20);
        
        return view('reportes.bicicleta-historial', compact('bicicleta', 'historial'));
    }
}