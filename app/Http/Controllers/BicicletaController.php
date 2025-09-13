<?php


namespace App\Http\Controllers;

use App\Models\Bicicleta;
use App\Models\Estacion;
use App\Models\UsoBicicleta;
use App\Models\ReporteDano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BicicletaController extends Controller
{
    public function seleccionar()
    {
        $user = Auth::user();
        
        if (!$user->tieneMembresiaActiva()) {
            return redirect('/membresias')->with('error', 'Necesitas una membresía activa para usar las bicicletas.');
        }

        // Verificar si ya tiene un recorrido en curso
        $usoEnCurso = $user->usosBicicletas()->where('estado', 'en_curso')->first();
        if ($usoEnCurso) {
            return redirect('/bicicletas/usar/' . $usoEnCurso->id)->with('info', 'Ya tienes un recorrido en curso.');
        }

        $membresia = $user->membresiaActiva;
        $tipoBicicleta = $membresia->membresia->tipo_bicicleta;
        
        $bicicletas = Bicicleta::with('estacionActual')
            ->where('estado', 'disponible')
            ->where('bloqueada', false)
            ->when($tipoBicicleta !== 'ambas', function($query) use ($tipoBicicleta) {
                return $query->where('tipo', $tipoBicicleta);
            })
            ->get()
            ->groupBy(function($bicicleta) {
                return $bicicleta->estacionActual ? $bicicleta->estacionActual->nombre : 'Sin Estación';
            });
        
        return view('bicicletas.seleccionar', compact('bicicletas', 'tipoBicicleta'));
    }

    public function usar(Request $request, $bicicletaId)
    {
        $user = Auth::user();
        $bicicleta = Bicicleta::findOrFail($bicicletaId);
        
        // Validaciones
        if (!$user->tieneMembresiaActiva()) {
            return back()->with('error', 'Necesitas una membresía activa.');
        }

        if (!$bicicleta->estaDisponible()) {
            return back()->with('error', 'La bicicleta no está disponible.');
        }

        if (!$user->puedeUsarBicicleta($bicicleta->tipo)) {
            return back()->with('error', 'Tu membresía no incluye este tipo de bicicleta.');
        }

        // Verificar si ya tiene un uso en curso
        $usoEnCurso = $user->usosBicicletas()->where('estado', 'en_curso')->first();
        if ($usoEnCurso) {
            return back()->with('error', 'Ya tienes un recorrido en curso.');
        }

        // Iniciar uso
        $uso = UsoBicicleta::create([
            'user_id' => $user->id,
            'bicicleta_id' => $bicicleta->id,
            'user_membresia_id' => $user->membresiaActiva->id,
            'estacion_inicio_id' => $bicicleta->estacion_actual_id,
            'fecha_hora_inicio' => now(),
            'estado' => 'en_curso',
        ]);

        // Cambiar estado de la bicicleta
        $bicicleta->update(['estado' => 'en_uso']);

        return redirect('/bicicletas/usar/' . $uso->id)->with('success', 'Recorrido iniciado. ¡Disfruta tu viaje!');
    }

    public function mostrarUso($usoId)
    {
        $uso = UsoBicicleta::with(['bicicleta', 'estacionInicio'])
            ->where('user_id', Auth::id())
            ->where('estado', 'en_curso')
            ->findOrFail($usoId);
        
        $estaciones = Estacion::where('estado', 'activa')->get();
        
        return view('bicicletas.mostrar-uso', compact('uso', 'estaciones'));
    }

    public function finalizarUso(Request $request, $usoId)
    {
        $validated = $request->validate([
            'estacion_fin_id' => 'required|exists:estaciones,id',
            'ruta_id' => 'nullable|exists:rutas,id',
            'calificacion' => 'nullable|integer|between:1,5',
            'comentarios' => 'nullable|string|max:500',
        ]);

        $uso = UsoBicicleta::with(['bicicleta', 'userMembresia.membresia'])
            ->where('user_id', Auth::id())
            ->where('estado', 'en_curso')
            ->findOrFail($usoId);

        $fechaFin = now();
        $duracionMinutos = $fechaFin->diffInMinutes($uso->fecha_hora_inicio);
        
        // Calcular costos y CO2
        $membresia = $uso->userMembresia->membresia;
        $minutosRestantes = $uso->userMembresia->minutosRestantes();
        
        $minutosIncluidos = min($duracionMinutos, $minutosRestantes);
        $minutosExtra = max(0, $duracionMinutos - $minutosRestantes);
        $costoExtra = $minutosExtra * $membresia->tarifa_minuto_extra;
        
        // Estimación de CO2 reducido (0.23 kg CO2 por km, estimando 15 km/h promedio)
        $distanciaEstimada = abs(($duracionMinutos / 60) * 15); // km
        $co2Reducido = abs($distanciaEstimada * 0.23);
        
        // Puntos verdes (1 punto por cada 0.1 kg de CO2)
        $puntosGanados = abs(floor($co2Reducido * 10));
        if ($uso->bicicleta->tipo === 'electrica') {
            $puntosGanados = abs(floor($puntosGanados * 1.5));
        }
        
        // Asegurar que los puntos sean siempre positivos
        $puntosGanados = max(1, $puntosGanados);

        // Actualizar uso
        $uso->update([
            'fecha_hora_fin' => $fechaFin,
            'estacion_fin_id' => $validated['estacion_fin_id'],
            'ruta_id' => $validated['ruta_id'] ?? null,
            'duracion_minutos' => $duracionMinutos,
            'minutos_incluidos_usados' => $minutosIncluidos,
            'minutos_extra' => $minutosExtra,
            'costo_extra' => $costoExtra,
            'distancia_recorrida' => $distanciaEstimada,
            'co2_reducido' => $co2Reducido,
            'puntos_verdes_ganados' => $puntosGanados,
            'estado' => 'completado',
            'calificacion' => $validated['calificacion'] ?? null,
            'comentarios' => $validated['comentarios'] ?? null,
        ]);

        // Actualizar usuario
        Auth::user()->increment('puntos_verdes', $puntosGanados);
        Auth::user()->increment('co2_reducido_total', $co2Reducido);

        // Actualizar bicicleta
        $uso->bicicleta->update([
            'estado' => 'disponible',
            'estacion_actual_id' => $validated['estacion_fin_id'],
            'kilometraje_total' => $uso->bicicleta->kilometraje_total + $distanciaEstimada,
        ]);

        // Incrementar uso de ruta si se especificó
        if (!empty($validated['ruta_id'])) {
            \App\Models\Ruta::find($validated['ruta_id'])->incrementarUso();
        }

        return redirect('/dashboard')->with('success', "¡Recorrido finalizado! Ganaste {$puntosGanados} puntos verdes.");
    }

    public function recorridoActual()
    {
        $user = Auth::user();
        $usoEnCurso = $user->usosBicicletas()
            ->with(['bicicleta', 'estacionInicio', 'userMembresia.membresia'])
            ->where('estado', 'en_curso')
            ->first();
        
        return view('bicicletas.recorrido-actual', compact('usoEnCurso'));
    }

    public function historial()
    {
        $usos = Auth::user()->usosBicicletas()
            ->with(['bicicleta', 'estacionInicio', 'estacionFin', 'ruta'])
            ->where('estado', 'completado')
            ->latest()
            ->paginate(10);
        
        return view('bicicletas.historial', compact('usos'));
    }

    public function reportarDano($bicicletaId)
    {
        $bicicleta = Bicicleta::findOrFail($bicicletaId);
        return view('bicicletas.reportar-dano', compact('bicicleta'));
    }

    public function guardarReporteDano(Request $request, $bicicletaId)
    {
        $validated = $request->validate([
            'tipo_dano' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'severidad' => 'required|in:leve,moderado,severo',
            'fotos.*' => 'nullable|image|max:2048',
        ]);

        $bicicleta = Bicicleta::findOrFail($bicicletaId);
        
        $fotos = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $fotos[] = $foto->store('reportes', 'public');
            }
        }

        ReporteDano::create([
            'user_id' => Auth::id(),
            'bicicleta_id' => $bicicleta->id,
            'tipo_dano' => $validated['tipo_dano'],
            'descripcion' => $validated['descripcion'],
            'severidad' => $validated['severidad'],
            'fotos' => $fotos,
            'estado' => 'reportado',
            'fecha_reporte' => now(),
        ]);

        // Si es severo, bloquear la bicicleta
        if ($validated['severidad'] === 'severo') {
            $bicicleta->update([
                'bloqueada' => true,
                'motivo_bloqueo' => 'Daño severo reportado: ' . $validated['tipo_dano'],
                'estado' => 'danada',
            ]);
        }

        return redirect('/dashboard')->with('success', 'Reporte de daño enviado. ¡Gracias por mantener EcoBici seguro!');
    }
}