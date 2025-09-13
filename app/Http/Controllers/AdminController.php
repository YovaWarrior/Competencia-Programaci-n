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
            'reportes_pendientes' => 0, // ReporteDano::where('estado', 'reportado')->count(),
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
            ->with([
                'membresiaActiva.membresia',
                'membresias' => function($query) {
                    $query->latest()->limit(1);
                },
                'usosBicicletas' => function($query) {
                    $query->latest()->limit(5);
                },
                // 'reportesDanos' => function($query) {
                //     $query->latest()->limit(3);
                // }
            ])
            ->withCount([
                'usosBicicletas',
                // 'reportesDanos',
                'membresias'
            ])
            ->latest()
            ->paginate(20);
        
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function catalogoUsuarios()
    {
        $usuarios = User::where('rol', 'usuario')
            ->with([
                'membresiaActiva.membresia',
                'membresias.membresia',
                'usosBicicletas' => function($query) {
                    $query->where('estado', 'completado')->latest();
                },
                // 'reportesDanos'
            ])
            ->withCount([
                'usosBicicletas as total_usos' => function($query) {
                    $query->where('estado', 'completado');
                },
                // 'reportesDanos as total_reportes',
                'membresias as total_membresias'
            ])
            ->withSum('usosBicicletas', 'co2_reducido')
            ->withSum('usosBicicletas', 'distancia_recorrida')
            ->withSum('usosBicicletas', 'duracion_minutos')
            ->latest()
            ->paginate(15);

        $estadisticas = [
            'total_usuarios' => User::where('rol', 'usuario')->count(),
            'usuarios_activos' => User::where('rol', 'usuario')->where('activo', true)->count(),
            'usuarios_con_membresia' => User::where('rol', 'usuario')
                ->whereHas('membresiaActiva', function($query) {
                    $query->where('activa', true)->where('fecha_fin', '>', now());
                })->count(),
            'promedio_edad' => User::where('rol', 'usuario')
                ->whereNotNull('fecha_nacimiento')
                ->selectRaw('AVG(YEAR(CURDATE()) - YEAR(fecha_nacimiento)) as promedio')
                ->value('promedio'),
        ];
        
        return view('admin.usuarios.catalogo', compact('usuarios', 'estadisticas'));
    }

    public function usuarioDetalle($id)
    {
        $usuario = User::with([
            'membresias.membresia',
            'usosBicicletas.bicicleta',
            'reportesDanos.bicicleta'
        ])->findOrFail($id);
        
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function suspenderUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        
        $usuario->update([
            'activo' => false,
            'motivo_suspension' => $request->input('motivo')
        ]);

        return back()->with('success', 'Usuario suspendido correctamente.');
    }

    public function activarUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        
        $usuario->update([
            'activo' => true,
            'motivo_suspension' => null
        ]);

        return back()->with('success', 'Usuario activado correctamente.');
    }

    public function bicicletas()
    {
        $bicicletas = Bicicleta::with(['estacionActual', 'reportesDanos' => function($query) {
            $query->where('estado', 'reportado');
        }])
        ->withCount('usosBicicletas')
        ->latest()
        ->paginate(20);
        
        $totalBicicletas = Bicicleta::count();
        $bicicletasDisponibles = Bicicleta::where('estado', 'disponible')->count();
        $bicicletasEnUso = Bicicleta::where('estado', 'en_uso')->count();
        $bicicletasMantenimiento = Bicicleta::where('estado', 'mantenimiento')->count();
        $estaciones = Estacion::all();

        return view('admin.bicicletas.index', compact(
            'bicicletas', 
            'totalBicicletas', 
            'bicicletasDisponibles', 
            'bicicletasEnUso', 
            'bicicletasMantenimiento',
            'estaciones'
        ));
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
        ])
        ->when(request('estado'), function($query, $estado) {
            return $query->where('estado', $estado);
        })
        ->when(request('buscar'), function($query, $buscar) {
            return $query->where('nombre', 'like', "%{$buscar}%")
                         ->orWhere('direccion', 'like', "%{$buscar}%");
        })
        ->when(request('capacidad_min'), function($query, $capacidad) {
            return $query->where('capacidad_total', '>=', $capacidad);
        })
        ->paginate(20);
        
        $totalEstaciones = Estacion::count();
        $estacionesActivas = Estacion::where('estado', 'activa')->count();
        $estacionesMantenimiento = Estacion::where('estado', 'mantenimiento')->count();
        $capacidadTotal = Estacion::sum('capacidad_total');

        return view('admin.estaciones.index', compact(
            'estaciones', 
            'totalEstaciones', 
            'estacionesActivas', 
            'estacionesMantenimiento', 
            'capacidadTotal'
        ));
    }

    public function reportesDanos()
    {
        $reportes = ReporteDano::with(['user', 'bicicleta'])
            ->when(request('estado'), function($query, $estado) {
                return $query->where('estado', $estado);
            })
            ->when(request('prioridad'), function($query, $prioridad) {
                return $query->where('prioridad', $prioridad);
            })
            ->when(request('fecha_desde'), function($query, $fecha) {
                return $query->whereDate('created_at', '>=', $fecha);
            })
            ->latest()
            ->paginate(20);

        $reportesPendientes = ReporteDano::where('estado', 'pendiente')->count();
        $reportesRevision = ReporteDano::where('estado', 'revision')->count();
        $reportesResueltos = ReporteDano::where('estado', 'resuelto')->count();
        $totalReportes = ReporteDano::count();

        return view('admin.reportes-danos.index', compact(
            'reportes', 
            'reportesPendientes', 
            'reportesRevision', 
            'reportesResueltos', 
            'totalReportes'
        ));
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

    public function exportarReporte($tipo)
    {
        $fechaInicio = request('fecha_inicio');
        $fechaFin = request('fecha_fin');
        
        // Debug: Log para verificar datos
        \Log::info('Exportando reporte', [
            'tipo' => $tipo,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'request_all' => request()->all()
        ]);
        
        switch ($tipo) {
            case 'usuarios':
                return $this->exportarUsuariosPDF();
            case 'bicicletas':
                return $this->exportarBicicletasPDF();
            case 'uso':
                return $this->exportarUsoPDF($fechaInicio, $fechaFin);
            case 'ingresos':
                return $this->exportarIngresosPDF($fechaInicio, $fechaFin);
            case 'co2':
                return $this->exportarCo2PDF($fechaInicio, $fechaFin);
            case 'usuarios-activos':
                return $this->exportarUsuariosActivosPDF();
            case 'bicicletas-populares':
                return $this->exportarBicicletasPopularesPDF();
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

    public function reportesIndex()
    {
        $stats = [
            'total_usos' => UsoBicicleta::where('estado', 'completado')->count(),
            'usuarios_activos' => User::where('rol', 'usuario')->where('activo', true)->count(),
            'ingresos_totales' => UserMembresia::where('estado_pago', 'pagado')->sum('monto_pagado'),
            'co2_total' => UsoBicicleta::where('estado', 'completado')->sum('co2_reducido'),
        ];

        return view('admin.reportes.index', compact('stats'));
    }

    public function reporteUso()
    {
        $usos = UsoBicicleta::with(['user', 'bicicleta', 'estacionInicio', 'estacionFin'])
            ->where('estado', 'completado')
            ->latest()
            ->paginate(20);

        return view('admin.reportes.uso', compact('usos'));
    }

    public function reporteIngresos()
    {
        $ingresos = UserMembresia::with(['user', 'membresia'])
            ->where('estado_pago', 'pagado')
            ->latest()
            ->paginate(20);

        return view('admin.reportes.ingresos', compact('ingresos'));
    }

    public function reporteCo2()
    {
        $reporteCo2 = UsoBicicleta::with(['user', 'bicicleta'])
            ->where('estado', 'completado')
            ->where('co2_reducido', '>', 0)
            ->latest()
            ->paginate(20);

        return view('admin.reportes.co2', compact('reporteCo2'));
    }

    public function reporteUsuariosActivos()
    {
        $usuariosActivos = User::where('rol', 'usuario')
            ->where('activo', true)
            ->whereHas('membresiaActiva', function($query) {
                $query->where('activa', true)->where('fecha_fin', '>', now());
            })
            ->with([
                'membresiaActiva.membresia',
                'usosBicicletas' => function($query) {
                    $query->where('estado', 'completado')->latest()->limit(5);
                }
            ])
            ->withCount([
                'usosBicicletas as total_usos' => function($query) {
                    $query->where('estado', 'completado');
                }
            ])
            ->withSum('usosBicicletas', 'co2_reducido')
            ->withSum('usosBicicletas', 'duracion_minutos')
            ->orderBy('total_usos', 'desc')
            ->paginate(20);

        $estadisticas = [
            'total_usuarios_activos' => $usuariosActivos->total(),
            'promedio_usos' => $usuariosActivos->avg('total_usos') ?? 0,
            'total_co2_reducido' => $usuariosActivos->sum('uso_bicicletas_sum_co2_reducido') ?? 0,
            'total_minutos' => $usuariosActivos->sum('uso_bicicletas_sum_duracion_minutos') ?? 0,
        ];

        return view('admin.reportes.usuarios-activos', compact('usuariosActivos', 'estadisticas'));
    }

    public function reporteBicicletasPopulares()
    {
        // Obtener estadísticas usando consultas separadas para evitar conflictos
        $bicicletasConUsos = \DB::table('uso_bicicletas')
            ->select([
                'bicicleta_id',
                \DB::raw('COUNT(*) as total_usos'),
                \DB::raw('SUM(duracion_minutos) as total_duracion'),
                \DB::raw('SUM(distancia_recorrida) as total_distancia'),
                \DB::raw('SUM(co2_reducido) as total_co2'),
                \DB::raw('AVG(calificacion) as promedio_calificacion')
            ])
            ->where('estado', 'completado')
            ->groupBy('bicicleta_id')
            ->having('total_usos', '>', 0)
            ->orderBy('total_usos', 'desc')
            ->get()
            ->keyBy('bicicleta_id');

        // Obtener las bicicletas con sus relaciones
        $bicicletaIds = $bicicletasConUsos->pluck('bicicleta_id')->toArray();
        
        if (empty($bicicletaIds)) {
            $bicicletasPopulares = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                20,
                1,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        } else {
            $bicicletas = Bicicleta::with(['estacionActual'])
                ->whereIn('id', $bicicletaIds)
                ->get()
                ->map(function($bicicleta) use ($bicicletasConUsos) {
                    $stats = $bicicletasConUsos->get($bicicleta->id);
                    $bicicleta->total_usos = $stats->total_usos ?? 0;
                    $bicicleta->total_duracion = $stats->total_duracion ?? 0;
                    $bicicleta->total_distancia = $stats->total_distancia ?? 0;
                    $bicicleta->total_co2 = $stats->total_co2 ?? 0;
                    $bicicleta->promedio_calificacion = $stats->promedio_calificacion ?? null;
                    return $bicicleta;
                })
                ->sortByDesc('total_usos');

            // Paginar manualmente
            $page = request()->get('page', 1);
            $perPage = 20;
            $offset = ($page - 1) * $perPage;
            
            $bicicletasPopulares = new \Illuminate\Pagination\LengthAwarePaginator(
                $bicicletas->slice($offset, $perPage)->values(),
                $bicicletas->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        }

        $estadisticas = [
            'total_bicicletas_usadas' => $bicicletasConUsos->count(),
            'promedio_usos' => $bicicletasConUsos->avg('total_usos') ?? 0,
            'bicicleta_mas_popular' => $bicicletasPopulares->first(),
            'total_distancia' => $bicicletasConUsos->sum('total_distancia') ?? 0,
        ];

        return view('admin.reportes.bicicletas-populares', compact('bicicletasPopulares', 'estadisticas'));
    }

    // Métodos de exportación PDF
    private function exportarCo2PDF($fechaInicio, $fechaFin)
    {
        // Si no hay fechas, usar rango por defecto
        if (!$fechaInicio) $fechaInicio = now()->subMonth()->format('Y-m-d');
        if (!$fechaFin) $fechaFin = now()->format('Y-m-d');
        
        $query = UsoBicicleta::with(['user', 'bicicleta']);
        
        $query->whereBetween('fecha_hora_inicio', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        
        if (request('usuario')) {
            $query->whereHas('user', function($q) {
                $q->where('nombre', 'like', '%' . request('usuario') . '%')
                  ->orWhere('apellido', 'like', '%' . request('usuario') . '%');
            });
        }
        
        $reporteCo2 = $query->where('estado', 'completado')
            ->where('co2_reducido', '>', 0)
            ->orderBy('fecha_hora_inicio', 'desc')
            ->get();

        $html = view('admin.pdf.co2', compact('reporteCo2', 'fechaInicio', 'fechaFin'))->render();
        return $this->generarPDF($html, 'reporte-co2.pdf');
    }
    private function exportarUsuariosPDF()
    {
        $usuarios = User::where('rol', 'usuario')
            ->with('membresiaActiva.membresia')
            ->withCount('usosBicicletas')
            ->withSum('usosBicicletas', 'co2_reducido')
            ->get();

        $html = view('admin.pdf.usuarios', compact('usuarios'))->render();
        return $this->generarPDF($html, 'catalogo-usuarios.pdf');
    }

    private function exportarBicicletasPDF()
    {
        $bicicletas = Bicicleta::with('estacionActual')
            ->withCount('usosBicicletas')
            ->get();

        $html = view('admin.pdf.bicicletas', compact('bicicletas'))->render();
        return $this->generarPDF($html, 'catalogo-bicicletas.pdf');
    }

    private function exportarUsoPDF($fechaInicio, $fechaFin)
    {
        // Si no hay fechas, usar rango por defecto
        if (!$fechaInicio) $fechaInicio = now()->subMonth()->format('Y-m-d');
        if (!$fechaFin) $fechaFin = now()->format('Y-m-d');
        
        $query = UsoBicicleta::with(['user', 'bicicleta', 'estacionInicio', 'estacionFin']);
        
        $query->whereBetween('fecha_hora_inicio', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        
        if (request('usuario')) {
            $query->whereHas('user', function($q) {
                $q->where('nombre', 'like', '%' . request('usuario') . '%')
                  ->orWhere('apellido', 'like', '%' . request('usuario') . '%')
                  ->orWhere('email', 'like', '%' . request('usuario') . '%');
            });
        }
        
        $usos = $query->orderBy('fecha_hora_inicio', 'desc')->get();

        $html = view('admin.pdf.uso', compact('usos', 'fechaInicio', 'fechaFin'))->render();
        return $this->generarPDF($html, 'reporte-uso.pdf');
    }

    private function exportarIngresosPDF($fechaInicio, $fechaFin)
    {
        // Si no hay fechas, usar rango por defecto
        if (!$fechaInicio) $fechaInicio = now()->subMonth()->format('Y-m-d');
        if (!$fechaFin) $fechaFin = now()->format('Y-m-d');
        
        $query = UserMembresia::with(['user', 'membresia']);
        
        $query->whereBetween('fecha_inicio', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        
        if (request('metodo_pago')) {
            $query->where('metodo_pago', request('metodo_pago'));
        }
        
        $ingresos = $query->where('estado_pago', 'pagado')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $html = view('admin.pdf.ingresos', compact('ingresos', 'fechaInicio', 'fechaFin'))->render();
        return $this->generarPDF($html, 'reporte-ingresos.pdf');
    }

    private function exportarUsuariosActivosPDF()
    {
        $usuariosActivos = User::where('rol', 'usuario')
            ->where('activo', true)
            ->whereHas('membresiaActiva')
            ->with('membresiaActiva.membresia')
            ->withCount('usosBicicletas')
            ->withSum('usosBicicletas', 'co2_reducido')
            ->get();

        $html = view('admin.pdf.usuarios-activos', compact('usuariosActivos'))->render();
        return $this->generarPDF($html, 'usuarios-activos.pdf');
    }

    private function exportarBicicletasPopularesPDF()
    {
        $bicicletasPopulares = Bicicleta::with('estacionActual')
            ->withCount([
                'usosBicicletas as total_usos' => function($query) {
                    $query->where('estado', 'completado');
                }
            ])
            ->withSum('usosBicicletas', 'distancia_recorrida')
            ->withAvg('usosBicicletas', 'calificacion')
            ->having('total_usos', '>', 0)
            ->orderBy('total_usos', 'desc')
            ->get();

        $html = view('admin.pdf.bicicletas-populares', compact('bicicletasPopulares'))->render();
        return $this->generarPDF($html, 'bicicletas-populares.pdf');
    }

    private function generarPDF($html, $filename)
    {
        // Generar HTML optimizado para descarga directa
        $headers = [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . str_replace('.pdf', '.html', $filename) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        // Optimizar HTML para impresión como PDF
        $optimizedHtml = str_replace(
            '<style>',
            '<style>@media print { body { -webkit-print-color-adjust: exact; } @page { margin: 1cm; } } ',
            $html
        );
        
        return response($optimizedHtml, 200, $headers);
    }
}