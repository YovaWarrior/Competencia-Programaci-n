<?php

namespace App\Http\Controllers;

use App\Models\ReporteDano;
use App\Models\Bicicleta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteDanoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $reportes = ReporteDano::with(['bicicleta', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reportes.index', compact('reportes'));
    }

    public function create()
    {
        $bicicletas = Bicicleta::where('estado', 'disponible')->get();
        
        return view('reportes.create', compact('bicicletas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bicicleta_id' => 'required|exists:bicicletas,id',
            'tipo_dano' => 'required|string|max:255',
            'descripcion' => 'required|string|max:500',
            'severidad' => 'required|in:leve,moderado,severo'
        ]);

        ReporteDano::create([
            'user_id' => Auth::id(),
            'bicicleta_id' => $request->bicicleta_id,
            'tipo_dano' => $request->tipo_dano,
            'descripcion' => $request->descripcion,
            'severidad' => $request->severidad,
            'estado' => 'reportado',
            'fecha_reporte' => now()
        ]);

        return redirect()->route('reportes.index')
            ->with('success', 'Reporte enviado correctamente. Gracias por ayudarnos a mantener las bicicletas en buen estado.');
    }

    public function show(ReporteDano $reporte)
    {
        if ($reporte->user_id !== Auth::id()) {
            abort(403);
        }

        return view('reportes.show', compact('reporte'));
    }
}
