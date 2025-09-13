{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - EcoBici')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        pointer-events: none;
    }
    
    .stat-card:hover {
        transform: translateY(-10px) scale(1.02);
    }
    
    .progress-ring {
        width: 120px;
        height: 120px;
        position: relative;
    }
    
    .progress-ring svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }
    
    .progress-ring circle {
        fill: none;
        stroke-width: 8;
        stroke-linecap: round;
    }
    
    .progress-ring .background {
        stroke: rgba(255,255,255,0.2);
    }
    
    .progress-ring .progress {
        stroke: #fff;
        stroke-dasharray: 283; /* 2 * π * 45 */
        stroke-dashoffset: 283;
        transition: stroke-dashoffset 2s ease-out;
    }
    
    .co2-impact {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        border-left: 5px solid #16a34a;
    }
    
    .recent-trip {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .recent-trip:hover {
        border-left-color: #2563eb;
        background: rgba(37, 99, 235, 0.05);
        transform: translateX(5px);
    }
    
    .pulse-dot {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
    }
</style>
@endpush

@section('content')
<!-- Header personalizado -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-primary text-white border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="text-white mb-1">
                            ¡Hola, {{ Auth::user()->nombre }}! 👋
                        </h2>
                        <p class="text-white-50 mb-0 lead">
                            Bienvenido a tu dashboard de EcoBici Puerto Barrios
                        </p>
                        <small class="text-white-75">
                            <i class="fas fa-calendar me-1"></i>
                            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </small>
                    </div>
                    <div class="col-lg-4 text-end">
                        @if(Auth::user()->tieneMembresiaActiva())
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="me-3">
                                    <span class="badge bg-success fs-6 px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Membresía Activa
                                    </span>
                                    <div class="text-white-75 small mt-1">
                                        Vence: {{ Auth::user()->membresiaActiva->fecha_fin->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="pulse-dot bg-success rounded-circle" style="width: 12px; height: 12px;"></div>
                            </div>
                        @else
                            <a href="{{ route('membresias.index') }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Activar Membresía
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas principales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card bg-primary text-white h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-route fa-3x mb-3 opacity-75"></i>
                <h2 class="mb-1" data-counter="{{ $stats['recorridos_totales'] }}">{{ $stats['recorridos_totales'] }}</h2>
                <p class="mb-0 text-white-75">Recorridos Totales</p>
                <small class="text-white-50">
                    <i class="fas fa-arrow-up me-1"></i>
                    +12% este mes
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card bg-success text-white h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-leaf fa-3x mb-3 opacity-75"></i>
                <h2 class="mb-1" data-counter="{{ abs($stats['co2_reducido_total']) }}">{{ number_format(abs($stats['co2_reducido_total']), 2) }}</h2>
                <p class="mb-0 text-white-75">kg CO₂ Reducido</p>
                <small class="text-white-50">
                    <i class="fas fa-tree me-1"></i>
                    {{ number_format(abs($stats['co2_reducido_total']) / 21.8, 1) }} árboles equivalentes
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card bg-info text-white h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-star fa-3x mb-3 opacity-75"></i>
                <h2 class="mb-1" data-counter="{{ abs($stats['puntos_verdes']) }}">{{ number_format(abs($stats['puntos_verdes'])) }}</h2>
                <p class="mb-0 text-white-75">Puntos Verdes</p>
                <small class="text-white-50">
                    <i class="fas fa-gift me-1"></i>
                    {{ floor(abs($stats['puntos_verdes']) / 50) }} recompensas disponibles
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card bg-warning text-white h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-clock fa-3x mb-3 opacity-75"></i>
                <h2 class="mb-1" data-counter="{{ $stats['tiempo_total_minutos'] }}">{{ number_format($stats['tiempo_total_minutos']) }}</h2>
                <p class="mb-0 text-white-75">Minutos Totales</p>
                <small class="text-white-50">
                    <i class="fas fa-heart me-1"></i>
                    {{ number_format($stats['tiempo_total_minutos'] * 0.1) }} calorías quemadas
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Recorrido en curso -->
@if($usoEnCurso)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning shadow-lg" data-uso-activo="true">
            <div class="card-header bg-warning text-dark">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-bicycle me-2 fa-spin"></i>
                            Recorrido en Curso
                        </h5>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-dark">En Vivo</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-2">
                                    <strong>Bicicleta:</strong> 
                                    <span class="badge bg-primary" data-bicicleta-codigo>{{ $usoEnCurso->bicicleta->codigo }}</span>
                                    <span class="badge bg-{{ $usoEnCurso->bicicleta->tipo === 'electrica' ? 'info' : 'success' }} ms-1">
                                        {{ ucfirst($usoEnCurso->bicicleta->tipo) }}
                                    </span>
                                </p>
                                <p class="mb-2">
                                    <strong>Desde:</strong> {{ $usoEnCurso->estacionInicio->nombre }}
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-2">
                                    <strong>Iniciado:</strong> {{ $usoEnCurso->fecha_hora_inicio->format('H:i') }}
                                </p>
                                <p class="mb-0">
                                    <strong>Tiempo transcurrido:</strong> 
                                    <span class="badge bg-info" data-tiempo-transcurrido>
                                        {{ $usoEnCurso->fecha_hora_inicio->diffForHumans() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-grid gap-2">
                            <a href="{{ route('bicicletas.mostrar-uso', $usoEnCurso->id) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-map-marked-alt me-1"></i>
                                Finalizar Recorrido
                            </a>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Recuerda devolver la bicicleta en una estación
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Contenido principal -->
<div class="row">
    <!-- Membresía actual y progreso -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-id-card me-2"></i>
                            Mi Membresía Actual
                        </h5>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('membresias.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog me-1"></i>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($membresiaActiva)
                    <div class="row">
                        <div class="col-lg-8">
                            <h6 class="text-primary mb-2">{{ $membresiaActiva->membresia->nombre }}</h6>
                            
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <p class="mb-1">
                                        <i class="fas fa-calendar-alt text-muted me-1"></i>
                                        <strong>Vence:</strong> {{ $membresiaActiva->fecha_fin->format('d/m/Y') }}
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-clock text-muted me-1"></i>
                                        <strong>Minutos restantes:</strong> 
                                        <span class="text-{{ $membresiaActiva->minutosRestantes() > 500 ? 'success' : ($membresiaActiva->minutosRestantes() > 100 ? 'warning' : 'danger') }}">
                                            {{ number_format($membresiaActiva->minutosRestantes()) }} min
                                        </span>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1">
                                        <i class="fas fa-bicycle text-muted me-1"></i>
                                        <strong>Tipo:</strong> 
                                        <span class="badge bg-info">{{ ucfirst($membresiaActiva->membresia->tipo_bicicleta) }}</span>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-money-bill-wave text-muted me-1"></i>
                                        <strong>Pagado:</strong> Q{{ number_format($membresiaActiva->monto_pagado, 2) }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Barra de progreso de minutos -->
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Minutos utilizados</span>
                                    <span>{{ number_format($membresiaActiva->membresia->minutos_incluidos - $membresiaActiva->minutosRestantes()) }} / {{ number_format($membresiaActiva->membresia->minutos_incluidos) }}</span>
                                </div>
                                @php
                                    $porcentajeUsado = (($membresiaActiva->membresia->minutos_incluidos - $membresiaActiva->minutosRestantes()) / $membresiaActiva->membresia->minutos_incluidos) * 100;
                                @endphp
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar bg-{{ $porcentajeUsado < 70 ? 'success' : ($porcentajeUsado < 90 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $porcentajeUsado }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gráfico circular de progreso -->
                        <div class="col-lg-4 text-center">
                            <div class="progress-ring mx-auto">
                                <svg>
                                    <circle cx="60" cy="60" r="45" class="background"></circle>
                                    <circle cx="60" cy="60" r="45" class="progress" 
                                            style="stroke-dashoffset: {{ 283 - (283 * ($porcentajeUsado / 100)) }}"></circle>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <h4 class="text-primary mb-0">{{ number_format(100 - $porcentajeUsado, 1) }}%</h4>
                                    <small class="text-muted">Restante</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                        <h5 class="text-muted mb-3">No tienes una membresía activa</h5>
                        <p class="text-muted mb-4">Activa tu membresía para comenzar a usar las bicicletas de EcoBici</p>
                        <a href="{{ route('membresias.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>
                            Activar Membresía
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recompensas disponibles -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-gift me-2"></i>
                            Recompensas
                        </h5>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-warning">{{ $recompensasDisponibles->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($recompensasDisponibles->count() > 0)
                    @foreach($recompensasDisponibles as $recompensa)
                        <div class="d-flex align-items-center p-3 border rounded mb-2 bg-light">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $recompensa->nombre }}</h6>
                                <p class="mb-1 small text-muted">{{ Str::limit($recompensa->descripcion, 50) }}</p>
                                <span class="badge bg-primary">{{ $recompensa->puntos_requeridos }} puntos</span>
                            </div>
                            <div class="ms-2">
                                <button class="btn btn-sm btn-outline-success" onclick="canjearRecompensa({{ $recompensa->id }})">
                                    <i class="fas fa-star"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <a href="#" class="text-primary text-decoration-none">
                            <i class="fas fa-eye me-1"></i>
                            Ver todas las recompensas
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted mb-2">Sin recompensas disponibles</h6>
                        <p class="text-muted small mb-3">Acumula más puntos verdes realizando recorridos</p>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ min(100, (Auth::user()->puntos_verdes / 50) * 100) }}%"></div>
                        </div>
                        <small class="text-muted">
                            {{ Auth::user()->puntos_verdes }}/50 puntos para tu primera recompensa
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Impacto ambiental y últimos recorridos -->
<div class="row">
    <!-- Impacto ambiental -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-leaf me-2 text-success"></i>
                    Tu Impacto Ambiental
                </h5>
            </div>
            <div class="card-body">
                <div class="co2-impact p-4 rounded mb-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fas fa-leaf fa-3x text-success"></i>
                        </div>
                        <div class="col">
                            <h4 class="text-success mb-1">{{ number_format(abs($stats['co2_reducido_total']), 2) }} kg CO₂</h4>
                            <p class="mb-0 small">Total de CO₂ que has evitado emitir</p>
                        </div>
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <i class="fas fa-tree text-success fa-2x mb-2"></i>
                            <h6 class="mb-1">{{ number_format(abs($stats['co2_reducido_total']) / 21.8, 1) }}</h6>
                            <small class="text-muted">Árboles plantados equivalente</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <i class="fas fa-car text-danger fa-2x mb-2"></i>
                        <h6 class="mb-1">{{ number_format(abs($stats['co2_reducido_total']) / 4.6, 1) }}</h6>
                        <small class="text-muted">Días de auto evitados</small>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <p class="small text-muted mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Cada recorrido cuenta para un planeta más limpio
                    </p>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ min(100, (abs($stats['co2_reducido_total']) / 100) * 100) }}%" title="Progreso hacia 100kg CO₂"></div>
                    </div>
                    <small class="text-muted">Meta: 100kg CO₂ reducidos</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Últimos recorridos -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Últimos Recorridos
                </h5>
                <a href="{{ route('bicicletas.historial') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>
                    Ver Todo
                </a>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @if($ultimosRecorridos->count() > 0)
                    @foreach($ultimosRecorridos as $recorrido)
                        <div class="recent-trip p-3 rounded mb-2 bg-light">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">
                                        <span class="badge bg-secondary">{{ $recorrido->bicicleta->codigo }}</span>
                                        <span class="badge bg-{{ $recorrido->bicicleta->tipo === 'electrica' ? 'info' : 'success' }} ms-1">
                                            {{ ucfirst($recorrido->bicicleta->tipo) }}
                                        </span>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $recorrido->fecha_hora_inicio->format('d/m H:i') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="small">
                                        <span class="text-success">
                                            <i class="fas fa-leaf me-1"></i>
                                            {{ number_format($recorrido->co2_reducido, 2) }}kg CO₂
                                        </span>
                                    </div>
                                    <div class="small">
                                        <span class="text-warning">
                                            <i class="fas fa-star me-1"></i>
                                            {{ $recorrido->puntos_verdes_ganados }} pts
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="small text-muted">
                                    {{ Str::limit($recorrido->estacionInicio->nombre, 15) }}
                                    <i class="fas fa-arrow-right mx-1"></i>
                                    {{ $recorrido->estacionFin ? Str::limit($recorrido->estacionFin->nombre, 15) : 'En curso' }}
                                </div>
                                <div class="small">
                                    <span class="badge bg-primary">{{ $recorrido->duracion_minutos ?? 0 }} min</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bicycle fa-4x text-muted mb-3"></i>
                        <h6 class="text-muted mb-2">Aún no has realizado recorridos</h6>
                        <p class="text-muted mb-3">¡Comienza tu primera aventura en bicicleta!</p>
                        <a href="{{ route('bicicletas.seleccionar') }}" class="btn btn-success">
                            <i class="fas fa-bicycle me-2"></i>
                            Usar Bicicleta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Acciones rápidas -->
<div class="row">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="text-center mb-4">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Acciones Rápidas
                </h5>
                
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('bicicletas.seleccionar') }}" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-bicycle fa-2x mb-2"></i><br>
                            <span>Usar Bicicleta</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('estaciones.mapa') }}" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-map-marked-alt fa-2x mb-2"></i><br>
                            <span>Ver Mapa</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('rutas.crear') }}" class="btn btn-info btn-lg w-100">
                            <i class="fas fa-route fa-2x mb-2"></i><br>
                            <span>Crear Ruta</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('bicicletas.historial') }}" class="btn btn-secondary btn-lg w-100">
                            <i class="fas fa-history fa-2x mb-2"></i><br>
                            <span>Mi Historial</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animar contadores al cargar
    document.querySelectorAll('[data-counter]').forEach(counter => {
        EcoBici.animarNumero(counter, parseInt(counter.dataset.counter), 2000);
    });
    
    // Actualizar tiempo transcurrido si hay uso activo
    @if($usoEnCurso)
        setInterval(() => {
            EcoBici.verificarUsoActivo();
        }, 30000);
    @endif
});

function canjearRecompensa(recompensaId) {
    if (confirm('¿Estás seguro de que quieres canjear esta recompensa?')) {
        // Aquí iría la lógica de canje
        EcoBici.mostrarNotificacion('Recompensa canjeada exitosamente', 'success');
    }
}
</script>
@endpush