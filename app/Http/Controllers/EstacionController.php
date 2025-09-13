<?php

namespace App\Http\Controllers;

use App\Models\Estacion;
use App\Models\Bicicleta;
use Illuminate\Http\Request;

class EstacionController extends Controller
{
    public function index()
    {
        $estaciones = Estacion::with(['bicicletas' => function($query) {
            $query->where('estado', 'disponible');
        }])->where('estado', 'activa')->get();
        
        return view('estaciones.index', compact('estaciones'));
    }

    public function show($id)
    {
        $estacion = Estacion::with([
            'bicicletas' => function($query) {
                $query->where('estado', 'disponible');
            }
        ])->findOrFail($id);
        
        return view('estaciones.show', compact('estacion'));
    }

    public function mapa()
    {
        $estaciones = Estacion::where('estado', 'activa')
            ->withCount(['bicicletas as bicicletas_disponibles' => function($query) {
                $query->where('estado', 'disponible');
            }])
            ->get();
        
        return view('estaciones.mapa', compact('estaciones'));
    }

    public function api()
    {
        $estaciones = Estacion::where('estado', 'activa')
            ->withCount(['bicicletas as bicicletas_disponibles' => function($query) {
                $query->where('estado', 'disponible');
            }])
            ->get();
        
        return response()->json($estaciones);
    }
}