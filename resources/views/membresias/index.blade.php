{{-- resources/views/membresias/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Membresías - EcoBici')

@push('styles')
<style>
    .membership-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .membership-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--ecobici-azul), var(--ecobici-verde), var(--ecobici-celeste));
        transition: left 0.5s ease;
    }
    
    .membership-card:hover::before {
        left: 0;
    }
    
    .membership-card:hover {
        transform: translateY(-10px) scale(1.02);
        border-color: var(--ecobici-azul);
        box-shadow: 0 20px 40px rgba(37, 99, 235, 0.2);
    }
    
    .membership-recommended {
        border-color: var(--ecobici-verde) !important;
        box-shadow: 0 10px 30px rgba(22, 163, 74, 0.15);
    }
    
    .price-display {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(45deg, var(--ecobici-verde), var(--ecobici-celeste));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .benefit-item {
        transition: all 0.3s ease;
        padding: 8px 0;
        border-radius: 8px;
    }
    
    .benefit-item:hover {
        background: rgba(37, 99, 235, 0.05);
        transform: translateX(5px);
    }
    
    .bike-type-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .bike-traditional {
        background: linear-gradient(45deg, var(--ecobici-verde), rgba(22, 163, 74, 0.8));
        color: white;
    }
    
    .bike-electric {
        background: linear-gradient(45deg, var(--ecobici-celeste), rgba(14, 165, 233, 0.8));
        color: white;
    }
    
    .bike-premium {
        background: linear-gradient(45deg, #f59e0b, #f97316);
        color: white;
    }
    
    .plan-value-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(45deg, var(--ecobici-verde), var(--ecobici-celeste));
        color: white;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(22, 163, 74, 0.25);
    }
    
    .comparison-table {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .why-choose-card {
        background: linear-gradient(135deg, var(--ecobici-azul-claro), var(--ecobici-verde-claro));
        border: none;
        border-radius: 20px;
    }
    
    .feature-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(45deg, var(--ecobici-azul), var(--ecobici-celeste));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
        transition: all 0.3s ease;
    }
    
    .feature-icon:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }
    
    .current-membership {
        background: linear-gradient(135deg, var(--ecobici-verde-claro), rgba(255, 255, 255, 0.9));
        border-left: 5px solid var(--ecobici-verde);
        animation: glow 3s ease-in-out infinite alternate;
    }
    
    @keyframes glow {
        from { box-shadow: 0 0 20px rgba(22, 163, 74, 0.2); }
        to { box-shadow: 0 0 30px rgba(22, 163, 74, 0.4); }
    }

    .monthly-cost-info {
        background: rgba(22, 163, 74, 0.1);
        border-radius: 10px;
        padding: 8px 12px;
        margin-top: 8px;
    }
</style>
@endpush

@section('content')
<!-- Hero section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="text-center">
            <div class="mb-4">
                <i class="fas fa-id-card fa-4x text-primary mb-3"></i>
                <h1 class="display-5 fw-bold text-primary mb-3">
                    Planes de Membresía EcoBici
                </h1>
                <p class="lead text-muted mb-4">
                    Elige el plan perfecto para tu estilo de movilidad sostenible en Puerto Barrios
                </p>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="alert alert-info border-0 rounded-4">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-lightbulb fa-2x text-warning"></i>
                                </div>
                                <div class="col">
                                    <h6 class="alert-heading mb-1">💡 ¿Sabías que...?</h6>
                                    <p class="mb-0">Los planes anuales te ofrecen mejor valor y contribuyes más al medio ambiente</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Membresía actual (si existe) -->
@if($membresiaActual)
<div class="row mb-5">
    <div class="col-12">
        <div class="card current-membership">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle fa-2x text-success me-3"></i>
                            <div>
                                <h5 class="text-success mb-1">
                                    ✨ Membresía Activa: {{ $membresiaActual->membresia->nombre }}
                                </h5>
                                <p class="text-muted mb-0">
                                    Activa desde {{ $membresiaActual->fecha_inicio->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Válida hasta:</strong><br>
                                <span class="text-primary">{{ $membresiaActual->fecha_fin->format('d/m/Y') }}</span>
                                <small class="text-muted d-block">{{ $membresiaActual->fecha_fin->diffForHumans() }}</small>
                            </div>
                            <div class="col-md-4">
                                <strong>Minutos restantes:</strong><br>
                                <span class="text-{{ $membresiaActual->minutosRestantes() > 500 ? 'success' : 'warning' }}">
                                    {{ number_format($membresiaActual->minutosRestantes()) }} min
                                </span>
                            </div>
                            <div class="col-md-4">
                                <strong>Tipo de bicicletas:</strong><br>
                                <span class="badge bg-info">{{ ucfirst($membresiaActual->membresia->tipo_bicicleta) }}</span>
                            </div>
                        </div>
                        
                        <!-- Barra de progreso -->
                        <div class="mt-3">
                            @php
                                $porcentajeUsado = (($membresiaActual->membresia->minutos_incluidos - $membresiaActual->minutosRestantes()) / $membresiaActual->membresia->minutos_incluidos) * 100;
                            @endphp
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Progreso de uso</span>
                                <span>{{ number_format($porcentajeUsado, 1) }}% utilizado</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $porcentajeUsado }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-end">
                        <div class="d-grid gap-2">
                            <a href="{{ route('membresias.historial') }}" class="btn btn-outline-success">
                                <i class="fas fa-history me-1"></i>
                                Ver Historial
                            </a>
                            <a href="{{ route('bicicletas.seleccionar') }}" class="btn btn-success">
                                <i class="fas fa-bicycle me-1"></i>
                                Usar Bicicleta
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Planes disponibles por categoría -->
@foreach($membresias->groupBy('tipo_bicicleta') as $tipo => $planes)
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center mb-4">
                <h3 class="mb-2">
                    @if($tipo === 'tradicional')
                        <i class="fas fa-bicycle text-success me-2"></i>
                        🚲 Bicicletas Tradicionales
                        <small class="text-muted d-block">Perfectas para recorridos urbanos y ejercicio</small>
                    @elseif($tipo === 'electrica')
                        <i class="fas fa-bolt text-warning me-2"></i>
                        ⚡ Bicicletas Eléctricas
                        <small class="text-muted d-block">Ideal para distancias largas y mayor comodidad</small>
                    @else
                        <i class="fas fa-crown text-warning me-2"></i>
                        👑 Acceso Premium (Ambas)
                        <small class="text-muted d-block">La experiencia completa de EcoBici</small>
                    @endif
                </h3>
            </div>
            
            <div class="row justify-content-center">
                @foreach($planes as $membresia)
                    <div class="col-lg-6 col-xl-5 mb-4">
                        <div class="card membership-card h-100 {{ $membresia->duracion === 'anual' ? 'membership-recommended' : '' }}">
                            
                            <!-- Badge de tipo de bicicleta -->
                            <div class="bike-type-badge bike-{{ $tipo === 'ambas' ? 'premium' : $tipo }}">
                                @if($tipo === 'tradicional')
                                    🚲
                                @elseif($tipo === 'electrica')
                                    ⚡
                                @else
                                    👑
                                @endif
                            </div>
                            
                            <!-- Badge de mejor valor para planes anuales -->
                            @if($membresia->duracion === 'anual')
                                <div class="plan-value-badge">
                                    Mejor
                                </div>
                            @endif
                            
                            <div class="card-body p-4 text-center">
                                <!-- Título y descripción -->
                                <div class="mt-3 mb-4">
                                    <h4 class="card-title text-primary fw-bold">{{ $membresia->nombre }}</h4>
                                    <p class="text-muted">{{ $membresia->descripcion }}</p>
                                </div>
                                
                                <!-- Precio principal -->
                                <div class="mb-4">
                                    <div class="price-display mb-1">
                                        Q{{ number_format($membresia->precio, 0) }}
                                    </div>
                                    <div class="text-muted">
                                        <small>por {{ $membresia->duracion === 'mensual' ? 'mes' : 'año' }}</small>
                                        @if($membresia->duracion === 'anual')
                                            <div class="monthly-cost-info">
                                                <i class="fas fa-calculator me-1"></i>
                                                <small class="text-success">
                                                    <strong>Solo Q{{ number_format($membresia->precio / 12, 0) }} por mes</strong>
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Características principales -->
                                <div class="row text-center mb-4">
                                    <div class="col-4">
                                        <div class="border-end">
                                            <i class="fas fa-clock text-info mb-1 d-block"></i>
                                            <strong>{{ number_format($membresia->minutos_incluidos / 60, 0) }}h</strong>
                                            <small class="text-muted d-block">incluidas</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-end">
                                            <i class="fas fa-plus-circle text-warning mb-1 d-block"></i>
                                            <strong>Q{{ $membresia->tarifa_minuto_extra }}</strong>
                                            <small class="text-muted d-block">por min extra</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-calendar text-primary mb-1 d-block"></i>
                                        <strong>{{ $membresia->duracion_dias }}</strong>
                                        <small class="text-muted d-block">días</small>
                                    </div>
                                </div>
                                
                                <!-- Lista de beneficios -->
                                <div class="text-start mb-4">
                                    <h6 class="text-center mb-3">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        Beneficios Incluidos
                                    </h6>
                                    @foreach($membresia->beneficios as $beneficio)
                                        <div class="benefit-item">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <small>{{ $beneficio }}</small>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Botón de acción -->
                                <div class="d-grid">
                                    @if(!$membresiaActual || !$membresiaActual->estaVigente())
                                        <a href="{{ route('membresias.pago', $membresia->id) }}" 
                                           class="btn {{ $membresia->duracion === 'anual' ? 'btn-success' : 'btn-primary' }} btn-lg">
                                            <i class="fas fa-credit-card me-2"></i>
                                            Seleccionar Plan
                                        </a>
                                    @else
                                        <button class="btn btn-outline-secondary btn-lg" disabled>
                                            <i class="fas fa-check me-2"></i>
                                            Tienes Membresía Activa
                                        </button>
                                    @endif
                                </div>
                                
                                <!-- Información adicional -->
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Sin permanencia • Cancela cuando quieras
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach

<!-- Tabla de comparación -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card comparison-table">
            <div class="card-header text-center">
                <h4 class="mb-0">
                    <i class="fas fa-balance-scale me-2"></i>
                    Comparación de Planes
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Característica</th>
                                <th class="text-center">Tradicional Mensual</th>
                                <th class="text-center">Tradicional Anual</th>
                                <th class="text-center">Eléctrica Mensual</th>
                                <th class="text-center">Eléctrica Anual</th>
                                <th class="text-center">Premium Anual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Precio</strong></td>
                                <td class="text-center">Q75/mes</td>
                                <td class="text-center text-success"><strong>Q720/año</strong> <small>(Q60/mes)</small></td>
                                <td class="text-center">Q150/mes</td>
                                <td class="text-center text-success"><strong>Q1,440/año</strong> <small>(Q120/mes)</small></td>
                                <td class="text-center text-warning"><strong>Q1,920/año</strong> <small>(Q160/mes)</small></td>
                            </tr>
                            <tr>
                                <td><strong>Horas incluidas</strong></td>
                                <td class="text-center">30h</td>
                                <td class="text-center">360h</td>
                                <td class="text-center">15h</td>
                                <td class="text-center">180h</td>
                                <td class="text-center text-success"><strong>480h</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Tipos de bicicleta</strong></td>
                                <td class="text-center">🚲 Tradicionales</td>
                                <td class="text-center">🚲 Tradicionales</td>
                                <td class="text-center">⚡ Eléctricas</td>
                                <td class="text-center">⚡ Eléctricas</td>
                                <td class="text-center text-success"><strong>🚲⚡ Ambas</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Puntos verdes</strong></td>
                                <td class="text-center">x1</td>
                                <td class="text-center text-success">x2</td>
                                <td class="text-center">x1.5</td>
                                <td class="text-center text-success">x2</td>
                                <td class="text-center text-warning"><strong>x3</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Soporte</strong></td>
                                <td class="text-center">Estándar</td>
                                <td class="text-center">Estándar</td>
                                <td class="text-center">Estándar</td>
                                <td class="text-center">Prioritario</td>
                                <td class="text-center text-success"><strong>VIP 24/7</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ¿Por qué elegir EcoBici? -->
<div class="row">
    <div class="col-12">
        <div class="card why-choose-card">
            <div class="card-body p-5">
                <h4 class="text-center mb-5">
                    <i class="fas fa-heart text-danger me-2"></i>
                    ¿Por qué elegir EcoBici Puerto Barrios?
                </h4>
                
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h6 class="fw-bold">Eco-Amigable</h6>
                        <p class="small text-muted">Cada kilómetro reduce tu huella de carbono y ayuda al planeta. ¡Cada recorrido cuenta!</p>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h6 class="fw-bold">Vida Saludable</h6>
                        <p class="small text-muted">Mantente en forma mientras te transportas. Ejercicio y movilidad en uno solo.</p>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h6 class="fw-bold">Ahorra Dinero</h6>
                        <p class="small text-muted">Más económico que transporte privado. Sin gasolina, sin parqueos, sin mantenimiento.</p>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h6 class="fw-bold">Disponible 24/7</h6>
                        <p class="small text-muted">Úsalo cuando necesites, donde necesites. Sin horarios ni restricciones.</p>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <div class="alert alert-success d-inline-block">
                        <i class="fas fa-users me-2"></i>
                        <strong>+500 usuarios</strong> ya eligieron EcoBici para moverse por Puerto Barrios
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
    // Animaciones de entrada para las cards
    const cards = document.querySelectorAll('.membership-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
    
    // Efecto de brillo en hover para botones
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Animación del progress bar de membresía actual
    const progressBar = document.querySelector('.current-membership .progress-bar');
    if (progressBar) {
        const width = progressBar.style.width;
        progressBar.style.width = '0%';
        setTimeout(() => {
            progressBar.style.transition = 'width 2s ease';
            progressBar.style.width = width;
        }, 500);
    }
});
</script>
@endpush