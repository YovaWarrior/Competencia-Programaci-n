@extends('layouts.app')

@section('title', 'Crear Ruta - EcoBici')

@push('styles')
<!-- Leaflet CSS para el mapa -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    #mapa-ruta {
        height: 400px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .form-section {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        border: none;
    }

    .section-title {
        color: var(--ecobici-azul);
        border-bottom: 2px solid var(--ecobici-azul-claro);
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .dificultad-option {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }

    .dificultad-option:hover {
        border-color: var(--ecobici-azul);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(37, 99, 235, 0.1);
    }

    .dificultad-option.selected {
        border-color: var(--ecobici-verde);
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    }

    .dificultad-icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .route-progress {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .progress-step {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--ecobici-azul);
        color: white;
        font-weight: bold;
        margin-right: 15px;
        font-size: 0.9rem;
    }

    .step-number.completed {
        background: var(--ecobici-verde);
    }

    .step-number.current {
        background: var(--ecobici-celeste);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .estacion-selector {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .estacion-selector:hover {
        border-color: var(--ecobici-azul);
        background: #f8fafc;
    }

    .estacion-selector.selected {
        border-color: var(--ecobici-verde);
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    }

    .route-stats-preview {
        background: linear-gradient(135deg, #e0f2fe, #b3e5fc);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }

    .stat-preview {
        margin-bottom: 15px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--ecobici-azul);
    }

    .floating-save-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        border-radius: 50px;
        padding: 15px 25px;
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
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
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Crear Nueva Ruta
                    </h1>
                    <p class="text-muted mb-0">Diseña tu ruta personalizada para optimizar tus recorridos</p>
                </div>
                <div>
                    <a href="{{ route('rutas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver a Rutas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Progreso de creación -->
    <div class="route-progress">
        <h6 class="mb-3">
            <i class="fas fa-tasks me-2"></i>
            Progreso de Creación
        </h6>
        <div class="progress-step">
            <div class="step-number current" id="step-1">1</div>
            <span>Información básica de la ruta</span>
        </div>
        <div class="progress-step">
            <div class="step-number" id="step-2">2</div>
            <span>Seleccionar estaciones de inicio y fin</span>
        </div>
        <div class="progress-step">
            <div class="step-number" id="step-3">3</div>
            <span>Trazar ruta en el mapa</span>
        </div>
        <div class="progress-step">
            <div class="step-number" id="step-4">4</div>
            <span>Configurar detalles y guardar</span>
        </div>
    </div>

    <form method="POST" action="{{ route('rutas.store') }}" id="ruta-form">
        @csrf
        
        <!-- Sección 1: Información Básica -->
        <div class="form-section" id="seccion-1">
            <h4 class="section-title">
                <i class="fas fa-info-circle me-2"></i>
                Información Básica
            </h4>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">
                            <i class="fas fa-tag me-1"></i>
                            Nombre de la Ruta
                        </label>
                        <input type="text" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" 
                               name="nombre" 
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Ruta al trabajo por la costa"
                               required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-bold">
                            <i class="fas fa-edit me-1"></i>
                            Descripción (Opcional)
                        </label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="3"
                                  placeholder="Describe tu ruta, puntos de interés, recomendaciones...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="route-stats-preview">
                        <h6 class="mb-3">
                            <i class="fas fa-chart-line me-2"></i>
                            Vista Previa
                        </h6>
                        <div class="stat-preview">
                            <div class="stat-value" id="preview-distancia">0.0 km</div>
                            <small class="text-muted">Distancia estimada</small>
                        </div>
                        <div class="stat-preview">
                            <div class="stat-value" id="preview-tiempo">0 min</div>
                            <small class="text-muted">Tiempo estimado</small>
                        </div>
                        <div class="stat-preview">
                            <div class="stat-value" id="preview-co2">0.0 kg</div>
                            <small class="text-muted">CO₂ reducido</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-end">
                <button type="button" class="btn btn-primary" onclick="siguientePaso(2)">
                    Siguiente: Estaciones
                    <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Sección 2: Selección de Estaciones -->
        <div class="form-section" id="seccion-2" style="display: none;">
            <h4 class="section-title">
                <i class="fas fa-map-marker-alt me-2"></i>
                Estaciones de Inicio y Fin
            </h4>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3 text-success">
                        <i class="fas fa-play-circle me-2"></i>
                        Estación de Inicio
                    </h6>
                    @foreach($estaciones as $estacion)
                        <div class="estacion-selector mb-2" 
                             data-tipo="inicio" 
                             data-id="{{ $estacion->id }}"
                             data-lat="{{ $estacion->latitud }}"
                             data-lng="{{ $estacion->longitud }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $estacion->nombre }}</h6>
                                    <small class="text-muted">{{ Str::limit($estacion->direccion, 40) }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-info">{{ $estacion->tipo }}</span>
                                    @if($estacion->tiene_cargador_electrico)
                                        <br><span class="badge bg-warning mt-1">⚡ Eléctrica</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <input type="hidden" name="estacion_inicio_id" id="estacion_inicio_id" required>
                </div>
                
                <div class="col-md-6">
                    <h6 class="mb-3 text-danger">
                        <i class="fas fa-stop-circle me-2"></i>
                        Estación de Fin
                    </h6>
                    @foreach($estaciones as $estacion)
                        <div class="estacion-selector mb-2" 
                             data-tipo="fin" 
                             data-id="{{ $estacion->id }}"
                             data-lat="{{ $estacion->latitud }}"
                             data-lng="{{ $estacion->longitud }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $estacion->nombre }}</h6>
                                    <small class="text-muted">{{ Str::limit($estacion->direccion, 40) }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-info">{{ $estacion->tipo }}</span>
                                    @if($estacion->tiene_cargador_electrico)
                                        <br><span class="badge bg-warning mt-1">⚡ Eléctrica</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <input type="hidden" name="estacion_fin_id" id="estacion_fin_id" required>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary" onclick="anteriorPaso(1)">
                    <i class="fas fa-arrow-left me-2"></i>
                    Anterior
                </button>
                <button type="button" class="btn btn-primary" onclick="siguientePaso(3)" id="btn-siguiente-2" disabled>
                    Siguiente: Mapa
                    <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Sección 3: Mapa y Trazado -->
        <div class="form-section" id="seccion-3" style="display: none;">
            <h4 class="section-title">
                <i class="fas fa-map me-2"></i>
                Trazar Ruta en el Mapa
            </h4>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <div id="mapa-ruta"></div>
                        <div class="mt-2 text-center">
                            <button type="button" class="btn btn-info btn-sm" id="btn-calcular-ruta">
                                <i class="fas fa-route me-2"></i>
                                Calcular Ruta Automática
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="btn-limpiar-ruta">
                                <i class="fas fa-eraser me-2"></i>
                                Limpiar Ruta
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <h6>
                            <i class="fas fa-lightbulb me-2"></i>
                            Instrucciones
                        </h6>
                        <ul class="small mb-0">
                            <li>Haz clic en el mapa para agregar puntos a tu ruta</li>
                            <li>Los puntos se conectarán automáticamente</li>
                            <li>Usa "Calcular Ruta Automática" para una ruta directa</li>
                            <li>Puedes ajustar la ruta manualmente después</li>
                        </ul>
                    </div>
                    
                    <div class="route-stats-preview">
                        <h6 class="mb-3">Estadísticas de Ruta</h6>
                        <div class="stat-preview">
                            <div class="stat-value" id="distancia-calculada">0.0 km</div>
                            <small class="text-muted">Distancia total</small>
                        </div>
                        <div class="stat-preview">
                            <div class="stat-value" id="puntos-ruta">0</div>
                            <small class="text-muted">Puntos marcados</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="puntos_ruta" id="puntos_ruta" required>
            <input type="hidden" name="distancia_km" id="distancia_km" required>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary" onclick="anteriorPaso(2)">
                    <i class="fas fa-arrow-left me-2"></i>
                    Anterior
                </button>
                <button type="button" class="btn btn-primary" onclick="siguientePaso(4)" id="btn-siguiente-3" disabled>
                    Siguiente: Finalizar
                    <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Sección 4: Detalles Finales -->
        <div class="form-section" id="seccion-4" style="display: none;">
            <h4 class="section-title">
                <i class="fas fa-cog me-2"></i>
                Detalles Finales
            </h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tiempo_estimado_minutos" class="form-label fw-bold">
                            <i class="fas fa-clock me-1"></i>
                            Tiempo Estimado (minutos)
                        </label>
                        <input type="number" 
                               class="form-control @error('tiempo_estimado_minutos') is-invalid @enderror" 
                               id="tiempo_estimado_minutos" 
                               name="tiempo_estimado_minutos" 
                               value="{{ old('tiempo_estimado_minutos') }}"
                               min="1" 
                               max="300"
                               required>
                        <div class="form-text">Tiempo promedio en bicicleta tradicional</div>
                        @error('tiempo_estimado_minutos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-mountain me-1"></i>
                            Nivel de Dificultad
                        </label>
                        <div class="row">
                            <div class="col-4">
                                <div class="dificultad-option" data-dificultad="facil">
                                    <div class="dificultad-icon text-success">😊</div>
                                    <strong>Fácil</strong>
                                    <br><small class="text-muted">Terreno plano</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="dificultad-option" data-dificultad="moderada">
                                    <div class="dificultad-icon text-warning">😅</div>
                                    <strong>Moderada</strong>
                                    <br><small class="text-muted">Algunas colinas</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="dificultad-option" data-dificultad="dificil">
                                    <div class="dificultad-icon text-danger">😰</div>
                                    <strong>Difícil</strong>
                                    <br><small class="text-muted">Terreno exigente</small>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="dificultad" id="dificultad" required>
                        @error('dificultad')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Resumen final -->
            <div class="alert alert-success">
                <h6>
                    <i class="fas fa-check-circle me-2"></i>
                    Resumen de tu Ruta
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nombre:</strong> <span id="resumen-nombre">-</span><br>
                        <strong>Desde:</strong> <span id="resumen-inicio">-</span><br>
                        <strong>Hasta:</strong> <span id="resumen-fin">-</span><br>
                    </div>
                    <div class="col-md-6">
                        <strong>Distancia:</strong> <span id="resumen-distancia">0 km</span><br>
                        <strong>Tiempo:</strong> <span id="resumen-tiempo">0 min</span><br>
                        <strong>Dificultad:</strong> <span id="resumen-dificultad">-</span><br>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary" onclick="anteriorPaso(3)">
                    <i class="fas fa-arrow-left me-2"></i>
                    Anterior
                </button>
                <button type="submit" class="btn btn-success btn-lg" id="btn-guardar">
                    <i class="fas fa-save me-2"></i>
                    Guardar Ruta
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Botón flotante de guardado rápido -->
<button type="button" class="floating-save-btn btn btn-success d-none" id="quick-save-btn" onclick="guardarRapido()">
    <i class="fas fa-save me-2"></i>
    Guardar
</button>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let mapa = null;
let marcadorInicio = null;
let marcadorFin = null;
let rutaPolyline = null;
let puntosRuta = [];
let estacionInicio = null;
let estacionFin = null;

document.addEventListener('DOMContentLoaded', function() {
    // Manejar selección de estaciones
    document.querySelectorAll('.estacion-selector').forEach(selector => {
        selector.addEventListener('click', function() {
            const tipo = this.dataset.tipo;
            const id = this.dataset.id;
            const lat = parseFloat(this.dataset.lat);
            const lng = parseFloat(this.dataset.lng);
            const nombre = this.querySelector('h6').textContent;
            
            // Deseleccionar otras estaciones del mismo tipo
            document.querySelectorAll(`[data-tipo="${tipo}"]`).forEach(s => {
                s.classList.remove('selected');
            });
            
            // Seleccionar esta estación
            this.classList.add('selected');
            
            // Actualizar campo oculto
            document.getElementById(`estacion_${tipo}_id`).value = id;
            
            // Guardar datos
            if (tipo === 'inicio') {
                estacionInicio = { id, lat, lng, nombre };
            } else {
                estacionFin = { id, lat, lng, nombre };
            }
            
            // Verificar si se pueden habilitar botones
            verificarProgreso();
        });
    });
    
    // Manejar selección de dificultad
    document.querySelectorAll('.dificultad-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.dificultad-option').forEach(o => {
                o.classList.remove('selected');
            });
            this.classList.add('selected');
            document.getElementById('dificultad').value = this.dataset.dificultad;
            actualizarResumen();
        });
    });
    
    // Manejar cambios en el formulario
    document.getElementById('nombre').addEventListener('input', actualizarResumen);
    document.getElementById('tiempo_estimado_minutos').addEventListener('input', actualizarResumen);
});

function siguientePaso(paso) {
    // Ocultar sección actual
    document.querySelectorAll('.form-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Mostrar nueva sección
    document.getElementById(`seccion-${paso}`).style.display = 'block';
    
    // Actualizar indicadores de progreso
    for (let i = 1; i <= 4; i++) {
        const stepElement = document.getElementById(`step-${i}`);
        if (i < paso) {
            stepElement.className = 'step-number completed';
        } else if (i === paso) {
            stepElement.className = 'step-number current';
        } else {
            stepElement.className = 'step-number';
        }
    }
    
    // Inicializar mapa si llegamos al paso 3
    if (paso === 3 && !mapa) {
        initializarMapa();
    }
    
    // Scroll al top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function anteriorPaso(paso) {
    siguientePaso(paso);
}

function verificarProgreso() {
    // Verificar paso 2
    const tieneInicio = document.getElementById('estacion_inicio_id').value !== '';
    const tieneFin = document.getElementById('estacion_fin_id').value !== '';
    document.getElementById('btn-siguiente-2').disabled = !(tieneInicio && tieneFin);
    
    // Verificar paso 3
    const tieneRuta = puntosRuta.length >= 2;
    const btnSiguiente3 = document.getElementById('btn-siguiente-3');
    if (btnSiguiente3) {
        btnSiguiente3.disabled = !tieneRuta;
    }
}

function initializarMapa() {
    // Coordenadas de Puerto Barrios
    const centroMapa = [15.7278, -88.5944];
    
    mapa = L.map('mapa-ruta').setView(centroMapa, 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa);
    
    // Agregar marcadores de estaciones si están seleccionadas
    if (estacionInicio) {
        marcadorInicio = L.marker([estacionInicio.lat, estacionInicio.lng])
            .addTo(mapa)
            .bindPopup(`<strong>Inicio:</strong> ${estacionInicio.nombre}`)
            .openPopup();
    }
    
    if (estacionFin) {
        marcadorFin = L.marker([estacionFin.lat, estacionFin.lng])
            .addTo(mapa)
            .bindPopup(`<strong>Fin:</strong> ${estacionFin.nombre}`);
    }
    
    // Manejar clics en el mapa para agregar puntos
    mapa.on('click', function(e) {
        agregarPuntoRuta(e.latlng.lat, e.latlng.lng);
    });
    
    // Calcular ruta automática si hay inicio y fin
    if (estacionInicio && estacionFin) {
        document.getElementById('btn-calcular-ruta').addEventListener('click', calcularRutaAutomatica);
    }
    
    document.getElementById('btn-limpiar-ruta').addEventListener('click', limpiarRuta);
}

function agregarPuntoRuta(lat, lng) {
    puntosRuta.push([lat, lng]);
    actualizarRutaEnMapa();
    actualizarEstadisticasRuta();
    verificarProgreso();
}

function actualizarRutaEnMapa() {
    if (rutaPolyline) {
        mapa.removeLayer(rutaPolyline);
    }
    
    if (puntosRuta.length >= 2) {
        rutaPolyline = L.polyline(puntosRuta, {
            color: '#2563eb',
            weight: 4,
            opacity: 0.8
        }).addTo(mapa);
    }
    
    // Actualizar campos ocultos
    document.getElementById('puntos_ruta').value = JSON.stringify(puntosRuta);
}

function calcularRutaAutomatica() {
    if (!estacionInicio || !estacionFin) return;
    
    puntosRuta = [
        [estacionInicio.lat, estacionInicio.lng],
        [estacionFin.lat, estacionFin.lng]
    ];
    
    actualizarRutaEnMapa();
    actualizarEstadisticasRuta();
    verificarProgreso();
}

function limpiarRuta() {
    puntosRuta = [];
    if (rutaPolyline) {
        mapa.removeLayer(rutaPolyline);
        rutaPolyline = null;
    }
    actualizarEstadisticasRuta();
    verificarProgreso();
}

function actualizarEstadisticasRuta() {
    let distanciaTotal = 0;
    
    if (puntosRuta.length >= 2) {
        for (let i = 0; i < puntosRuta.length - 1; i++) {
            const punto1 = L.latLng(puntosRuta[i]);
            const punto2 = L.latLng(puntosRuta[i + 1]);
            distanciaTotal += punto1.distanceTo(punto2);
        }
    }
    
    const distanciaKm = distanciaTotal / 1000;
    const tiempoEstimado = Math.round(distanciaKm * 4); // 4 min por km aprox
    const co2Reducido = distanciaKm * 0.23; // 0.23 kg CO2 por km
    
    // Actualizar previsualizaciones
    document.getElementById('distancia-calculada').textContent = distanciaKm.toFixed(1) + ' km';
    document.getElementById('puntos-ruta').textContent = puntosRuta.length;
    document.getElementById('preview-distancia').textContent = distanciaKm.toFixed(1) + ' km';
    document.getElementById('preview-tiempo').textContent = tiempoEstimado + ' min';
    document.getElementById('preview-co2').textContent = co2Reducido.toFixed(1) + ' kg';
    
    // Actualizar campos del formulario
    document.getElementById('distancia_km').value = distanciaKm.toFixed(2);
    if (!document.getElementById('tiempo_estimado_minutos').value) {
        document.getElementById('tiempo_estimado_minutos').value = tiempoEstimado;
    }
}

function actualizarResumen() {
    const nombre = document.getElementById('nombre').value || '-';
    const tiempo = document.getElementById('tiempo_estimado_minutos').value || '0';
    const dificultad = document.getElementById('dificultad').value || '-';
    const distancia = document.getElementById('distancia_km').value || '0';
    
    document.getElementById('resumen-nombre').textContent = nombre;
    document.getElementById('resumen-inicio').textContent = estacionInicio ? estacionInicio.nombre : '-';
    document.getElementById('resumen-fin').textContent = estacionFin ? estacionFin.nombre : '-';
    document.getElementById('resumen-distancia').textContent = parseFloat(distancia).toFixed(1) + ' km';
    document.getElementById('resumen-tiempo').textContent = tiempo + ' min';
    document.getElementById('resumen-dificultad').textContent = dificultad;
}

function guardarRapido() {
    document.getElementById('ruta-form').submit();
}
</script>
@endpush