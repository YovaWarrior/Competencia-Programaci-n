<?php

namespace App\Http\Controllers;

use App\Models\UsoBicicleta;
use App\Models\UserMembresia;
use App\Models\User;
use App\Models\Bicicleta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * Reporte de CO₂ reducido
     */
    public function co2(Request $request)
    {
        $user = Auth::user();
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        
        // Estadísticas del usuario actual
        $estadisticasUsuario = $this->calcularEstadisticasCO2($user, $fechaInicio, $fechaFin);
        
        // Ranking comunitario
        $ranking = $this->obtenerRankingCO2();
        
        // Datos del gráfico temporal
        $datosGrafico = $this->obtenerDatosGraficoCO2($user, $fechaInicio, $fechaFin);
        
        return view('reportes.co2', array_merge($estadisticasUsuario, [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'ranking' => $ranking,
            'datosGrafico' => $datosGrafico
        ]));
    }

    /**
     * Reporte de uso de bicicletas
     */
    public function uso(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        
        // Usos por día
        $usosPorDia = UsoBicicleta::select(
                DB::raw('DATE(fecha_hora_inicio) as fecha'),
                DB::raw('COUNT(*) as total_usos'),
                DB::raw('SUM(duracion_minutos) as total_minutos'),
                DB::raw('COUNT(DISTINCT user_id) as usuarios_unicos')
            )
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Bicicletas más usadas
        $bicicletasMasUsadas = Bicicleta::select('bicicletas.*')
            ->withCount(['usosBicicletas as total_usos' => function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
                      ->where('estado', 'completado');
            }])
            ->having('total_usos', '>', 0)
            ->orderBy('total_usos', 'desc')
            ->take(10)
            ->get();

        // Usuarios más activos
        $usuariosMasActivos = User::select('users.*')
            ->withCount(['usosBicicletas as total_usos' => function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
                      ->where('estado', 'completado');
            }])
            ->having('total_usos', '>', 0)
            ->orderBy('total_usos', 'desc')
            ->take(10)
            ->get();

        // Estadísticas por tipo de bicicleta
        $estatisticasPorTipo = UsoBicicleta::join('bicicletas', 'uso_bicicletas.bicicleta_id', '=', 'bicicletas.id')
            ->select(
                'bicicletas.tipo',
                DB::raw('COUNT(*) as total_usos'),
                DB::raw('SUM(uso_bicicletas.duracion_minutos) as total_minutos'),
                DB::raw('AVG(uso_bicicletas.duracion_minutos) as promedio_minutos')
            )
            ->whereBetween('uso_bicicletas.fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('uso_bicicletas.estado', 'completado')
            ->groupBy('bicicletas.tipo')
            ->get();
        
        return view('reportes.uso', compact(
            'usosPorDia', 'bicicletasMasUsadas', 'usuariosMasActivos', 
            'estatisticasPorTipo', 'fechaInicio', 'fechaFin'
        ));
    }

    /**
     * Reporte de ingresos
     */
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

        // Ingresos por tipo de membresía
        $ingresosPorTipo = UserMembresia::join('membresias', 'user_membresias.membresia_id', '=', 'membresias.id')
            ->select(
                'membresias.nombre',
                'membresias.tipo_bicicleta',
                'membresias.duracion',
                DB::raw('SUM(user_membresias.monto_pagado) as total_ingresos'),
                DB::raw('COUNT(*) as total_ventas')
            )
            ->whereBetween('user_membresias.fecha_inicio', [$fechaInicio, $fechaFin])
            ->where('user_membresias.estado_pago', 'pagado')
            ->groupBy('membresias.id', 'membresias.nombre', 'membresias.tipo_bicicleta', 'membresias.duracion')
            ->orderBy('total_ingresos', 'desc')
            ->get();

        // Métodos de pago más utilizados
        $metodosPago = UserMembresia::select(
                'metodo_pago',
                DB::raw('COUNT(*) as total_transacciones'),
                DB::raw('SUM(monto_pagado) as total_monto')
            )
            ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'pagado')
            ->groupBy('metodo_pago')
            ->orderBy('total_monto', 'desc')
            ->get();

        // Resumen general
        $resumenGeneral = [
            'total_ingresos' => UserMembresia::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->where('estado_pago', 'pagado')
                ->sum('monto_pagado'),
            'total_transacciones' => UserMembresia::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->where('estado_pago', 'pagado')
                ->count(),
            'promedio_transaccion' => UserMembresia::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->where('estado_pago', 'pagado')
                ->avg('monto_pagado'),
            'usuarios_activos' => UserMembresia::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->where('estado_pago', 'pagado')
                ->distinct('user_id')
                ->count('user_id')
        ];
        
        return view('reportes.ingresos', compact(
            'ingresosMembresías', 'ingresosPorTipo', 'metodosPago', 
            'resumenGeneral', 'fechaInicio', 'fechaFin'
        ));
    }

    /**
     * Historial de una bicicleta específica
     */
    public function bicicletaHistorial($bicicletaId, Request $request)
    {
        $bicicleta = Bicicleta::findOrFail($bicicletaId);
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        
        $historial = UsoBicicleta::with(['user', 'estacionInicio', 'estacionFin'])
            ->where('bicicleta_id', $bicicletaId)
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_hora_inicio', 'desc')
            ->paginate(20);

        $estadisticas = [
            'total_usos' => $historial->total(),
            'total_minutos' => $bicicleta->usosBicicletas()
                ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
                ->sum('duracion_minutos'),
            'usuarios_unicos' => $bicicleta->usosBicicletas()
                ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
                ->distinct('user_id')
                ->count('user_id'),
            'promedio_duracion' => $bicicleta->usosBicicletas()
                ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
                ->avg('duracion_minutos')
        ];
        
        return view('reportes.bicicleta-historial', compact(
            'bicicleta', 'historial', 'estadisticas', 'fechaInicio', 'fechaFin'
        ));
    }

    /**
     * Calcular estadísticas de CO₂ para un usuario
     */
    private function calcularEstadisticasCO2($user, $fechaInicio, $fechaFin)
    {
        $usos = UsoBicicleta::where('user_id', $user->id)
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->get();

        $totalKilometros = $usos->sum('distancia_km') ?: 0;
        $totalMinutos = $usos->sum('duracion_minutos') ?: 0;
        $totalRecorridos = $usos->count();
        
        // Cálculo de CO₂ reducido
        // Promedio: 0.25 kg CO₂ por km en auto vs 0 kg en bicicleta
        $totalCO2Reducido = $totalKilometros * 0.25;

        return [
            'totalKilometros' => $totalKilometros,
            'totalMinutos' => $totalMinutos,
            'totalRecorridos' => $totalRecorridos,
            'totalCO2Reducido' => $totalCO2Reducido
        ];
    }

    /**
     * Obtener ranking de CO₂ reducido
     */
    private function obtenerRankingCO2()
    {
        return User::select('users.*')
            ->selectRaw('
                SUM(CASE WHEN uso_bicicletas.distancia_km IS NOT NULL 
                    THEN uso_bicicletas.distancia_km * 0.25 
                    ELSE (uso_bicicletas.duracion_minutos / 60) * 15 * 0.25 
                END) as total_co2
            ')
            ->selectRaw('COUNT(uso_bicicletas.id) as total_recorridos')
            ->selectRaw('
                SUM(CASE WHEN uso_bicicletas.distancia_km IS NOT NULL 
                    THEN uso_bicicletas.distancia_km 
                    ELSE (uso_bicicletas.duracion_minutos / 60) * 15 
                END) as total_km
            ')
            ->leftJoin('uso_bicicletas', function($join) {
                $join->on('users.id', '=', 'uso_bicicletas.user_id')
                     ->where('uso_bicicletas.estado', '=', 'completado')
                     ->where('uso_bicicletas.fecha_hora_inicio', '>=', Carbon::now()->subMonths(3));
            })
            ->groupBy('users.id', 'users.nombre', 'users.email')
            ->having('total_co2', '>', 0)
            ->orderBy('total_co2', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Obtener datos para gráfico de CO₂
     */
    private function obtenerDatosGraficoCO2($user, $fechaInicio, $fechaFin)
    {
        $datos = UsoBicicleta::where('user_id', $user->id)
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->select(
                DB::raw('DATE(fecha_hora_inicio) as fecha'),
                DB::raw('SUM(CASE WHEN distancia_km IS NOT NULL 
                    THEN distancia_km * 0.25 
                    ELSE (duracion_minutos / 60) * 15 * 0.25 
                END) as co2_diario')
            )
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return $datos->map(function($item) {
            return [
                'fecha' => Carbon::parse($item->fecha)->format('d/m'),
                'co2' => round($item->co2_diario, 2)
            ];
        });
    }

    /**
     * Exportar reporte en diferentes formatos
     */
    public function exportar($tipo, Request $request)
    {
        $formato = $request->input('formato', 'pdf');
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        switch ($tipo) {
            case 'co2':
                return $this->exportarCO2($formato, $fechaInicio, $fechaFin);
            case 'uso':
                return $this->exportarUso($formato, $fechaInicio, $fechaFin);
            case 'ingresos':
                return $this->exportarIngresos($formato, $fechaInicio, $fechaFin);
            default:
                return back()->withErrors(['error' => 'Tipo de reporte no válido']);
        }
    }

    /**
     * Exportar reporte de CO₂
     */
    private function exportarCO2($formato, $fechaInicio, $fechaFin)
    {
        $user = Auth::user();
        $datos = $this->calcularEstadisticasCO2($user, $fechaInicio, $fechaFin);
        
        if ($formato === 'json') {
            return response()->json([
                'tipo' => 'reporte_co2',
                'periodo' => "$fechaInicio al $fechaFin",
                'usuario' => $user->nombre,
                'datos' => $datos,
                'generado' => now()->toISOString()
            ]);
        }
        
        // Para otros formatos, retornar datos básicos
        return response()->json($datos);
    }

    /**
     * Exportar reporte de uso
     */
    private function exportarUso($formato, $fechaInicio, $fechaFin)
    {
        $usos = UsoBicicleta::with(['user', 'bicicleta', 'estacionInicio', 'estacionFin'])
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->get();

        if ($formato === 'json') {
            return response()->json([
                'tipo' => 'reporte_uso',
                'periodo' => "$fechaInicio al $fechaFin",
                'total_registros' => $usos->count(),
                'datos' => $usos,
                'generado' => now()->toISOString()
            ]);
        }

        return response()->json(['total_usos' => $usos->count()]);
    }

    /**
     * Exportar reporte de ingresos
     */
    private function exportarIngresos($formato, $fechaInicio, $fechaFin)
    {
        $ingresos = UserMembresia::with(['user', 'membresia'])
            ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'pagado')
            ->get();

        $totalIngresos = $ingresos->sum('monto_pagado');

        if ($formato === 'json') {
            return response()->json([
                'tipo' => 'reporte_ingresos',
                'periodo' => "$fechaInicio al $fechaFin",
                'total_ingresos' => $totalIngresos,
                'total_transacciones' => $ingresos->count(),
                'datos' => $ingresos,
                'generado' => now()->toISOString()
            ]);
        }

        return response()->json(['total_ingresos' => $totalIngresos]);
    }

    /**
     * Obtener estadísticas generales del sistema
     */
    public function estadisticasGenerales()
    {
        $estadisticas = [
            'usuarios_activos' => User::whereHas('usosBicicletas', function($query) {
                $query->where('fecha_hora_inicio', '>=', Carbon::now()->subMonth());
            })->count(),
            
            'total_recorridos_mes' => UsoBicicleta::where('fecha_hora_inicio', '>=', Carbon::now()->subMonth())
                ->where('estado', 'completado')
                ->count(),
            
            'co2_total_reducido' => UsoBicicleta::where('estado', 'completado')
                ->sum(DB::raw('CASE WHEN distancia_km IS NOT NULL 
                    THEN distancia_km * 0.25 
                    ELSE (duracion_minutos / 60) * 15 * 0.25 
                END')),
            
            'ingresos_mes' => UserMembresia::where('fecha_inicio', '>=', Carbon::now()->subMonth())
                ->where('estado_pago', 'pagado')
                ->sum('monto_pagado'),
            
            'bicicletas_disponibles' => Bicicleta::where('estado', 'disponible')->count(),
            
            'promedio_duracion' => UsoBicicleta::where('estado', 'completado')
                ->avg('duracion_minutos')
        ];

        return response()->json($estadisticas);
    }
}