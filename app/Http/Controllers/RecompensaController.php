<?php

namespace App\Http\Controllers;

use App\Models\Recompensa;
use App\Models\CanjeRecompensa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecompensaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $recompensas = Recompensa::where('activa', true)
            ->where('stock', '>', 0)
            ->orderBy('puntos_requeridos', 'asc')
            ->get();
        
        $misCanjes = $user->canjesRecompensas()
            ->with('recompensa')
            ->latest()
            ->take(5)
            ->get();
        
        return view('recompensas.index', compact('recompensas', 'misCanjes'));
    }

    public function canjear(Request $request, $id)
    {
        $user = Auth::user();
        $recompensa = Recompensa::findOrFail($id);
        
        // Validaciones
        if (!$recompensa->activa) {
            return back()->with('error', 'Esta recompensa no está disponible.');
        }
        
        if ($recompensa->stock <= 0) {
            return back()->with('error', 'Esta recompensa está agotada.');
        }
        
        if ($user->puntos_verdes < $recompensa->puntos_requeridos) {
            return back()->with('error', 'No tienes suficientes puntos verdes para esta recompensa.');
        }
        
        // Realizar el canje
        $user->decrement('puntos_verdes', $recompensa->puntos_requeridos);
        $recompensa->decrement('stock');
        
        CanjeRecompensa::create([
            'user_id' => $user->id,
            'recompensa_id' => $recompensa->id,
            'puntos_utilizados' => $recompensa->puntos_requeridos,
            'estado' => 'pendiente',
            'fecha_canje' => now(),
        ]);
        
        return back()->with('success', "¡Recompensa canjeada exitosamente! Se descontaron {$recompensa->puntos_requeridos} puntos verdes.");
    }

    public function misCanjes()
    {
        $canjes = Auth::user()->canjesRecompensas()
            ->with('recompensa')
            ->latest()
            ->paginate(10);
        
        return view('recompensas.mis-canjes', compact('canjes'));
    }
}
