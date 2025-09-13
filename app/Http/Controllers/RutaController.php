<?php


namespace App\Http\Controllers;

use App\Models\Ruta;
use App\Models\Estacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RutaController extends Controller
{
    public function index()
    {
        $rutas = Auth::user()->rutas()->with(['estacionInicio', 'estacionFin'])->latest()->get();
        $rutasPublicas = Ruta::with(['user', 'estacionInicio', 'estacionFin'])
            ->where('user_id', '!=', Auth::id())
            ->orderBy('veces_usada', 'desc')
            ->take(10)
            ->get();
        
        return view('rutas.index', compact('rutas', 'rutasPublicas'));
    }

    public function crear()
    {
        $estaciones = Estacion::where('estado', 'activa')->get();
        return view('rutas.crear', compact('estaciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'estacion_inicio_id' => 'required|exists:estaciones,id',
            'estacion_fin_id' => 'required|exists:estaciones,id|different:estacion_inicio_id',
            'puntos_ruta' => 'required|json',
            'distancia_km' => 'required|numeric|min:0.1|max:50',
            'tiempo_estimado_minutos' => 'required|integer|min:1|max:300',
            'dificultad' => 'required|in:facil,moderada,dificil',
        ]);

        $puntosRuta = json_decode($validated['puntos_ruta'], true);
        
        // Calcular CO2 reducido estimado
        $co2Estimado = $validated['distancia_km'] * 0.23; // 0.23 kg CO2 por km

        Ruta::create([
            'user_id' => Auth::id(),
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'estacion_inicio_id' => $validated['estacion_inicio_id'],
            'estacion_fin_id' => $validated['estacion_fin_id'],
            'puntos_ruta' => $puntosRuta,
            'distancia_km' => $validated['distancia_km'],
            'tiempo_estimado_minutos' => $validated['tiempo_estimado_minutos'],
            'dificultad' => $validated['dificultad'],
            'co2_reducido_estimado' => $co2Estimado,
        ]);

        return redirect('/rutas')->with('success', 'Ruta creada exitosamente.');
    }

    public function show($id)
    {
        $ruta = Ruta::with(['user', 'estacionInicio', 'estacionFin', 'usosBicicletas'])
            ->findOrFail($id);
        
        return view('rutas.detalle', compact('ruta'));
    }

    public function toggleFavorita($id)
    {
        $ruta = Auth::user()->rutas()->findOrFail($id);
        $ruta->update(['favorita' => !$ruta->favorita]);
        
        return back()->with('success', $ruta->favorita ? 'Ruta añadida a favoritas.' : 'Ruta removida de favoritas.');
    }

    public function destroy($id)
    {
        $ruta = Auth::user()->rutas()->findOrFail($id);
        $ruta->delete();
        
        return back()->with('success', 'Ruta eliminada correctamente.');
    }
}