<?php

namespace App\Http\Controllers;

use App\Models\UsoBicicleta;
use App\Models\Bicicleta;
use App\Models\Estacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsoBicicletaController extends Controller
{
    /**
     * Mostrar historial de uso del usuario actual
     */
    public function index()
    {
        $usos = Auth::user()->usosBicicletas()
            ->with(['bicicleta', 'estacionInicio', 'estacionFin', 'ruta'])
            ->latest('fecha_hora_inicio')
            ->paginate(15);

        $estadisticas = $this->obtenerEstadisticasUsuario();

        return view('uso-bicicletas.index', compact('usos', 'estadisticas'));
    }

    /**
     * Mostrar detalles de un uso específico
     */
    public function show($id)
    {
        $uso = Auth::user()->usosBicicletas()
            ->with(['bicicleta', 'estacionInicio', 'estacionFin', 'ruta', 'userMembresia.membresia'])
            ->findOrFail($id);

        return view('uso-bicicletas.show', compact('uso'));
    }

    /**
     * Iniciar un nuevo uso de bicicleta
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bicicleta_id' => 'required|exists:bicicletas,id',
            'estacion_inicio_id' => 'required|exists:estaciones,id',
            'ruta_id' => 'nullable|exists:rutas,id',
        ]);

        $user = Auth::user();
        $bicicleta = Bicicleta::findOrFail($validated['bicicleta_id']);

        // Validaciones de negocio
        $this->validarInicioUso($user, $bicicleta);

        DB::beginTransaction();
        
        try {
            // Crear uso
            $uso = UsoBicicleta::create([
                'user_id' => $user->id,
                'bicicleta_id' => $bicicleta->id,
                'user_membresia_id' => $user->membresiaActiva->id,
                'estacion_inicio_id' => $validated['estacion_inicio_id'],
                'ruta_id' => $validated['ruta_id'],
                'fecha_hora_inicio' => now(),
                'estado' => 'en_curso',
            ]);

            // Actualizar estado de la bicicleta
            $bicicleta->update([
                'estado' => 'en_uso',
                'estacion_actual_id' => null, // Se quita de la estación
            ]);

            // Actualizar capacidad de la estación
            $estacion = Estacion::find($validated['estacion_inicio_id']);
            $estacion->increment('capacidad_disponible');

            DB::commit();

            return $this->successResponse($uso, 'Recorrido iniciado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al iniciar el recorrido: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Finalizar un uso de bicicleta
     */
    public function finalizar(Request $request, $id)
    {
        $validated = $request->validate([
            'estacion_fin_id' => 'required|exists:estaciones,id',
            'distancia_recorrida' => 'nullable|numeric|min:0|max:100',
            'calificacion' => 'nullable|integer|between:1,5',
            'comentarios' => 'nullable|string|max:500',
        ]);

        $uso = Auth::user()->usosBicicletas()
            ->with(['bicicleta', 'userMembresia.membresia'])
            ->where('estado', 'en_curso')
            ->findOrFail($id);

        DB::beginTransaction();

        try {
            $datosFinalizacion = $this->calcularDatosFinalizacion($uso, $validated);
            
            // Actualizar uso
            $uso->update(array_merge($validated, $datosFinalizacion, [
                'fecha_hora_fin' => now(),
                'estado' => 'completado',
            ]));

            // Actualizar bicicleta
            $uso->bicicleta->update([
                'estado' => 'disponible',
                'estacion_actual_id' => $validated['estacion_fin_id'],
                'kilometraje_total' => $uso->bicicleta->kilometraje_total + $datosFinalizacion['distancia_recorrida'],
            ]);

            // Actualizar usuario - asegurar valores positivos
            Auth::user()->increment('puntos_verdes', abs($datosFinalizacion['puntos_verdes_ganados']));
            Auth::user()->increment('co2_reducido_total', abs($datosFinalizacion['co2_reducido']));

            // Actualizar capacidad de estación de destino
            $estacionFin = Estacion::find($validated['estacion_fin_id']);
            $estacionFin->decrement('capacidad_disponible');

            // Incrementar uso de ruta si aplica
            if ($uso->ruta_id) {
                $uso->ruta->incrementarUso();
            }

            DB::commit();

            $mensaje = "¡Recorrido finalizado! Ganaste {$datosFinalizacion['puntos_verdes_ganados']} puntos verdes y redujiste {$datosFinalizacion['co2_reducido']} kg de CO₂.";
            
            return $this->successResponse($uso->fresh(), $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al finalizar el recorrido: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cancelar un uso en curso
     */
    public function cancelar($id)
    {
        $uso = Auth::user()->usosBicicletas()
            ->where('estado', 'en_curso')
            ->findOrFail($id);

        DB::beginTransaction();

        try {
            // Actualizar uso
            $uso->update([
                'estado' => 'cancelado',
                'fecha_hora_fin' => now(),
            ]);

            // Devolver bicicleta a estación original
            $uso->bicicleta->update([
                'estado' => 'disponible',
                'estacion_actual_id' => $uso->estacion_inicio_id,
            ]);

    
            $estacion = Estacion::find($uso->estacion_inicio_id);
            $estacion->decrement('capacidad_disponible');

            DB::commit();

            return $this->successResponse(null, 'Recorrido cancelado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al cancelar el recorrido: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener estadísticas del usuario
     */
    public function estadisticas()
    {
        $estadisticas = $this->obtenerEstadisticasUsuario();
        return $this->successResponse($estadisticas);
    }

    /**
     * Obtener uso actual si existe
     */
    public function usoActual()
    {
        $uso = Auth::user()->usosBicicletas()
            ->with(['bicicleta', 'estacionInicio'])
            ->where('estado', 'en_curso')
            ->first();

        if ($uso) {
            // Calcular tiempo transcurrido
            $uso->tiempo_transcurrido = now()->diffInMinutes($uso->fecha_hora_inicio);
        }

        return $this->successResponse($uso);
    }

    /**
     * Reportes de uso por período
     */
    public function reportes(Request $request)
    {
        $validated = $request->validate([
            'periodo' => 'required|in:dia,semana,mes,ano',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $query = Auth::user()->usosBicicletas()->where('estado', 'completado');

        // Aplicar filtros de fecha
        switch ($validated['periodo']) {
            case 'dia':
                $query->whereDate('fecha_hora_inicio', today());
                break;
            case 'semana':
                $query->whereBetween('fecha_hora_inicio', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
                break;
            case 'mes':
                $query->whereMonth('fecha_hora_inicio', now()->month)
                      ->whereYear('fecha_hora_inicio', now()->year);
                break;
            case 'ano':
                $query->whereYear('fecha_hora_inicio', now()->year);
                break;
        }

        if ($validated['fecha_inicio'] && $validated['fecha_fin']) {
            $query->whereBetween('fecha_hora_inicio', [
                $validated['fecha_inicio'],
                $validated['fecha_fin']
            ]);
        }

        $usos = $query->with(['bicicleta', 'estacionInicio', 'estacionFin'])
                     ->latest('fecha_hora_inicio')
                     ->get();

        $resumen = [
            'total_recorridos' => $usos->count(),
            'total_minutos' => $usos->sum('duracion_minutos'),
            'total_distancia' => $usos->sum('distancia_recorrida'),
            'total_co2_reducido' => $usos->sum('co2_reducido'),
            'total_puntos_ganados' => $usos->sum('puntos_verdes_ganados'),
            'promedio_duracion' => $usos->avg('duracion_minutos'),
            'bicicleta_mas_usada' => $usos->groupBy('bicicleta.codigo')->map->count()->sortDesc()->first(),
        ];

        return $this->successResponse([
            'usos' => $usos,
            'resumen' => $resumen,
            'equivalencias_co2' => $this->obtenerEquivalenciasCO2($resumen['total_co2_reducido'])
        ]);
    }

    /**
     * Validaciones para inicio de uso
     */
    private function validarInicioUso($user, $bicicleta)
    {
        if (!$user->tieneMembresiaActiva()) {
            throw new \Exception('Necesitas una membresía activa para usar las bicicletas.');
        }

        if (!$bicicleta->estaDisponible()) {
            throw new \Exception('La bicicleta no está disponible en este momento.');
        }

        if (!$user->puedeUsarBicicleta($bicicleta->tipo)) {
            throw new \Exception('Tu membresía no incluye este tipo de bicicleta.');
        }

        $usoEnCurso = $user->usosBicicletas()->where('estado', 'en_curso')->exists();
        if ($usoEnCurso) {
            throw new \Exception('Ya tienes un recorrido en curso. Finalízalo antes de iniciar otro.');
        }
    }

    /**
     * Calcular datos de finalización del uso
     */
    private function calcularDatosFinalizacion($uso, $validated)
    {
        $fechaFin = now();
        $duracionMinutos = $fechaFin->diffInMinutes($uso->fecha_hora_inicio);
        
        // Calcular distancia si no se proporcionó
        $distancia = $validated['distancia_recorrida'] ?? $this->estimarDistancia($duracionMinutos);
        
        // Calcular minutos de membresía utilizados
        $membresia = $uso->userMembresia->membresia;
        $minutosRestantes = $uso->userMembresia->minutosRestantes();
        
        $minutosIncluidos = min($duracionMinutos, $minutosRestantes);
        $minutosExtra = max(0, $duracionMinutos - $minutosRestantes);
        $costoExtra = $minutosExtra * $membresia->tarifa_minuto_extra;
        
        // Calcular CO₂ y puntos
        $co2Reducido = $this->calcularCO2($distancia);
        $puntosVerdes = $this->calcularPuntosVerdes($co2Reducido, $uso->bicicleta->tipo);
        
        // Bonus por membresía premium
        if ($membresia->tipo_bicicleta === 'ambas') {
            $puntosVerdes = floor($puntosVerdes * 2);
        }

        return [
            'duracion_minutos' => $duracionMinutos,
            'distancia_recorrida' => $distancia,
            'minutos_incluidos_usados' => $minutosIncluidos,
            'minutos_extra' => $minutosExtra,
            'costo_extra' => $costoExtra,
            'co2_reducido' => $co2Reducido,
            'puntos_verdes_ganados' => $puntosVerdes,
        ];
    }

    /**
     * Estimar distancia basada en duración (15 km/h promedio)
     */
    private function estimarDistancia($duracionMinutos)
    {
        return round(($duracionMinutos / 60) * 15, 2);
    }

    /**
     * Obtener estadísticas del usuario
     */
    private function obtenerEstadisticasUsuario()
    {
        $user = Auth::user();
        
        $usosCompletados = $user->usosBicicletas()->where('estado', 'completado');
        
        return [
            'total_recorridos' => $usosCompletados->count(),
            'tiempo_total_minutos' => $usosCompletados->sum('duracion_minutos'),
            'distancia_total' => $usosCompletados->sum('distancia_recorrida'),
            'co2_reducido_total' => $user->co2_reducido_total,
            'puntos_verdes' => $user->puntos_verdes,
            'recorrido_mas_largo' => $usosCompletados->max('distancia_recorrida'),
            'tiempo_promedio' => $usosCompletados->avg('duracion_minutos'),
            'bicicleta_favorita' => $usosCompletados->with('bicicleta')
                ->get()
                ->groupBy('bicicleta.codigo')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),
        ];
    }
}