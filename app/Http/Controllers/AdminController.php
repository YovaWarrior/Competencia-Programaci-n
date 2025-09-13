<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bicicleta;
use App\Models\Estacion;
use App\Models\UsoBicicleta;
use App\Models\UserMembresia;
use App\Models\ReporteDano;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->esAdmin()) {
                abort(403, 'Acceso denegado');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'usuarios_total' => User::where('rol', 'usuario')->count(),
            'usuarios_activos' => User::where('rol', 'usuario')
                ->whereHas('membresias', function($query) {
                    $query->where('activa', true)->where('fecha_fin', '>', now());
                })->count(),
            'bicicletas_total' => Bicicleta::count(),
            'bicicletas_disponibles' => Bicicleta::where('estado', 'disponible')->count(),
            'recorridos_hoy' => UsoBicicleta::whereDate('fecha_hora_inicio', today())->count(),
            'ingresos_mes' => UserMembresia::whereMonth('fecha_inicio', now()->month)
                ->where('estado_pago', 'pagado')->sum('monto_pagado'),
            'co2_mes' => UsoBicicleta::whereMonth('fecha_hora_inicio', now()->month)
                ->where('estado', 'completado')->sum('co2_reducido'),
            'reportes_pendientes' => ReporteDano::where('estado', 'reportado')->count(),
        ];

        // Gráfico de uso en los últimos 7 días
        $usosSemana = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $usosSemana[] = [
                'fecha' => $fecha->format('d/m'),
                'usos' => UsoBicicleta::whereDate('fecha_hora_inicio', $fecha)->count()
            ];
        }

        return view('admin.dashboard', compact('stats', 'usosSemana'));
    }

    public function usuarios()
    {
        $usuarios = User::where('rol', 'usuario')
            ->with('membresiaActiva.membresia')
            ->withCount('usosBicicletas')
            ->latest()
            ->paginate(20);
        
        return view('admin.usuarios', compact('usuarios'));
    }

    public function usuarioDetalle($id)
    {
        $usuario = User::with([
            'membresias.membresia',
            'usosBicicletas.bicicleta',
            'reportesDanos.bicicleta'
        ])->findOrFail($id);
        
        return view('admin.usuario-detalle', compact('usuario'));
    }

    public function bicicletas()
    {
        $bicicletas = Bicicleta::with(['estacionActual', 'reportesDanos' => function($query) {
            $query->where('estado', 'reportado');
        }])
        ->withCount('usosBicicletas')
        ->latest()
        ->paginate(20);
        
        return view('admin.bicicletas', compact('bicicletas'));
    }

    public function bicicletaDetalle($id)
    {
        $bicicleta = Bicicleta::with([
            'estacionActual',
            'usosBicicletas.user',
            'reportesDanos.user'
        ])->findOrFail($id);
        
        return view('admin.bicicleta-detalle', compact('bicicleta'));
    }

    public function estaciones()
    {
        $estaciones = Estacion::withCount([
            'bicicletas',
            'bicicletas as bicicletas_disponibles' => function($query) {
                $query->where('estado', 'disponible');
            }
        ])->get();
        
        return view('admin.estaciones', compact('estaciones'));
    }

    public function reportesDanos()
    {
        $reportes = ReporteDano::with(['user', 'bicicleta'])
            ->latest('fecha_reporte')
            ->paginate(20);
        
        return view('admin.reportes-danos', compact('reportes'));
    }

    public function actualizarReporteDano(Request $request, $id)
    {
        $validated = $request->validate([
            'estado' => 'required|in:en_revision,reparado,descartado',
            'comentarios_tecnico' => 'nullable|string|max:500',
        ]);

        $reporte = ReporteDano::findOrFail($id);
        
        $reporte->update([
            'estado' => $validated['estado'],
            'comentarios_tecnico' => $validated['comentarios_tecnico'],
            'fecha_resolucion' => $validated['estado'] !== 'en_revision' ? now() : null,
        ]);

        // Si se marca como reparado, desbloquear la bicicleta
        if ($validated['estado'] === 'reparado') {
            $reporte->bicicleta->update([
                'bloqueada' => false,
                'motivo_bloqueo' => null,
                'estado' => 'disponible',
            ]);
        }

        return back()->with('success', 'Reporte actualizado correctamente.');
    }

    public function cambiarEstadoBicicleta(Request $request, $id)
    {
        $validated = $request->validate([
            'estado' => 'required|in:disponible,en_uso,mantenimiento,danada,fuera_servicio',
            'motivo_bloqueo' => 'nullable|string|max:255',
        ]);

        $bicicleta = Bicicleta::findOrFail($id);
        
        $bicicleta->update([
            'estado' => $validated['estado'],
            'bloqueada' => in_array($validated['estado'], ['danada', 'fuera_servicio']),
            'motivo_bloqueo' => $validated['motivo_bloqueo'],
        ]);

        return back()->with('success', 'Estado de la bicicleta actualizado.');
    }

    public function exportarReporte(Request $request, $tipo)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        
        switch ($tipo) {
            case 'usuarios':
                return $this->exportarUsuarios();
            case 'bicicletas':
                return $this->exportarBicicletas();
            case 'uso':
                return $this->exportarUso($fechaInicio, $fechaFin);
            case 'ingresos':
                return $this->exportarIngresos($fechaInicio, $fechaFin);
            default:
                abort(404);
        }
    }

    private function exportarUsuarios()
    {
        $usuarios = User::where('rol', 'usuario')
            ->with('membresiaActiva.membresia')
            ->get()
            ->map(function($user) {
                return [
                    'DPI' => $user->dpi,
                    'Nombre Completo' => $user->nombre . ' ' . $user->apellido,
                    'Email' => $user->email,
                    'Teléfono' => $user->telefono,
                    'Fecha Nacimiento' => $user->fecha_nacimiento->format('d/m/Y'),
                    'Membresía Actual' => $user->membresiaActiva ? $user->membresiaActiva->membresia->nombre : 'Sin membresía',
                    'Puntos Verdes' => $user->puntos_verdes,
                    'CO₂ Reducido (kg)' => $user->co2_reducido_total,
                    'Estado' => $user->activo ? 'Activo' : 'Inactivo',
                ];
            });

        return response()->json($usuarios);
    }

    private function exportarBicicletas()
    {
        $bicicletas = Bicicleta::with('estacionActual')
            ->withCount('usosBicicletas')
            ->get()
            ->map(function($bicicleta) {
                return [
                    'Código' => $bicicleta->codigo,
                    'Tipo' => ucfirst($bicicleta->tipo),
                    'Marca' => $bicicleta->marca,
                    'Modelo' => $bicicleta->modelo,
                    'Año' => $bicicleta->ano_fabricacion,
                    'Estado' => ucfirst($bicicleta->estado),
                    'Estación Actual' => $bicicleta->estacionActual ? $bicicleta->estacionActual->nombre : 'Sin estación',
                    'Nivel Batería' => $bicicleta->nivel_bateria ?: 'N/A',
                    'Kilometraje Total' => $bicicleta->kilometraje_total . ' km',
                    'Total Usos' => $bicicleta->uso_bicicletas_count,
                    'Bloqueada' => $bicicleta->bloqueada ? 'Sí' : 'No',
                ];
            });

        return response()->json($bicicletas);
    }

    private function exportarUso($fechaInicio, $fechaFin)
    {
        $usos = UsoBicicleta::with(['user', 'bicicleta', 'estacionInicio', 'estacionFin'])
            ->whereBetween('fecha_hora_inicio', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->get()
            ->map(function($uso) {
                return [
                    'Fecha' => $uso->fecha_hora_inicio->format('d/m/Y H:i'),
                    'Usuario' => $uso->user->nombre . ' ' . $uso->user->apellido,
                    'Bicicleta' => $uso->bicicleta->codigo,
                    'Tipo Bicicleta' => ucfirst($uso->bicicleta->tipo),
                    'Estación Inicio' => $uso->estacionInicio->nombre,
                    'Estación Fin' => $uso->estacionFin ? $uso->estacionFin->nombre : 'Sin finalizar',
                    'Duración (min)' => $uso->duracion_minutos,
                    'Distancia (km)' => $uso->distancia_recorrida,
                    'CO₂ Reducido (kg)' => $uso->co2_reducido,
                    'Puntos Ganados' => $uso->puntos_verdes_ganados,
                    'Costo Extra (Q)' => $uso->costo_extra,
                ];
            });

        return response()->json($usos);
    }

    private function exportarIngresos($fechaInicio, $fechaFin)
    {
        $membresias = UserMembresia::with(['user', 'membresia'])
            ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'pagado')
            ->get()
            ->map(function($userMembresia) {
                return [
                    'Fecha' => $userMembresia->fecha_inicio->format('d/m/Y'),
                    'Usuario' => $userMembresia->user->nombre . ' ' . $userMembresia->user->apellido,
                    'Membresía' => $userMembresia->membresia->nombre,
                    'Monto (Q)' => $userMembresia->monto_pagado,
                    'Método Pago' => ucfirst($userMembresia->metodo_pago),
                    'Referencia' => $userMembresia->referencia_pago,
                    'Estado' => ucfirst($userMembresia->estado_pago),
                ];
            });

        return response()->json($membresias);
    }
}