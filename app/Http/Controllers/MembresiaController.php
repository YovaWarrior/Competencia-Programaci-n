<?php


namespace App\Http\Controllers;

use App\Models\Membresia;
use App\Models\UserMembresia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MembresiaController extends Controller
{
    public function index()
    {
        $membresias = Membresia::where('activa', true)->get();
        $membresiaActual = Auth::user()->membresiaActiva;
        
        return view('membresias.index', compact('membresias', 'membresiaActual'));
    }

    public function pago($membresiaId)
    {
        $membresia = Membresia::findOrFail($membresiaId);
        return view('membresias.pago', compact('membresia'));
    }

    public function procesarPago(Request $request, $membresiaId)
    {
        $validated = $request->validate([
            'metodo_pago' => 'required|in:tarjeta,efectivo,transferencia',
            'referencia_pago' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $membresia = Membresia::findOrFail($membresiaId);

        // Desactivar membresía anterior si existe
        $user->membresias()->where('activa', true)->update(['activa' => false]);

        // Crear nueva membresía
        $fechaInicio = Carbon::now();
        $fechaFin = $fechaInicio->copy()->addDays($membresia->duracion_dias);

        UserMembresia::create([
            'user_id' => $user->id,
            'membresia_id' => $membresia->id,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'monto_pagado' => $membresia->precio,
            'estado_pago' => 'pagado', // En producción sería 'pendiente'
            'metodo_pago' => $validated['metodo_pago'],
            'referencia_pago' => $validated['referencia_pago'],
            'activa' => true,
        ]);

        return redirect('/dashboard')->with('success', '¡Membresía activada exitosamente!');
    }

    public function historial()
    {
        $user = Auth::user();
        $membresias = $user->membresias()->with('membresia')->latest()->paginate(10);
        
        return view('membresias.historial', compact('membresias'));
    }
}