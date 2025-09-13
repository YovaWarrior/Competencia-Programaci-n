@extends('layouts.app')

@section('title', 'Mapa de Estaciones - EcoBici')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>

<style>
    #mapa-estaciones {
        height: 600px;
        width: 100%;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .mapa-container {
        position: relative;
        margin-bottom: 2rem;
    }

    .mapa-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .control-btn {
        background: white;
        border: none;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .control-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .estacion-info {
        background: white;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border: none;
        max-width: 350px;
    }

    .estacion-header {
        display: flex;
        align-items: center;
        justify-content: between;
        margin-bottom: 10px;
    }

    .estacion-tipo-badge {
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 20px;
    }

    .bicicletas-counter {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
    }

    .counter-item {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        background: #f8f9fa;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    .legend {
        background: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }

    .legend-marker {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: white;
        font-weight: bold;
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 15px;
    }

    .loading-spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1001;
    }

    @media (max-width: 768px) {
        #mapa-estaciones {
            height: 400px;
        }
        
        .mapa-controls {
            position: static;
            display: flex;
            flex-direction: row;
            justify-content: center;
            margin-bottom: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center">
                <h2 class="mb-3">
                    <i class="fas fa-map-marked-alt text-primary me-2"></i>
                    Mapa de Estaciones EcoBici
                </h2>
                <p class="text-muted lead">
                    Encuentra la estación más cercana y revisa la disponibilidad de bicicletas en tiempo real
                </p>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                    <h4 id="total-estaciones">{{ count($estaciones) }}</h4>
                    <small>Estaciones Activas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-bicycle fa-2x mb-2"></i>
                    <h4 id="total-bicicletas">{{ $estaciones->sum('bicicletas_disponibles') }}</h4>
                    <small>Bicicletas Disponibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-bolt fa-2x mb-2"></i>
                    <h4 id="estaciones-carga">{{ $estaciones->where('tiene_cargador_electrico', true)->count() }}</h4>
                    <small>Con Carga Eléctrica</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h4>24/7</h4>
                    <small>Disponibilidad</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Mapa -->
        <div class="col-lg-9 mb-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="mapa-container">
                        <!-- Controles del mapa -->
                        <div class="mapa-controls d-none d-md-flex">
                            <button class="control-btn" onclick="EcoBici.centrarMapa()" title="Centrar mapa">
                                <i class="fas fa-crosshairs text-primary"></i>
                            </button>
                            <button class="control-btn" onclick="EcoBici.buscarMiUbicacion()" title="Mi ubicación">
                                <i class="fas fa-location-arrow text-success"></i>
                            </button>
                            <button class="control-btn" onclick="EcoBici.actualizarEstaciones()" title="Actualizar">
                                <i class="fas fa-sync-alt text-info"></i>
                            </button>
                        </div>

                        <!-- Spinner de carga -->
                        <div id="mapa-loading" class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando mapa...</span>
                            </div>
                        </div>

                        <!-- Contenedor del mapa -->
                        <div id="mapa-estaciones"></div>
                    </div>

                    <!-- Controles móviles -->
                    <div class="mapa-controls d-md-none">
                        <button class="control-btn" onclick="EcoBici.centrarMapa()">
                            <i class="fas fa-crosshairs text-primary me-1"></i> Centrar
                        </button>
                        <button class="control-btn" onclick="EcoBici.buscarMiUbicacion()">
                            <i class="fas fa-location-arrow text-success me-1"></i> Mi Ubicación
                        </button>
                        <button class="control-btn" onclick="EcoBici.actualizarEstaciones()">
                            <i class="fas fa-sync-alt text-info me-1"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-lg-3">
            <!-- Leyenda -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Leyenda del Mapa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-marker" style="background: #28a745;">
                                <i class="fas fa-bicycle"></i>
                            </div>
                            <span>Estación con bicicletas disponibles</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-marker" style="background: #ffc107;">
                                <i class="fas fa-exclamation"></i>
                            </div>
                            <span>Pocas bicicletas disponibles</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-marker" style="background: #dc3545;">
                                <i class="fas fa-times"></i>
                            </div>
                            <span>Sin bicicletas disponibles</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-marker" style="background: #17a2b8;">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <span>Estación con carga eléctrica</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de estaciones -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Estaciones Cercanas
                    </h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div id="lista-estaciones">
                        @foreach($estaciones->sortByDesc('bicicletas_disponibles')->take(10) as $estacion)
                            <div class="estacion-item mb-3 p-3 border rounded" 
                                 onclick="EcoBici.enfocarEstacion({{ $estacion->latitud }}, {{ $estacion->longitud }}, '{{ $estacion->nombre }}')"
                                 style="cursor: pointer; transition: all 0.3s ease;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $estacion->nombre }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ Str::limit($estacion->direccion, 40) }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $estacion->bicicletas_disponibles > 5 ? 'success' : ($estacion->bicicletas_disponibles > 0 ? 'warning' : 'danger') }}">
                                        {{ $estacion->bicicletas_disponibles }}
                                    </span>
                                </div>
                                
                                <div class="mt-2">
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-light text-dark">
                                            {{ ucfirst($estacion->tipo) }}
                                        </span>
                                        @if($estacion->tiene_cargador_electrico)
                                            <span class="badge bg-info">
                                                <i class="fas fa-bolt"></i> Eléctrica
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Filtros rápidos -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Filtros Rápidos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success btn-sm" onclick="EcoBici.filtrarEstaciones('disponibles')">
                            <i class="fas fa-bicycle me-1"></i>
                            Solo con Bicicletas
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="EcoBici.filtrarEstaciones('electricas')">
                            <i class="fas fa-bolt me-1"></i>
                            Con Carga Eléctrica
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="EcoBici.filtrarEstaciones('todas')">
                            <i class="fas fa-list me-1"></i>
                            Mostrar Todas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row mt-4">
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
                            <a href="{{ route('rutas.crear') }}" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-route fa-2x mb-2"></i><br>
                                <span>Crear Ruta</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-tachometer-alt fa-2x mb-2"></i><br>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('membresias.index') }}" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-id-card fa-2x mb-2"></i><br>
                                <span>Membresías</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de información de estación -->
<div class="modal fade" id="estacionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <span id="modal-estacion-nombre">Información de Estación</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-estacion-contenido">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" id="modal-btn-usar" class="btn btn-success">
                    <i class="fas fa-bicycle me-1"></i>
                    Usar Bicicleta
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<script>
// Configurar datos del servidor
if (typeof window.EcoBici !== 'undefined') {
    // Pasar datos de estaciones desde el servidor
    window.EcoBici.estacionesData = @json($estaciones);
    
    // Configurar endpoints
    window.EcoBici.config.apiEndpoints = {
        ...window.EcoBici.config.apiEndpoints,
        mapaEstaciones: '{{ route("api.mapa.estaciones") }}'
    };
    
    console.log('📊 Datos de estaciones cargados desde servidor:', window.EcoBici.estacionesData.length);
} else {
    console.warn('⚠️ EcoBici no está definido. Verificar carga de app.js');
}

// Funciones específicas del modal
EcoBici.verDetallesEstacion = function(estacionId) {
    const estacion = this.estacionesData.find(e => e.id === estacionId);
    if (!estacion) {
        this.mostrarNotificacion('Estación no encontrada', 'warning');
        return;
    }

    document.getElementById('modal-estacion-nombre').textContent = estacion.nombre;
    document.getElementById('modal-btn-usar').href = `/bicicletas/seleccionar?estacion=${estacionId}`;
    
    const contenido = `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-info-circle me-2"></i>Información General</h6>
                <p><strong>Código:</strong> ${estacion.codigo || 'ECO-PB' + estacion.id.toString().padStart(3, '0')}</p>
                <p><strong>Tipo:</strong> ${estacion.tipo}</p>
                <p><strong>Capacidad:</strong> ${estacion.capacidad_total} espacios</p>
                <p><strong>Estado:</strong> <span class="badge bg-success">Activa</span></p>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-bicycle me-2"></i>Disponibilidad</h6>
                <p><strong>Bicicletas disponibles:</strong> 
                    <span class="badge bg-${estacion.bicicletas_disponibles > 5 ? 'success' : estacion.bicicletas_disponibles > 0 ? 'warning' : 'danger'}">
                        ${estacion.bicicletas_disponibles}
                    </span>
                </p>
                <p><strong>Espacios libres:</strong> ${estacion.capacidad_total - estacion.bicicletas_disponibles}</p>
                ${estacion.tiene_cargador_electrico ? 
                    '<p><strong>Carga eléctrica:</strong> <span class="badge bg-info"><i class="fas fa-bolt"></i> Disponible</span></p>' : 
                    '<p><strong>Carga eléctrica:</strong> <span class="badge bg-secondary">No disponible</span></p>'
                }
            </div>
        </div>
        <hr>
        <h6><i class="fas fa-map-marker-alt me-2"></i>Ubicación</h6>
        <p>${estacion.direccion}</p>
        <p><small class="text-muted">Coordenadas: ${estacion.latitud}, ${estacion.longitud}</small></p>
    `;
    
    document.getElementById('modal-estacion-contenido').innerHTML = contenido;
    
    // Usar Bootstrap 5 modal
    const modal = new bootstrap.Modal(document.getElementById('estacionModal'));
    modal.show();
};

// Función específica para filtrar en la lista lateral
EcoBici.actualizarListaEstaciones = function() {
    const lista = document.getElementById('lista-estaciones');
    if (!lista) return;
    
    const estacionesOrdenadas = this.estacionesData
        .sort((a, b) => (b.bicicletas_disponibles || 0) - (a.bicicletas_disponibles || 0))
        .slice(0, 10);
    
    lista.innerHTML = '';
    
    estacionesOrdenadas.forEach(estacion => {
        const disponibles = estacion.bicicletas_disponibles || 0;
        const item = document.createElement('div');
        item.className = 'estacion-item mb-3 p-3 border rounded';
        item.style.cursor = 'pointer';
        item.onclick = () => this.enfocarEstacion(estacion.latitud, estacion.longitud, estacion.nombre);
        
        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${estacion.nombre}</h6>
                    <small class="text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        ${estacion.direccion.substring(0, 40)}...
                    </small>
                </div>
                <span class="badge bg-${disponibles > 5 ? 'success' : (disponibles > 0 ? 'warning' : 'danger')}">
                    ${disponibles}
                </span>
            </div>
            <div class="mt-2">
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark">${estacion.tipo}</span>
                    ${estacion.tiene_cargador_electrico ? 
                        '<span class="badge bg-info"><i class="fas fa-bolt"></i> Eléctrica</span>' : 
                        ''
                    }
                </div>
            </div>
        `;
        
        lista.appendChild(item);
    });
};

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que EcoBici esté completamente inicializado
    setTimeout(() => {
        if (typeof EcoBici !== 'undefined' && EcoBici.mapa) {
            EcoBici.actualizarListaEstaciones();
        }
    }, 3000);
});
</script>
@endpush