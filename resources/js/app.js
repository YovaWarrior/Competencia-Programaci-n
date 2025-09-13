/**
 * ECOBICI PUERTO BARRIOS - JAVASCRIPT PRINCIPAL
 * Versión simplificada para resolver problemas de carga
 */

// ============================================
// CONFIGURACIÓN GLOBAL
// ============================================

window.EcoBici = window.EcoBici || {
    config: {
        mapCenter: [15.7278, -88.5944], // Centro de Puerto Barrios
        mapZoom: 13,
        apiEndpoints: {
            estaciones: '/api/estaciones',
            mapaEstaciones: '/api/mapa/estaciones'
        }
    },
    mapa: null,
    marcadores: [],
    marcadorUsuario: null,
    estacionesData: []
};

// ============================================
// INICIALIZACIÓN PRINCIPAL
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚴‍♀️ EcoBici Puerto Barrios - JavaScript cargado');
    EcoBici.init();
});

EcoBici.init = function() {
    console.log('🔄 Inicializando componentes EcoBici...');
    
    // Solo inicializar mapa si existe el contenedor
    if (document.getElementById('mapa-estaciones')) {
        this.initializeMapas();
    }
    
    console.log('✅ Componentes inicializados correctamente');
};

// ============================================
// MAPAS INTERACTIVOS
// ============================================

EcoBici.initializeMapas = function() {
    const mapContainer = document.getElementById('mapa-estaciones');
    if (!mapContainer) {
        console.log('📍 No se encontró contenedor de mapa en esta página');
        return;
    }

    console.log('📍 Inicializando mapa de estaciones...');

    try {
        // Verificar que Leaflet esté disponible
        if (typeof L === 'undefined') {
            console.error('❌ Leaflet no está cargado');
            return;
        }

        // Inicializar Leaflet
        this.mapa = L.map('mapa-estaciones').setView(this.config.mapCenter, this.config.mapZoom);

        // Añadir capa del mapa
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors | EcoBici Puerto Barrios',
            maxZoom: 18,
            minZoom: 10
        }).addTo(this.mapa);

        // Cargar estaciones
        this.cargarEstaciones();
        
        // Ocultar spinner después de cargar
        setTimeout(() => {
            const spinner = document.getElementById('mapa-loading');
            if (spinner) spinner.style.display = 'none';
        }, 2000);

        console.log('✅ Mapa inicializado correctamente');
        
    } catch (error) {
        console.error('❌ Error inicializando mapa:', error);
        this.mostrarNotificacion('Error cargando el mapa', 'danger');
    }
};

EcoBici.cargarEstaciones = function() {
    if (!this.mapa) return;

    // Si tenemos datos estáticos, usarlos
    if (this.estacionesData && this.estacionesData.length > 0) {
        this.renderizarMarcadores(this.estacionesData);
        return;
    }

    // Intentar cargar desde API
    fetch(this.config.apiEndpoints.mapaEstaciones)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(estaciones => {
            console.log(`📊 Cargadas ${estaciones.length} estaciones`);
            this.estacionesData = estaciones;
            this.renderizarMarcadores(estaciones);
        })
        .catch(error => {
            console.error('❌ Error cargando estaciones:', error);
            this.mostrarNotificacion('Error al cargar estaciones', 'warning');
            
            // Usar datos de ejemplo si falla la API
            this.usarDatosEjemplo();
        });
};

EcoBici.renderizarMarcadores = function(estaciones) {
    // Limpiar marcadores anteriores
    this.marcadores.forEach(marcador => {
        this.mapa.removeLayer(marcador);
    });
    this.marcadores = [];

    // Agregar marcadores de estaciones
    estaciones.forEach(estacion => {
        this.agregarMarcadorEstacion(estacion);
    });
};

EcoBici.agregarMarcadorEstacion = function(estacion) {
    const disponibles = estacion.bicicletas_disponibles || 0;
    
    // Determinar color del marcador
    let color = '#dc3545'; // Rojo por defecto
    let icono = 'times';
    
    if (disponibles > 5) {
        color = '#28a745'; // Verde
        icono = 'bicycle';
    } else if (disponibles > 0) {
        color = '#ffc107'; // Amarillo
        icono = 'exclamation';
    }

    // Si tiene cargador eléctrico, usar color azul
    if (estacion.tiene_cargador_electrico && disponibles > 0) {
        color = '#17a2b8';
        icono = 'bolt';
    }

    // Crear icono personalizado
    const customIcon = L.divIcon({
        html: `
            <div style="
                background: ${color};
                color: white;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 3px solid white;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                font-weight: bold;
                font-size: 12px;
            ">
                <i class="fas fa-${icono}"></i>
            </div>
        `,
        className: 'custom-marker',
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    // Crear popup
    const popupContent = this.crearPopupEstacion(estacion);

    // Crear marcador
    const marker = L.marker([estacion.latitud, estacion.longitud], { icon: customIcon })
        .bindPopup(popupContent, {
            maxWidth: 350,
            className: 'custom-popup'
        })
        .addTo(this.mapa);

    this.marcadores.push(marker);
};

EcoBici.crearPopupEstacion = function(estacion) {
    const disponibles = estacion.bicicletas_disponibles || 0;
    
    return `
        <div class="estacion-info">
            <div class="estacion-header">
                <h6 class="mb-1">${estacion.nombre}</h6>
                <span class="badge bg-primary">${estacion.tipo || 'mixta'}</span>
            </div>
            <p class="mb-2 text-muted small">
                <i class="fas fa-map-marker-alt me-1"></i>
                ${estacion.direccion}
            </p>
            <div class="bicicletas-counter">
                <div class="counter-item">
                    <i class="fas fa-bicycle text-success"></i>
                    <span>${disponibles} disponibles</span>
                </div>
                <div class="counter-item">
                    <i class="fas fa-parking text-muted"></i>
                    <span>${estacion.capacidad_total || 20} espacios</span>
                </div>
            </div>
            ${estacion.tiene_cargador_electrico ? 
                '<div class="text-center mt-2"><span class="badge bg-info"><i class="fas fa-bolt me-1"></i>Carga Eléctrica</span></div>' : 
                ''
            }
            <div class="text-center mt-3">
                <button class="btn btn-primary btn-sm me-2" onclick="EcoBici.verDetallesEstacion(${estacion.id})">
                    <i class="fas fa-info-circle me-1"></i>Detalles
                </button>
                ${disponibles > 0 ? 
                    `<button class="btn btn-success btn-sm" onclick="EcoBici.irASeleccionBicicletas(${estacion.id})">
                        <i class="fas fa-bicycle me-1"></i>Usar
                    </button>` : 
                    '<button class="btn btn-secondary btn-sm" disabled>Sin bicicletas</button>'
                }
            </div>
        </div>
    `;
};

// ============================================
// FUNCIONES DE UTILIDAD
// ============================================

EcoBici.centrarMapa = function() {
    if (this.mapa) {
        this.mapa.setView(this.config.mapCenter, this.config.mapZoom);
        this.mostrarNotificacion('Mapa centrado', 'info', 1500);
    }
};

EcoBici.buscarMiUbicacion = function() {
    if (!navigator.geolocation) {
        this.mostrarNotificacion('Geolocalización no disponible', 'warning');
        return;
    }

    this.mostrarNotificacion('Buscando tu ubicación...', 'info', 3000);

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Remover marcador anterior del usuario
            if (this.marcadorUsuario) {
                this.mapa.removeLayer(this.marcadorUsuario);
            }

            // Crear marcador del usuario
            const userIcon = L.divIcon({
                html: `
                    <div style="
                        background: #007bff;
                        color: white;
                        border-radius: 50%;
                        width: 20px;
                        height: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border: 3px solid white;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    ">
                        <i class="fas fa-user" style="font-size: 10px;"></i>
                    </div>
                `,
                className: 'user-marker',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            this.marcadorUsuario = L.marker([lat, lng], { icon: userIcon })
                .bindPopup('📍 Tu ubicación actual')
                .addTo(this.mapa);

            this.mapa.setView([lat, lng], 15);
            this.mostrarNotificacion('¡Ubicación encontrada!', 'success');
        },
        (error) => {
            console.error('Error obteniendo ubicación:', error);
            this.mostrarNotificacion('No se pudo obtener tu ubicación', 'warning');
        }
    );
};

EcoBici.actualizarEstaciones = function() {
    this.cargarEstaciones();
    this.mostrarNotificacion('Mapa actualizado', 'success', 2000);
};

EcoBici.enfocarEstacion = function(lat, lng, nombre) {
    if (this.mapa) {
        this.mapa.setView([lat, lng], 16);
        this.mostrarNotificacion(`Enfocando: ${nombre}`, 'info', 2000);
    }
};

EcoBici.verDetallesEstacion = function(estacionId) {
    this.mostrarNotificacion('Cargando detalles...', 'info', 2000);
    // Aquí se implementaría la lógica del modal
};

EcoBici.irASeleccionBicicletas = function(estacionId) {
    window.location.href = `/bicicletas/seleccionar?estacion=${estacionId}`;
};

EcoBici.filtrarEstaciones = function(tipo) {
    let estacionesFiltradas = this.estacionesData;

    switch(tipo) {
        case 'disponibles':
            estacionesFiltradas = this.estacionesData.filter(e => (e.bicicletas_disponibles || 0) > 0);
            break;
        case 'electricas':
            estacionesFiltradas = this.estacionesData.filter(e => e.tiene_cargador_electrico);
            break;
        case 'todas':
        default:
            estacionesFiltradas = this.estacionesData;
            break;
    }

    this.renderizarMarcadores(estacionesFiltradas);
    this.mostrarNotificacion(`Mostrando ${estacionesFiltradas.length} estaciones`, 'info', 2000);
};

EcoBici.usarDatosEjemplo = function() {
    // Datos de ejemplo para Puerto Barrios
    const estacionesEjemplo = [
        {
            id: 1,
            nombre: 'Estación Centro Histórico',
            latitud: 15.7278,
            longitud: -88.5944,
            direccion: 'Parque Central, Puerto Barrios',
            tipo: 'mixta',
            bicicletas_disponibles: 8,
            capacidad_total: 24,
            tiene_cargador_electrico: true
        },
        {
            id: 2,
            nombre: 'Estación Mercado Municipal',
            latitud: 15.7265,
            longitud: -88.5955,
            direccion: 'Mercado Municipal, Puerto Barrios',
            tipo: 'seleccion',
            bicicletas_disponibles: 12,
            capacidad_total: 20,
            tiene_cargador_electrico: false
        },
        {
            id: 3,
            nombre: 'Estación Muelle Municipal',
            latitud: 15.7290,
            longitud: -88.5920,
            direccion: 'Muelle Municipal, Puerto Barrios',
            tipo: 'carga',
            bicicletas_disponibles: 6,
            capacidad_total: 30,
            tiene_cargador_electrico: true
        }
    ];

    console.log('📊 Usando datos de ejemplo');
    this.estacionesData = estacionesEjemplo;
    this.renderizarMarcadores(estacionesEjemplo);
};

// ============================================
// SISTEMA DE NOTIFICACIONES
// ============================================

EcoBici.mostrarNotificacion = function(mensaje, tipo = 'info', duracion = 5000) {
    // Crear contenedor si no existe
    let container = document.getElementById('notifications-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notifications-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(container);
    }

    const alertId = 'alert-' + Date.now();
    const alertContainer = document.createElement('div');
    alertContainer.id = alertId;
    alertContainer.className = `alert alert-${tipo} alert-dismissible fade show mb-2`;
    alertContainer.style.cssText = `
        animation: slideInRight 0.5s ease-out;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border: none;
        border-radius: 12px;
    `;
    
    const iconMap = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    
    alertContainer.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${iconMap[tipo] || 'info-circle'} me-2"></i>
            <span>${mensaje}</span>
        </div>
        <button type="button" class="btn-close" onclick="EcoBici.cerrarNotificacion('${alertId}')"></button>
    `;
    
    container.appendChild(alertContainer);
    
    // Auto-remove después del tiempo especificado
    if (duracion > 0) {
        setTimeout(() => {
            this.cerrarNotificacion(alertId);
        }, duracion);
    }
    
    return alertId;
};

EcoBici.cerrarNotificacion = function(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.animation = 'slideOutRight 0.5s ease-out';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 500);
    }
};

// ============================================
// FUNCIONES DE BICICLETAS
// ============================================

EcoBici.usarBicicleta = function(bicicletaId, estacionId) {
    if (!bicicletaId || !estacionId) {
        this.mostrarNotificacion('Error: Datos incompletos', 'danger');
        return;
    }
    
    // Mostrar indicador de carga
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Iniciando...';
    button.disabled = true;
    
    // Crear y enviar formulario
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/bicicletas/${bicicletaId}/usar`;
    
    // CSRF Token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    // Estación ID
    const estacionInput = document.createElement('input');
    estacionInput.type = 'hidden';
    estacionInput.name = 'estacion_inicio_id';
    estacionInput.value = estacionId;
    
    form.appendChild(csrfInput);
    form.appendChild(estacionInput);
    document.body.appendChild(form);
    
    // Mostrar notificación
    this.mostrarNotificacion('Iniciando tu recorrido EcoBici...', 'info', 3000);
    
    // Enviar formulario
    setTimeout(() => {
        form.submit();
    }, 1000);
};

// ============================================
// ESTILOS CSS ADICIONALES
// ============================================

const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .custom-popup .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .estacion-info {
        padding: 15px;
        max-width: 300px;
    }

    .estacion-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .bicicletas-counter {
        display: flex;
        justify-content: space-between;
        margin: 10px 0;
    }

    .counter-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.8rem;
    }

    .estacion-item:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .custom-marker {
        background: transparent !important;
        border: none !important;
    }

    .user-marker {
        background: transparent !important;
        border: none !important;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #notifications-container {
            left: 10px !important;
            right: 10px !important;
            max-width: none !important;
        }
        
        .estacion-info {
            max-width: 250px !important;
        }
    }
`;

document.head.appendChild(additionalStyles);

// ============================================
// DEBUG Y DESARROLLO
// ============================================

// Exponer funciones útiles en modo desarrollo
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    window.EcoBiciDebug = {
        mostrarEstadisticas: () => {
            console.log('📊 Estadísticas EcoBici:', {
                mapaCargado: !!EcoBici.mapa,
                marcadoresActivos: EcoBici.marcadores.length,
                estacionesCargadas: EcoBici.estacionesData.length
            });
        },
        simularNotificacion: (mensaje, tipo) => {
            EcoBici.mostrarNotificacion(mensaje || 'Notificación de prueba', tipo || 'info');
        },
        reiniciarMapa: () => {
            EcoBici.initializeMapas();
        },
        cargarDatosEjemplo: () => {
            EcoBici.usarDatosEjemplo();
        }
    };
    
    console.log('🔧 Modo desarrollo activado. Usa window.EcoBiciDebug para funciones de debug.');
}

console.log('✅ EcoBici JavaScript cargado correctamente');
console.log('🚴‍♀️ ¡Listo para conquistar Puerto Barrios de manera sostenible!');
