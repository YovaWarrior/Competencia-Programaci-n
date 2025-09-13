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
    // Validación base
    $baseRules = [
        'metodo_pago' => 'required|in:tarjeta,efectivo,transferencia',
    ];

    // Validaciones específicas por método de pago
    $additionalRules = [];
    
    switch ($request->metodo_pago) {
        case 'tarjeta':
            $additionalRules = [
                'numero_tarjeta' => 'required|string|regex:/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/',
                'cvv' => 'required|string|regex:/^\d{3,4}$/',
                'nombre_titular' => 'required|string|min:3|max:100',
                'fecha_expiracion' => 'required|string|regex:/^\d{2}\/\d{2}$/',
            ];
            break;
            
        case 'transferencia':
            $additionalRules = [
                'comprobante_transferencia' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
                'numero_referencia_transferencia' => 'required|string|min:5|max:50',
                'banco_origen' => 'required|string|max:100',
            ];
            break;
            
        case 'efectivo':
            $additionalRules = [
                'comprobante_deposito' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
                'numero_boleta' => 'required|string|min:5|max:50',
                'fecha_deposito' => 'required|date|before_or_equal:today',
                'sucursal' => 'required|string|max:100',
            ];
            break;
    }

    // Combinar todas las validaciones
    $allRules = array_merge($baseRules, $additionalRules);
    
    // Validar los datos
    $validated = $request->validate($allRules, [
        // Mensajes personalizados de error
        'numero_tarjeta.regex' => 'El formato del número de tarjeta no es válido. Use: 1234 5678 9012 3456',
        'cvv.regex' => 'El CVV debe tener 3 o 4 dígitos numéricos',
        'fecha_expiracion.regex' => 'La fecha de expiración debe tener el formato MM/AA',
        'comprobante_transferencia.required' => 'Debe subir el comprobante de transferencia',
        'comprobante_transferencia.mimes' => 'El comprobante debe ser una imagen (JPG, PNG) o PDF',
        'comprobante_transferencia.max' => 'El comprobante no puede exceder 5MB',
        'comprobante_deposito.required' => 'Debe subir el comprobante de depósito',
        'comprobante_deposito.mimes' => 'El comprobante debe ser una imagen (JPG, PNG) o PDF',
        'comprobante_deposito.max' => 'El comprobante no puede exceder 5MB',
        'fecha_deposito.before_or_equal' => 'La fecha de depósito no puede ser futura',
    ]);

    // Validación adicional para fecha de expiración de tarjeta
    if ($request->metodo_pago === 'tarjeta' && isset($validated['fecha_expiracion'])) {
        $fechaExp = $validated['fecha_expiracion'];
        list($mes, $año) = explode('/', $fechaExp);
        
        $fechaExpiracion = Carbon::createFromDate(2000 + (int)$año, (int)$mes, 1)->endOfMonth();
        
        if ($fechaExpiracion->isPast()) {
            return back()->withErrors(['fecha_expiracion' => 'La tarjeta está expirada'])
                        ->withInput();
        }
    }

    $user = Auth::user();
    $membresia = Membresia::findOrFail($membresiaId);

    // Generar referencia de pago según el método
    $referenciaPago = $this->generarReferenciaPago($request->metodo_pago, $validated);

    // Manejar archivos subidos
    $rutaComprobante = null;
    if ($request->hasFile('comprobante_transferencia')) {
        $rutaComprobante = $request->file('comprobante_transferencia')
                                 ->store('comprobantes/transferencias', 'public');
    } elseif ($request->hasFile('comprobante_deposito')) {
        $rutaComprobante = $request->file('comprobante_deposito')
                                 ->store('comprobantes/depositos', 'public');
    }

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
        'estado_pago' => $request->metodo_pago === 'tarjeta' ? 'pagado' : 'pendiente',
        'metodo_pago' => $validated['metodo_pago'],
        'referencia_pago' => $referenciaPago,
        'comprobante_pago' => $rutaComprobante,
        'activa' => $request->metodo_pago === 'tarjeta' ? true : false, // Solo tarjeta se activa inmediatamente
    ]);

    $mensaje = $request->metodo_pago === 'tarjeta' 
                ? '¡Membresía activada exitosamente!' 
                : '¡Pago registrado! Tu membresía será activada una vez confirmemos el pago.';

    return redirect('/dashboard')->with('success', $mensaje);
}

/**
 * Generar referencia de pago según el método
 */
private function generarReferenciaPago($metodoPago, $validated)
{
    switch ($metodoPago) {
        case 'tarjeta':
            // Para tarjeta, usar últimos 4 dígitos + timestamp
            $ultimosDigitos = substr(str_replace(' ', '', $validated['numero_tarjeta']), -4);
            return 'CARD-' . $ultimosDigitos . '-' . time();
            
        case 'transferencia':
            return 'TRANS-' . $validated['numero_referencia_transferencia'];
            
        case 'efectivo':
            return 'DEP-' . $validated['numero_boleta'];
            
        default:
            return 'REF-' . time();
    }
}
    public function historial()
    {
        $user = Auth::user();
        $membresias = $user->membresias()->with('membresia')->latest()->paginate(10);
        
        return view('membresias.historial', compact('membresias'));
    }
}