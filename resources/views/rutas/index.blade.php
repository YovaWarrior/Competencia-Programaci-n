@extends('layouts.app')

@section('title', 'Mis Rutas - EcoBici')

@push('styles')
<style>
    .ruta-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .ruta-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .ruta-favorita {
        border-left: 5px solid #f59e0b;
    }

    .dificultad-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
    }

    .ruta-stats {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        border-radius: 10px;
        padding: 15px;
        margin: 10px 0;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }

    .floating-create-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        border-radius: 50px;
        padding: 15px 25px;
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .filter-tabs {
        background: white;
        border-radius: 15px;
        padding: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .filter-tab {
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .filter-tab.active {
        background: linear-gradient(45deg, var(--ecobici-azul), var(--ecobici-celeste));
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .route-map-preview {
        height: 150px;
        background: linear-gradient(135deg, #e0f2fe, #b3e5fc);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .route-points {
        position: absolute;
        width: 100%;
        height: 100%;
    }

    .route-point {
        position: absolute;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--ecobici-azul);
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .route-line {
        position: absolute;
        height: 3px;
        background: linear-gradient(90deg, var(--ecobici-azul), var(--ecobici-verde));
        border-radius: 2px;
        top: 50%;
        transform: translateY(-50%);
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
                        <i class="fas fa-route text-primary me-2"></i>
                        Mis Rutas EcoBici
                    </h1>
                    <p class="text-muted mb-0">Gestiona y explora tus rutas personalizadas</p>
                </div>
                <div>
                    <a href="{{ route('rutas.crear') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nueva Ruta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-tabs d-inline-flex">
                <button class="filter-tab active" data-filter="todas">
                    <i class="fas fa-list me-1"></i>
                    Todas ({{ $rutas->count() + $rutasPublicas->count() }})
                </button>
                <button class="filter-tab" data-filter="mis-rutas">
                    <i class="fas fa-user me-1"></i>
                    Mis Rutas ({{ $rutas->count() }})
                </button>
                <button class="filter-tab" data-filter="favoritas">
                    <i class="fas fa-heart me-1"></i>
                    Favoritas ({{ $rutas->where('favorita', true)->count() }})
                </button>
                <button class="filter-tab" data-filter="publicas">
                    <i class="fas fa-globe me-1"></i>
                    Públicas ({{ $rutasPublicas->count() }})
                </button>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body">
                    <i class="fas fa-route fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1">{{ $rutas->count() }}</h4>
                    <small class="text-muted">Rutas Creadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body">
                    <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1">{{ $rutas->where('favorita', true)->count() }}</h4>
                    <small class="text-muted">Favoritas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body">
                    <i class="fas fa-road fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">{{ number_format($rutas->sum('distancia_km'), 1) }}</h4>
                    <small class="text-muted">Km Planificados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body">
                    <i class="fas fa-leaf fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1">{{ number_format($rutas->sum('co2_reducido_estimado'), 1) }}</h4>
                    <small class="text-muted">Kg CO₂ Estimado</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Mis Rutas -->
    <div class="filter-section" data-section="mis-rutas">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">
                    <i class="fas fa-user text-primary me-2"></i>
                    Mis Rutas Personalizadas
                </h4>
            </div>
        </div>

        @if($rutas->count() > 0)
            <div class="row">
                @foreach($rutas as $ruta)
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card ruta-card {{ $ruta->favorita ? 'ruta-favorita' : '' }}">
                            <!-- Badge de dificultad -->
                            <div class="dificultad-badge">
                                @if($ruta->dificultad === 'facil')
                                    <span class="badge bg-success">😊 Fácil</span>
                                @elseif($ruta->dificultad === 'moderada')
                                    <span class="badge bg-warning">😅 Moderada</span>
                                @else
                                    <span class="badge bg-danger">😰 Difícil</span>
                                @endif
                            </div>

                            <!-- Preview del mapa -->
                            <div class="route-map-preview">
                                <div class="route-points">
                                    <div class="route-point" style="left: 20%; top: 40%;"></div>
                                    <div class="route-line" style="left: 20%; width: 60%;"></div>
                                    <div class="route-point" style="right: 20%; top: 60%;"></div>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-map fa-3x text-white opacity-75"></i>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Título y descripción -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $ruta->nombre }}</h5>
                                    <button class="btn btn-sm btn-outline-{{ $ruta->favorita ? 'warning' : 'secondary' }}" 
                                            onclick="toggleFavorita({{ $ruta->id }})">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                                
                                @if($ruta->descripcion)
                                    <p class="text-muted small mb-3">{{ Str::limit($ruta->descripcion, 80) }}</p>
                                @endif

                                <!-- Información de estaciones -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-play-circle text-success me-2"></i>
                                        <small class="text-muted">Desde: {{ Str::limit($ruta->estacionInicio->nombre, 25) }}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-stop-circle text-danger me-2"></i>
                                        <small class="text-muted">Hasta: {{ Str::limit($ruta->estacionFin->nombre, 25) }}</small>
                                    </div>
                                </div>

                                <!-- Estadísticas -->
                                <div class="ruta-stats">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-icon bg-primary text-white">
                                                    <i class="fas fa-road"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $ruta->distancia_km }} km</strong>
                                                    <br><small class="text-muted">Distancia</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-icon bg-success text-white">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $ruta->tiempo_estimado_minutos }}m</strong>
                                                    <br><small class="text-muted">Tiempo</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-icon bg-warning text-white">
                                                    <i class="fas fa-leaf"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ number_format($ruta->co2_reducido_estimado, 1) }}kg</strong>
                                                    <br><small class="text-muted">CO₂ Reducido</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-icon bg-info text-white">
                                                    <i class="fas fa-bicycle"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $ruta->veces_usada ?? 0 }}</strong>
                                                    <br><small class="text-muted">Veces usada</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('rutas.show', $ruta->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>
                                        Ver Detalles
                                    </a>
                                    <a href="{{ route('bicicletas.seleccionar') }}?ruta={{ $ruta->id }}" class="btn btn-success btn-sm flex-fill">
                                        <i class="fas fa-bicycle me-1"></i>
                                        Usar Ruta
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm" onclick="eliminarRuta({{ $ruta->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-route fa-4x mb-3"></i>
                <h5 class="mb-3">Aún no has creado rutas personalizadas</h5>
                <p class="mb-4">Comienza creando tu primera ruta para optimizar tus recorridos en EcoBici</p>
                <a href="{{ route('rutas.crear') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    Crear Mi Primera Ruta
                </a>
            </div>
        @endif
    </div>

    <!-- Sección: Rutas Públicas -->
    <div class="filter-section" data-section="publicas">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">
                    <i class="fas fa-globe text-info me-2"></i>
                    Rutas Populares de la Comunidad
                </h4>
            </div>
        </div>

        @if($rutasPublicas->count() > 0)
            <div class="row">
                @foreach($rutasPublicas as $rutaPublica)
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card ruta-card">
                            <!-- Badge de dificultad -->
                            <div class="dificultad-badge">
                                @if($rutaPublica->dificultad === 'facil')
                                    <span class="badge bg-success">😊 Fácil</span>
                                @elseif($rutaPublica->dificultad === 'moderada')
                                    <span class="badge bg-warning">😅 Moderada</span>
                                @else
                                    <span class="badge bg-danger">😰 Difícil</span>
                                @endif
                            </div>

                            <!-- Preview del mapa -->
                            <div class="route-map-preview">
                                <div class="route-points">
                                    <div class="route-point" style="left: 15%; top: 30%;"></div>
                                    <div class="route-line" style="left: 15%; width: 70%;"></div>
                                    <div class="route-point" style="right: 15%; top: 70%;"></div>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-users fa-2x text-white opacity-75"></i>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Título y creador -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $rutaPublica->nombre }}</h5>
                                    <span class="badge bg-info">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $rutaPublica->user->nombre }}
                                    </span>
                                </div>
                                
                                @if($rutaPublica->descripcion)
                                    <p class="text-muted small mb-3">{{ Str::limit($rutaPublica->descripcion, 80) }}</p>
                                @endif

                                <!-- Información de estaciones -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-play-circle text-success me-2"></i>
                                        <small class="text-muted">{{ Str::limit($rutaPublica->estacionInicio->nombre, 25) }}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-stop-circle text-danger me-2"></i>
                                        <small class="text-muted">{{ Str::limit($rutaPublica->estacionFin->nombre, 25) }}</small>
                                    </div>
                                </div>

                                <!-- Estadísticas simplificadas -->
                                <div class="ruta-stats">
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <strong class="text-primary">{{ $rutaPublica->distancia_km }}km</strong>
                                            <br><small class="text-muted">Distancia</small>
                                        </div>
                                        <div class="col-3">
                                            <strong class="text-success">{{ $rutaPublica->tiempo_estimado_minutos }}m</strong>
                                            <br><small class="text-muted">Tiempo</small>
                                        </div>
                                        <div class="col-3">
                                            <strong class="text-warning">{{ number_format($rutaPublica->co2_reducido_estimado, 1) }}</strong>
                                            <br><small class="text-muted">CO₂ kg</small>
                                        </div>
                                        <div class="col-3">
                                            <strong class="text-info">{{ $rutaPublica->veces_usada ?? 0 }}</strong>
                                            <br><small class="text-muted">Usos</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('rutas.show', $rutaPublica->id) }}" class="btn btn-outline-info btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>
                                        Ver Ruta
                                    </a>
                                    <a href="{{ route('bicicletas.seleccionar') }}?ruta={{ $rutaPublica->id }}" class="btn btn-success btn-sm flex-fill">
                                        <i class="fas fa-bicycle me-1"></i>
                                        Usar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-globe fa-4x mb-3"></i>
                <h5 class="mb-3">No hay rutas públicas disponibles</h5>
                <p class="mb-4">Sé el primero en crear una ruta para la comunidad</p>
            </div>
        @endif
    </div>
</div>

<!-- Botón flotante para crear ruta -->
<a href="{{ route('rutas.crear') }}" class="floating-create-btn btn btn-primary d-md-none">
    <i class="fas fa-plus me-2"></i>
    Nueva Ruta
</a>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash text-danger me-2"></i>
                    Eliminar Ruta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta ruta?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta acción no se puede deshacer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejo de filtros
    const filterTabs = document.querySelectorAll('.filter-tab');
    const filterSections = document.querySelectorAll('.filter-section');

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Actualizar tabs activos
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Mostrar/ocultar secciones
            filterSections.forEach(section => {
                section.style.display = 'none';
            });
            
            if (filter === 'todas') {
                filterSections.forEach(section => {
                    section.style.display = 'block';
                });
            } else if (filter === 'favoritas') {
                // Mostrar solo rutas favoritas
                document.querySelectorAll('.ruta-favorita').forEach(card => {
                    card.closest('.col-lg-6, .col-xl-4').style.display = 'block';
                });
                document.querySelectorAll('.ruta-card:not(.ruta-favorita)').forEach(card => {
                    card.closest('.col-lg-6, .col-xl-4').style.display = 'none';
                });
            } else {
                const targetSection = document.querySelector(`[data-section="${filter}"]`);
                if (targetSection) {
                    targetSection.style.display = 'block';
                }
            }
        });
    });

    // Animaciones de entrada
    const cards = document.querySelectorAll('.ruta-card');
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

// Función para toggle favorita
function toggleFavorita(rutaId) {
    fetch(`/rutas/${rutaId}/favorita`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof EcoBici !== 'undefined') {
            EcoBici.mostrarNotificacion('Error al actualizar favorita', 'danger');
        }
    });
}

// Función para eliminar ruta
function eliminarRuta(rutaId) {
    const form = document.getElementById('deleteForm');
    form.action = `/rutas/${rutaId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush