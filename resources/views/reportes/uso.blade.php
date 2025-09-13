@extends('layouts.app')

@section('title', 'Reporte de Uso - EcoBici')

@push('styles')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 5px;
        position: relative;
        z-index: 1;
    }

    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 25px;
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
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .ranking-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        margin-bottom: 15px;
        overflow: hidden;
    }

    .ranking-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .ranking-position {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        font-size: 1.1rem;
    }

    .pos-1 { background: linear-gradient(45deg, #ffd700, #ffed4e); color: #8b5a00; }
    .pos-2 { background: linear-gradient(45deg, #c0c0c0, #e5e5e5); color: #4a4a4a; }
    .pos-3 { background: linear-gradient(45deg, #cd7f32, #deb887); color: #5d4e37; }
    .pos-other { background: linear-gradient(45deg, #6b7280, #9ca3af); }

    .bike-status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-disponible { background: #dcfce7; color: #166534; }
    .status-en-uso { background: #fef3c7; color: #92400e; }
    .status-mantenimiento { background: #fee2e2; color: #991b1b; }

    .progress-usage {
        height: 8px;
        border-radius: 4px;
        background: #e5e7eb;
        overflow: hidden;
    }

    .progress-usage .progress-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 1s ease-in-out;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 15px;
    }

    .export-btn {
        background: linear-gradient(45deg, #10b981, #34d399);
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .bike-type-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }

    .bike-tradicional { background: #10b981; }
    .bike-electrica { background: #3b82f6; }

    .time-period-selector {
        display: flex;
        background: #f1f5f9;
        border-radius: 10px;
        padding: 4px;
        margin-bottom: 20px;
    }

    .period-btn {
        flex: 1;
        border: none;
        background: transparent;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .period-btn.active {
        background: white;
        color: #667eea;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Reporte de Uso de Bicicletas
                    </h1>
                    <p class="text-muted mb-0">Análisis detallado del uso del sistema EcoBici</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn export-btn dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i>
                            Exportar
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('reportes.exportar', 'uso') }}?formato=json">
                                <i class="fas fa-file-code me-2"></i>JSON
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('reportes.exportar', 'uso') }}?formato=csv">
                                <i class="fas fa-file-csv me-2"></i>CSV
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de fecha -->
    <div class="filter-section">
        <form method="GET" action="{{ route('reportes.uso') }}" class="row align-items-end">
            <div class="col-md-4">
                <label for="fecha_inicio" class="form-label fw-bold">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Fecha Inicio
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
                    Fecha Fin
                </label>
                <input type="date" 
                       class="form-control date-filter" 
                       id="fecha_fin" 
                       name="fecha_fin" 
                       value="{{ $fechaFin ?? '' }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>
                    Aplicar Filtros
                </button>
            </div>
        </form>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number" data-counter="{{ $usosPorDia->sum('total_usos') }}">
                    {{ number_format($usosPorDia->sum('total_usos')) }}
                </div>
                <div class="stats-label">Total de Usos</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number" data-counter="{{ $usosPorDia->sum('usuarios_unicos') }}">
                    {{ number_format($usosPorDia->sum('usuarios_unicos')) }}
                </div>
                <div class="stats-label">Usuarios Únicos</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number" data-counter="{{ round($usosPorDia->sum('total_minutos') / 60) }}">
                    {{ number_format(round($usosPorDia->sum('total_minutos') / 60)) }}
                </div>
                <div class="stats-label">Horas Totales</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number" data-counter="{{ $usosPorDia->count() > 0 ? round($usosPorDia->sum('total_usos') / $usosPorDia->count()) : 0 }}">
                    {{ $usosPorDia->count() > 0 ? number_format(round($usosPorDia->sum('total_usos') / $usosPorDia->count())) : '0' }}
                </div>
                <div class="stats-label">Promedio Diario</div>
            </div>
        </div>
    </div>

    <!-- Gráfico de uso por día -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Uso Diario de Bicicletas
                    </h4>
                    <div class="time-period-selector">
                        <button class="period-btn active" onclick="cambiarPeriodo('diario')">Diario</button>
                        <button class="period-btn" onclick="cambiarPeriodo('semanal')">Semanal</button>
                        <button class="period-btn" onclick="cambiarPeriodo('mensual')">Mensual</button>
                    </div>
                </div>
                <canvas id="usoChart" width="400" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Estadísticas por tipo de bicicleta -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="chart-container">
                <h4 class="mb-4">
                    <i class="fas fa-bicycle me-2 text-success"></i>
                    Uso por Tipo de Bicicleta
                </h4>
                
                @if($estatisticasPorTipo && $estatisticasPorTipo->count() > 0)
                    <div class="row">
                        @foreach($estatisticasPorTipo as $tipo)
                            <div class="col-md-4 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <span class="bike-type-indicator bike-{{ $tipo->tipo }}"></span>
                                            <h5 class="d-inline">
                                                @if($tipo->tipo === 'tradicional')
                                                    🚲 Tradicionales
                                                @else
                                                    ⚡ Eléctricas
                                                @endif
                                            </h5>
                                        </div>
                                        
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h4 class="text-primary mb-1">{{ number_format($tipo->total_usos) }}</h4>
                                                <small class="text-muted">Usos totales</small>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="text-success mb-1">{{ number_format($tipo->promedio_minutos ?? 0, 0) }}</h4>
                                                <small class="text-muted">Min. promedio</small>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <div class="progress-usage">
                                                <div class="progress-bar bg-{{ $tipo->tipo === 'tradicional' ? 'success' : 'primary' }}" 
                                                     style="width: {{ $estatisticasPorTipo->max('total_usos') > 0 ? ($tipo->total_usos / $estatisticasPorTipo->max('total_usos')) * 100 : 0 }}%"></div>
                                            </div>
                                            <small class="text-muted">
                                                {{ $estatisticasPorTipo->sum('total_usos') > 0 ? round(($tipo->total_usos / $estatisticasPorTipo->sum('total_usos')) * 100) : 0 }}% del total
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Gráfico circular -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <canvas id="tipoChart" width="200" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bicycle fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted mb-3">No hay datos de uso disponibles</h5>
                        <p class="text-muted">Los datos aparecerán aquí una vez que haya actividad en el sistema</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Rankings -->
    <div class="row mb-5">
        <!-- Bicicletas más usadas -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <h5 class="mb-4">
                    <i class="fas fa-trophy me-2 text-warning"></i>
                    Top 10 Bicicletas Más Usadas
                </h5>
                
                @if($bicicletasMasUsadas && $bicicletasMasUsadas->count() > 0)
                    @foreach($bicicletasMasUsadas as $index => $bicicleta)
                        <div class="ranking-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="ranking-position pos-{{ $index < 3 ? $index + 1 : 'other' }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $bicicleta->codigo }}</h6>
                                                <div class="d-flex align-items-center">
                                                    <span class="bike-type-indicator bike-{{ $bicicleta->tipo }}"></span>
                                                    <small class="text-muted">{{ ucfirst($bicicleta->tipo) }}</small>
                                                    <span class="bike-status-badge status-{{ $bicicleta->estado }} ms-2">
                                                        {{ $bicicleta->estado }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <h5 class="text-primary mb-0">{{ number_format($bicicleta->total_usos) }}</h5>
                                                <small class="text-muted">usos</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-bicycle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay datos de bicicletas disponibles</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Usuarios más activos -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <h5 class="mb-4">
                    <i class="fas fa-users me-2 text-info"></i>
                    Top 10 Usuarios Más Activos
                </h5>
                
                @if($usuariosMasActivos && $usuariosMasActivos->count() > 0)
                    @foreach($usuariosMasActivos as $index => $usuario)
                        <div class="ranking-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="ranking-position pos-{{ $index < 3 ? $index + 1 : 'other' }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $usuario->nombre }}</h6>
                                                <small class="text-muted">{{ $usuario->email }}</small>
                                            </div>
                                            <div class="text-end">
                                                <h5 class="text-success mb-0">{{ number_format($usuario->total_usos) }}</h5>
                                                <small class="text-muted">recorridos</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay datos de usuarios disponibles</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let usoChart = null;
let tipoChart = null;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar gráficos
    initUsoChart();
    initTipoChart();
    
    // Animar contadores
    animateCounters();
});

function initUsoChart() {
    const ctx = document.getElementById('usoChart');
    if (!ctx) return;
    
    const usosPorDia = @json($usosPorDia);
    const labels = usosPorDia.map(dia => {
        const fecha = new Date(dia.fecha);
        return fecha.toLocaleDateString('es-GT', { 
            month: 'short', 
            day: 'numeric' 
        });
    });
    const data = usosPorDia.map(dia => dia.total_usos);
    
    usoChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Usos por día',
                data: data,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#667eea',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        color: '#64748b'
                    }
                },
                x: {
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        color: '#64748b'
                    }
                }
            }
        }
    });
}

function initTipoChart() {
    const ctx = document.getElementById('tipoChart');
    if (!ctx) return;
    
    const estadisticasPorTipo = @json($estatisticasPorTipo ?? []);
    if (!estadisticasPorTipo || estadisticasPorTipo.length === 0) return;
    
    const labels = estadisticasPorTipo.map(tipo => 
        tipo.tipo === 'tradicional' ? 'Tradicionales' : 'Eléctricas'
    );
    const data = estadisticasPorTipo.map(tipo => tipo.total_usos);
    const colors = estadisticasPorTipo.map(tipo => 
        tipo.tipo === 'tradicional' ? '#10b981' : '#3b82f6'
    );
    
    tipoChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 3,
                hoverBorderWidth: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${context.raw} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function animateCounters() {
    document.querySelectorAll('[data-counter]').forEach(counter => {
        const target = parseInt(counter.dataset.counter);
        animateNumber(counter, 0, target, 2000);
    });
}

function animateNumber(element, start, end, duration) {
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(start + (end - start) * easeOutCubic(progress));
        element.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
}

function cambiarPeriodo(periodo) {
    // Actualizar botones activos
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Aquí se implementaría la lógica para cambiar el período del gráfico
    // Por ahora solo mostramos una notificación
    if (typeof EcoBici !== 'undefined' && EcoBici.mostrarNotificacion) {
        EcoBici.mostrarNotificacion(`Vista cambiada a: ${periodo}`, 'info', 2000);
    }
}

// Función para exportar datos
function exportarDatos(formato) {
    const params = new URLSearchParams(window.location.search);
    params.set('formato', formato);
    
    const url = '{{ route("reportes.exportar", "uso") }}?' + params.toString();
    window.open(url, '_blank');
}

// Auto-refresh cada 5 minutos
setInterval(() => {
    if (document.visibilityState === 'visible') {
        location.reload();
    }
}, 300000);
</script>
@endpush