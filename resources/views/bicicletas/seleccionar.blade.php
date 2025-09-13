@extends('layouts.app')

@section('title', 'Seleccionar Bicicleta - EcoBici')

@section('content')
<div class="container">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center">
                <h2 class="mb-3">
                    <i class="fas fa-bicycle text-success me-2"></i>
                    Selecciona tu EcoBici
                </h2>
                <p class="text-muted lead">
                    Encuentra la bicicleta perfecta para tu recorrido
                </p>
            </div>
        </div>
    </div>

    <!-- Información de membresía -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="text-success mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                Tu Membresía Activa
                            </h5>
                            <p class="mb-1">
                                <strong>Tipo de bicicletas disponibles:</strong>
                                @if($tipoBicicleta === 'ambas')
                                    <span class="badge bg-warning">👑 Premium - Tradicionales y Eléctricas</span>
                                @elseif($tipoBicicleta === 'tradicional')
                                    <span class="badge bg-success">🚲 Bicicletas Tradicionales</span>
                                @else
                                    <span class="badge bg-info">⚡ Bicicletas Eléctricas</span>
                                @endif
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Las bicicletas mostradas están disponibles según tu plan de membresía
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('membresias.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-upgrade me-1"></i>
                                Mejorar Plan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($bicicletas->isEmpty())
        <!-- No hay bicicletas disponibles -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
                        <h4 class="text-muted mb-3">No hay bicicletas disponibles</h4>
                        <p class="text-muted mb-4">
                            En este momento no tenemos bicicletas disponibles que coincidan con tu membresía.
                            <br>Te recomendamos intentar en unos minutos o visitar otra estación.
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('estaciones.mapa') }}" class="btn btn-primary">
                                <i class="fas fa-map-marked-alt me-2"></i>
                                Ver Otras Estaciones
                            </a>
                            <button onclick="location.reload()" class="btn btn-outline-success">
                                <i class="fas fa-sync-alt me-2"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Bicicletas agrupadas por estación -->
        @foreach($bicicletas as $nombreEstacion => $bicicletasEstacion)
            <div class="row mb-5">
                <div class="col-12">
                    <!-- Encabezado de estación -->
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $nombreEstacion }}
                                    </h5>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark">
                                        {{ count($bicicletasEstacion) }} bicicleta{{ count($bicicletasEstacion) !== 1 ? 's' : '' }} disponible{{ count($bicicletasEstacion) !== 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid de bicicletas -->
                    <div class="row">
                        @foreach($bicicletasEstacion as $bicicleta)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card bicicleta-card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <!-- Tipo y código -->
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="card-title mb-1">
                                                    @if($bicicleta->tipo === 'tradicional')
                                                        <i class="fas fa-bicycle text-success me-2"></i>
                                                        Tradicional
                                                    @else
                                                        <i class="fas fa-bolt text-warning me-2"></i>
                                                        Eléctrica
                                                    @endif
                                                </h6>
                                                <small class="text-muted">{{ $bicicleta->codigo }}</small>
                                            </div>
                                            <span class="badge bg-{{ $bicicleta->tipo === 'tradicional' ? 'success' : 'warning' }}">
                                                {{ $bicicleta->tipo === 'tradicional' ? '🚲' : '⚡' }}
                                            </span>
                                        </div>

                                        <!-- Estado y batería -->
                                        <div class="mb-3">
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Estado:</small><br>
                                                    <span class="badge bg-success">Disponible</span>
                                                </div>
                                                @if($bicicleta->tipo === 'electrica')
                                                    <div class="col-6">
                                                        <small class="text-muted">Batería:</small><br>
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                                <div class="progress-bar bg-{{ $bicicleta->nivel_bateria >= 50 ? 'success' : ($bicicleta->nivel_bateria >= 20 ? 'warning' : 'danger') }}" 
                                                                     style="width: {{ $bicicleta->nivel_bateria ?? 85 }}%"></div>
                                                            </div>
                                                            <small class="text-muted">{{ $bicicleta->nivel_bateria ?? 85 }}%</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Características -->
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">Características:</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @if($bicicleta->tipo === 'tradicional')
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-heart text-danger me-1"></i>Ejercicio
                                                    </span>
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-leaf text-success me-1"></i>Eco-friendly
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-zap text-warning me-1"></i>Asistencia eléctrica
                                                    </span>
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-mountain text-info me-1"></i>Mayor alcance
                                                    </span>
                                                @endif
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-lock text-primary me-1"></i>Segura
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Información adicional -->
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Última revisión: {{ $bicicleta->updated_at->diffForHumans() }}
                                            </small>
                                        </div>

                                        <!-- Botón de selección -->
                                        <div class="d-grid">
                                            <button 
                                                class="btn btn-{{ $bicicleta->tipo === 'tradicional' ? 'success' : 'warning' }} btn-lg"
                                                onclick="EcoBici.usarBicicleta({{ $bicicleta->id }}, {{ $bicicleta->estacion_actual_id }})"
                                            >
                                                <i class="fas fa-play me-2"></i>
                                                Iniciar Recorrido
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Footer con ubicación -->
                                    <div class="card-footer bg-light border-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ Str::limit($bicicleta->estacionActual->direccion, 30) }}
                                            </small>
                                            <a href="{{ route('estaciones.show', $bicicleta->estacion_actual_id) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Panel informativo -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="text-center mb-4">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Consejos para tu Recorrido
                    </h5>
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div class="tip-icon">
                                <i class="fas fa-helmet-safety fa-2x text-primary mb-2"></i>
                            </div>
                            <h6>Seguridad Primero</h6>
                            <small class="text-muted">
                                Usa siempre casco y respeta las señales de tránsito
                            </small>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="tip-icon">
                                <i class="fas fa-route fa-2x text-success mb-2"></i>
                            </div>
                            <h6>Planifica tu Ruta</h6>
                            <small class="text-muted">
                                Revisa las estaciones de destino antes de partir
                            </small>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="tip-icon">
                                <i class="fas fa-clock fa-2x text-info mb-2"></i>
                            </div>
                            <h6>Controla el Tiempo</h6>
                            <small class="text-muted">
                                Recuerda los minutos incluidos en tu membresía
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones adicionales -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver al Dashboard
                </a>
                <a href="{{ route('estaciones.mapa') }}" class="btn btn-outline-primary">
                    <i class="fas fa-map me-2"></i>
                    Ver Mapa de Estaciones
                </a>
                <button onclick="location.reload()" class="btn btn-outline-success">
                    <i class="fas fa-sync-alt me-2"></i>
                    Actualizar Disponibilidad
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .bicicleta-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .bicicleta-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
    }

    .tip-icon {
        margin-bottom: 1rem;
    }

    .tip-icon i {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        padding: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 10px;
    }

    .badge {
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .bicicleta-card:hover {
            transform: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-actualizar cada 2 minutos
    setInterval(function() {
        // Mostrar indicador sutil de actualización
        const indicator = document.createElement('div');
        indicator.className = 'position-fixed top-0 end-0 m-3 alert alert-info alert-dismissible fade show';
        indicator.innerHTML = `
            <i class="fas fa-sync-alt fa-spin me-2"></i>
            Actualizando disponibilidad...
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(indicator);
        
        // Actualizar después de 2 segundos
        setTimeout(() => {
            location.reload();
        }, 2000);
    }, 120000); // 2 minutos

    // Animación de entrada para las tarjetas
    const cards = document.querySelectorAll('.bicicleta-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection