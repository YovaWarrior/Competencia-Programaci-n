@extends('layouts.app')

@section('title', 'Reporte CO₂ - EcoBici')

@push('styles')
<style>
    .impact-card {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        position: relative;
        overflow: hidden;
    }

    .impact-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    .co2-number {
        font-size: 4rem;
        font-weight: 900;
        text-shadow: 0 4px 8px rgba(0,0,0,0.2);
        margin: 20px 0;
    }

    .comparison-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .comparison-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .comparison-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 15px;
    }

    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }

    .progress-eco {
        height: 25px;
        border-radius: 15px;
        background: #e5e7eb;
        overflow: hidden;
        position: relative;
    }

    .progress-eco .progress-bar {
        background: linear-gradient(90deg, #10b981, #34d399, #6ee7b7);
        border-radius: 15px;
        position: relative;
        overflow: hidden;
    }

    .progress-eco .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: progress-shine 2s infinite;
    }

    @keyframes progress-shine {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    .filter-section {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }

    .date-filter {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .date-filter:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .eco-badge {
        background: linear-gradient(45deg, #10b981, #34d399);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .trees-animation {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 20px 0;
    }

    .tree {
        font-size: 2rem;
        animation: sway 3s ease-in-out infinite;
    }

    .tree:nth-child(even) {
        animation-delay: 0.5s;
    }

    @keyframes sway {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(2deg); }
        75% { transform: rotate(-2deg); }
    }

    .impact-stat {
        text-align: center;
        padding: 20px;
        margin: 10px 0;
    }

    .impact-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #059669;
        margin-bottom: 5px;
    }

    .floating-share-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        border-radius: 50px;
        padding: 15px 25px;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-leaf text-success me-3"></i>
                Tu Impacto Ambiental
            </h1>
            <p class="lead text-muted">
                Descubre cómo estás ayudando a crear un Puerto Barrios más verde
            </p>
        </div>
    </div>

    <!-- Filtros de fecha -->
    <div class="filter-section">
        <form method="GET" action="{{ route('reportes.co2') }}" class="row align-items-end">
            <div class="col-md-4">
                <label for="fecha_inicio" class="form-label fw-bold">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Desde
                </label>
                <input type="date" 
                       class="form-control date-filter" 
                       id="fecha_inicio" 
                       name="fecha_inicio" 
                       value="{{ $fechaInicio ?? '' }}">
            </div>
            <div class="col-md-4">
                <label for="fecha_fin" class="form-label fw-bold">
                    <i class="fas fa-calendar-check me-1"></i>
                    Hasta
                </label>
                <input type="date" 
                       class="form-control date-filter" 
                       id="fecha_fin" 
                       name="fecha_fin" 
                       value="{{ $fechaFin ?? '' }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-filter me-2"></i>
                    Filtrar Período
                </button>
            </div>
        </form>
    </div>

    <!-- Impacto Principal -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="impact-card">
                <h2 class="mb-3">
                    <i class="fas fa-globe-americas me-2"></i>
                    ¡Felicidades por tu impacto positivo!
                </h2>
                <div class="co2-number" data-counter="{{ $totalCO2Reducido ?? 0 }}">
                    {{ number_format($totalCO2Reducido ?? 0, 1) }}
                </div>
                <h3 class="mb-3">Kilogramos de CO₂ reducido</h3>
                
                <div class="trees-animation">
                    @for($i = 0; $i < min(5, floor(($totalCO2Reducido ?? 0) / 10)); $i++)
                        <span class="tree">🌳</span>
                    @endfor
                </div>
                
                <p class="mb-0 fs-5">
                    Equivale a plantar <strong>{{ number_format(($totalCO2Reducido ?? 0) / 22, 0) }} árboles</strong> 
                    o ahorrar <strong>{{ number_format(($totalCO2Reducido ?? 0) / 2.3, 0) }} litros de gasolina</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Estadísticas detalladas -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="comparison-card">
                <div class="card-body impact-stat">
                    <div class="comparison-icon bg-primary text-white">
                        <i class="fas fa-bicycle"></i>
                    </div>
                    <div class="impact-number">{{ $totalRecorridos ?? 0 }}</div>
                    <p class="text-muted mb-0">Recorridos Realizados</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="comparison-card">
                <div class="card-body impact-stat">
                    <div class="comparison-icon bg-success text-white">
                        <i class="fas fa-route"></i>
                    </div>
                    <div class="impact-number">{{ number_format($totalKilometros ?? 0, 1) }}</div>
                    <p class="text-muted mb-0">Kilómetros Recorridos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="comparison-card">
                <div class="card-body impact-stat">
                    <div class="comparison-icon bg-warning text-white">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="impact-number">{{ number_format(($totalMinutos ?? 0) / 60, 1) }}</div>
                    <p class="text-muted mb-0">Horas en Bicicleta</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="comparison-card">
                <div class="card-body impact-stat">
                    <div class="comparison-icon bg-info text-white">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="impact-number">{{ number_format(($totalKilometros ?? 0) * 45, 0) }}</div>
                    <p class="text-muted mb-0">Calorías Quemadas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparaciones de impacto -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="chart-container">
                <h4 class="mb-4">
                    <i class="fas fa-balance-scale me-2 text-success"></i>
                    Equivalencias de tu Impacto Ambiental
                </h4>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="comparison-icon bg-danger text-white me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Emisiones de Auto Evitadas</h6>
                                <div class="progress-eco mb-2">
                                    <div class="progress-bar" style="width: {{ min(100, (($totalCO2Reducido ?? 0) / 100) * 100) }}%"></div>
                                </div>
                                <small class="text-muted">
                                    {{ number_format(($totalCO2Reducido ?? 0) / 0.25, 0) }} km que no recorrió un auto
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="comparison-icon bg-success text-white me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="fas fa-tree"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Árboles Equivalentes</h6>
                                <div class="progress-eco mb-2">
                                    <div class="progress-bar" style="width: {{ min(100, (($totalCO2Reducido ?? 0) / 50) * 100) }}%"></div>
                                </div>
                                <small class="text-muted">
                                    {{ number_format(($totalCO2Reducido ?? 0) / 22, 1) }} árboles plantados
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="comparison-icon bg-warning text-white me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="fas fa-gas-pump"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Combustible Ahorrado</h6>
                                <div class="progress-eco mb-2">
                                    <div class="progress-bar" style="width: {{ min(100, (($totalCO2Reducido ?? 0) / 25) * 100) }}%"></div>
                                </div>
                                <small class="text-muted">
                                    {{ number_format(($totalCO2Reducido ?? 0) / 2.3, 1) }} litros de gasolina
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="comparison-icon bg-info text-white me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Energía de Hogar Ahorrada</h6>
                                <div class="progress-eco mb-2">
                                    <div class="progress-bar" style="width: {{ min(100, (($totalCO2Reducido ?? 0) / 30) * 100) }}%"></div>
                                </div>
                                <small class="text-muted">
                                    {{ number_format(($totalCO2Reducido ?? 0) * 2.2, 0) }} kWh de electricidad
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de progreso temporal -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="chart-container">
                <h4 class="mb-4">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Tu Progreso de CO₂ Reducido
                </h4>
                <canvas id="co2Chart" width="400" height="150"></canvas>
            </div>
        </div>
    </div>

    <!-- Consejos y logros -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="chart-container">
                <h5 class="text-success mb-3">
                    <i class="fas fa-trophy me-2"></i>
                    Logros Ambientales
                </h5>
                
                @if(($totalCO2Reducido ?? 0) >= 100)
                    <div class="eco-badge mb-3">
                        <i class="fas fa-medal"></i>
                        <span>Héroe del Clima</span>
                    </div>
                @endif
                
                @if(($totalRecorridos ?? 0) >= 50)
                    <div class="eco-badge mb-3">
                        <i class="fas fa-bicycle"></i>
                        <span>Ciclista Dedicado</span>
                    </div>
                @endif
                
                @if(($totalKilometros ?? 0) >= 100)
                    <div class="eco-badge mb-3">
                        <i class="fas fa-route"></i>
                        <span>Explorador Verde</span>
                    </div>
                @endif
                
                @if(count(array_filter([($totalCO2Reducido ?? 0) >= 100, ($totalRecorridos ?? 0) >= 50, ($totalKilometros ?? 0) >= 100])) === 0)
                    <div class="text-center py-4">
                        <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                        <p class="text-muted">¡Sigue pedaleando para desbloquear logros!</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="chart-container">
                <h5 class="text-info mb-3">
                    <i class="fas fa-lightbulb me-2"></i>
                    Consejos Ecológicos
                </h5>
                
                <div class="alert alert-info">
                    <strong>💡 ¿Sabías qué?</strong><br>
                    Por cada kilómetro en bicicleta en lugar de auto, reduces aproximadamente 0.25 kg de CO₂.
                </div>
                
                <div class="alert alert-success">
                    <strong>🌱 Consejo del día:</strong><br>
                    Combina varios destinos en un solo recorrido para maximizar tu impacto positivo.
                </div>
                
                <div class="alert alert-warning">
                    <strong>🎯 Meta sugerida:</strong><br>
                    Intenta reducir {{ number_format((($totalCO2Reducido ?? 0) * 0.1) + 5, 0) }} kg más de CO₂ este mes.
                </div>
            </div>
        </div>
    </div>

    <!-- Ranking comunitario -->
    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h4 class="mb-4">
                    <i class="fas fa-users me-2 text-warning"></i>
                    Ranking Comunitario - Top Eco-Ciclistas
                </h4>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>Posición</th>
                                <th>Ciclista</th>
                                <th>CO₂ Reducido (kg)</th>
                                <th>Recorridos</th>
                                <th>Kilómetros</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ranking ?? [] as $index => $usuario)
                                <tr class="{{ $usuario->id === auth()->id() ? 'table-warning' : '' }}">
                                    <td>
                                        @if($index === 0)
                                            <i class="fas fa-crown text-warning"></i> #{{ $index + 1 }}
                                        @elseif($index === 1)
                                            <i class="fas fa-medal text-secondary"></i> #{{ $index + 1 }}
                                        @elseif($index === 2)
                                            <i class="fas fa-medal text-warning"></i> #{{ $index + 1 }}
                                        @else
                                            #{{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $usuario->nombre }}
                                        @if($usuario->id === auth()->id())
                                            <span class="badge bg-info ms-2">Tú</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            {{ number_format($usuario->total_co2, 1) }}
                                        </span>
                                    </td>
                                    <td>{{ $usuario->total_recorridos }}</td>
                                    <td>{{ number_format($usuario->total_km, 1) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                        <br>No hay datos de ranking disponibles
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botón flotante para compartir -->
<button class="floating-share-btn btn btn-success" onclick="compartirImpacto()">
    <i class="fas fa-share-alt me-2"></i>
    Compartir
</button>

<!-- Modal para compartir -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-share-alt me-2"></i>
                    Comparte tu Impacto Ambiental
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="fas fa-leaf fa-4x text-success mb-3"></i>
                    <h4>¡He reducido {{ number_format($totalCO2Reducido ?? 0, 1) }} kg de CO₂!</h4>
                    <p class="text-muted">usando EcoBici Puerto Barrios 🚴‍♀️🌱</p>
                </div>
                
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-primary" onclick="compartirFacebook()">
                        <i class="fab fa-facebook me-1"></i> Facebook
                    </button>
                    <button class="btn btn-info" onclick="compartirTwitter()">
                        <i class="fab fa-twitter me-1"></i> Twitter
                    </button>
                    <button class="btn btn-success" onclick="compartirWhatsApp()">
                        <i class="fab fa-whatsapp me-1"></i> WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación del contador principal
    const counter = document.querySelector('[data-counter]');
    if (counter) {
        const target = parseFloat(counter.dataset.counter);
        animateCounter(counter, 0, target, 2000);
    }
    
    // Inicializar gráfico
    initCO2Chart();
});

function animateCounter(element, start, end, duration) {
    const startTime = performance.now();
    
    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = start + (end - start) * easeOutCubic(progress);
        element.textContent = current.toFixed(1);
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    }
    
    requestAnimationFrame(updateCounter);
}

function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
}

function initCO2Chart() {
    const ctx = document.getElementById('co2Chart');
    if (!ctx) return;
    
    // Datos simulados para el gráfico - en producción vendrían del servidor
    const data = {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        datasets: [{
            label: 'CO₂ Reducido (kg)',
            data: [12, 19, 23, 17, 28, 35],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    };
    
    new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        color: '#f3f4f6'
                    }
                }
            }
        }
    });
}

function compartirImpacto() {
    new bootstrap.Modal(document.getElementById('shareModal')).show();
}

function compartirFacebook() {
    const texto = `¡He reducido {{ number_format($totalCO2Reducido ?? 0, 1) }} kg de CO₂ usando EcoBici Puerto Barrios! 🌱🚴‍♀️`;
    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${encodeURIComponent(texto)}`;
    window.open(url, '_blank', 'width=600,height=400');
}

function compartirTwitter() {
    const texto = `¡He reducido {{ number_format($totalCO2Reducido ?? 0, 1) }} kg de CO₂ usando EcoBici Puerto Barrios! 🌱🚴‍♀️ #EcoBici #SustentabilidadGT`;
    const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(texto)}&url=${encodeURIComponent(window.location.href)}`;
    window.open(url, '_blank', 'width=600,height=400');
}

function compartirWhatsApp() {
    const texto = `¡He reducido {{ number_format($totalCO2Reducido ?? 0, 1) }} kg de CO₂ usando EcoBici Puerto Barrios! 🌱🚴‍♀️ Únete al cambio: ${window.location.origin}`;
    const url = `https://wa.me/?text=${encodeURIComponent(texto)}`;
    window.open(url, '_blank');
}
</script>
@endpush