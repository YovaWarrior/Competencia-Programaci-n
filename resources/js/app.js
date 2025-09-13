// resources/js/app.js - ARCHIVO COMPLETO

/**
 * ECOBICI PUERTO BARRIOS - JAVASCRIPT PRINCIPAL
 * Funcionalidades: Mapas, Gráficos, Animaciones, AJAX, Validaciones
 * Desarrollado para Competencia Día del Programador 2025
 */

// ============================================
// CONFIGURACIÓN GLOBAL
// ============================================

window.EcoBici = {
    config: {
        mapCenter: [15.7278, -88.5944], // Centro de Puerto Barrios
        mapZoom: 13,
        apiEndpoints: {
            estaciones: '/api/estaciones',
            rutas: '/api/mapa/rutas',
            mapaEstaciones: '/api/mapa/estaciones',
            estadisticas: '/api/estadisticas-generales',
            usoActual: '/api/uso-actual'
        },
        colores: {
            azul: '#2563eb',
            verde: '#16a34a',
            celeste: '#0ea5e9',
            warning: '#f59e0b',
            danger: '#ef4444',
            success: '#16a34a'
        }
    },
    mapa: null,
    marcadores: [],
    charts: {},
    intervals: []
};

// ============================================
// INICIALIZACIÓN PRINCIPAL
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚴‍♀️ EcoBici Puerto Barrios - JavaScript cargado');
    
    // Inicializar todos los componentes
    EcoBici.init();
});

EcoBici.init = function() {
    console.log('🔄 Inicializando componentes EcoBici...');
    
    this.initializeAnimations();
    this.initializeCharts();
    this.initializeMapas();
    this.initializeFormValidation();
    this.initializeNotifications();
    this.initializeRealTimeUpdates();
    this.initializeEventListeners();
    
    console.log('✅ Todos los componentes inicializados correctamente');
};

// ============================================
// MAPAS INTERACTIVOS CON LEAFLET
// ============================================

EcoBici.initializeMapas = function() {
    const mapContainer = document.getElementById('mapa-estaciones');
    if (!mapContainer) {
        console.log('📍 No se encontró contenedor de mapa en esta página');
        return;
    }

    console.log('📍 Inicializando mapa de estaciones...');

    try {
        // Inicializar Leaflet
        this.mapa = L.map('mapa-estaciones').setView(this.config.mapCenter, this.config.mapZoom);

        // Añadir capa del mapa (OpenStreetMap gratuito)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors | EcoBici Puerto Barrios',
            maxZoom: 18,
            minZoom: 10
        }).addTo(this.mapa);

        // Cargar estaciones iniciales
        this.cargarEstaciones();
        
        // Actualizar cada 30 segundos
        const intervalId = setInterval(() => {
            this.cargarEstaciones();
        }, 30000);
        
        this.intervals.push(intervalId);

        console.log('✅ Mapa inicializado correctamente');
        
    } catch (error) {
        console.error('❌ Error inicializando mapa:', error);
        this.mostrarNotificacion('Error cargando el mapa', 'danger');
    }
};

EcoBici.cargarEstaciones = function() {
    if (!this.mapa) return;

    fetch(this.config.apiEndpoints.mapaEstaciones)
        .then(response => {
            if (!response.ok) {
                throw new Error(HTTP ${response.status});
            }
            return response.json();
        })
        .then(estaciones => {
            console.log(📊 Cargadas ${estaciones.length} estaciones);
            
            // Limpiar marcadores anteriores
            this.marcadores.forEach(marcador => {
                this.mapa.removeLayer(marcador);
            });
            this.marcadores = [];

            // Añadir nuevos marcadores
            estaciones.forEach(estacion => {
                this.agregarMarcadorEstacion(estacion);
            });

            // Actualizar estadísticas en la página
            this.actualizarEstadisticasEstaciones(estaciones);
        })
        .catch(error => {
            console.error('❌ Error cargando estaciones:', error);
            this.mostrarNotificacion('Error al cargar estaciones del mapa', 'warning', 3000);
        });
};

EcoBici.agregarMarcadorEstacion = function(estacion) {
    // Determinar color según disponibilidad
    let color = this.config.colores.verde;
    let iconClass = 'fa-bicycle';
    
    if (estacion.bicicletas.disponibles === 0) {
        color = this.config.colores.danger;
        iconClass = 'fa-times';
    } else if (estacion.bicicletas.disponibles < 3) {
        color = this.config.colores.warning;
        iconClass = 'fa-exclamation-triangle';
    }

    // Crear icono personalizado con HTML
    const icono = L.divIcon({
        className: 'marcador-estacion-custom',
        html: `
            <div style="
                background: linear-gradient(45deg, ${color}, ${this.config.colores.celeste});
                border: 3px solid white;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                animation: pulseMarker 2s infinite;
            ">
                <i class="fas ${iconClass}" style="font-size: 16px;"></i>
            </div>
        `,
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20]
    });

    // Crear marcador
    const marcador = L.marker([estacion.latitud, estacion.longitud], { icon: icono })
        .addTo(this.mapa);

    // Popup con información detallada
    const popupContent = this.crearPopupEstacion(estacion);
    marcador.bindPopup(popupContent, {
        maxWidth: 300,
        className: 'popup-estacion-custom'
    });

    this.marcadores.push(marcador);
};

EcoBici.crearPopupEstacion = function(estacion) {
    const porcentajeOcupacion = Math.round((estacion.bicicletas.disponibles / estacion.capacidad_total) * 100);
    
    return `
        <div class="text-center p-2">
            <h6 class="text-primary mb-2 fw-bold">${estacion.nombre}</h6>
            <p class="mb-1"><strong>Código:</strong> <span class="badge bg-secondary">${estacion.codigo}</span></p>
            <p class="mb-2"><strong>Tipo:</strong> ${this.formatearTipoEstacion(estacion.tipo)}</p>
            
            <div class="row text-center mb-2">
                <div class="col-4">
                    <i class="fas fa-bicycle text-success"></i><br>
                    <small><strong>${estacion.bicicletas.disponibles}</strong><br>Disponibles</small>
                </div>
                <div class="col-4">
                    <i class="fas fa-bolt text-warning"></i><br>
                    <small><strong>${estacion.bicicletas.electricas}</strong><br>Eléctricas</small>
                </div>
                <div class="col-4">
                    <i class="fas fa-percentage text-info"></i><br>
                    <small><strong>${porcentajeOcupacion}%</strong><br>Ocupación</small>
                </div>
            </div>
            
            <div class="progress mb-2" style="height: 8px;">
                <div class="progress-bar bg-${porcentajeOcupacion > 70 ? 'success' : porcentajeOcupacion > 30 ? 'warning' : 'danger'}" 
                     style="width: ${porcentajeOcupacion}%"></div>
            </div>
            
            ${estacion.tiene_cargador_electrico ? 
                '<p class="mb-2 text-warning"><i class="fas fa-bolt"></i> Estación de carga eléctrica</p>' : ''}
            
            <div class="d-grid gap-1">
                <button class="btn btn-primary btn-sm" onclick="EcoBici.verDetallesEstacion(${estacion.id})">
                    <i class="fas fa-info-circle me-1"></i> Ver Detalles
                </button>
                ${estacion.bicicletas.disponibles > 0 ? 
                    `<button class="btn btn-success btn-sm" onclick="EcoBici.irASeleccionarBicicleta(${estacion.id})">
                        <i class="fas fa-bicycle me-1"></i> Usar Bicicleta
                    </button>` : 
                    '<button class="btn btn-outline-secondary btn-sm" disabled>Sin Bicicletas</button>'
                }
            </div>
        </div>
    `;
};

EcoBici.formatearTipoEstacion = function(tipo) {
    const tipos = {
        'carga': '🔋 Carga',
        'seleccion': '🚲 Selección',
        'descanso': '🛑 Descanso',
        'mixta': '🔄 Mixta'
    };
    return tipos[tipo] || tipo;
};

EcoBici.verDetallesEstacion = function(estacionId) {
    window.location.href = /estaciones/${estacionId};
};

EcoBici.irASeleccionarBicicleta = function(estacionId) {
    window.location.href = /bicicletas/seleccionar?estacion=${estacionId};
};

EcoBici.actualizarEstadisticasEstaciones = function(estaciones) {
    const stats = {
        total: estaciones.length,
        disponibles: estaciones.filter(e => e.bicicletas.disponibles > 0).length,
        totalBicicletas: estaciones.reduce((sum, e) => sum + e.bicicletas.total, 0),
        bicicletasDisponibles: estaciones.reduce((sum, e) => sum + e.bicicletas.disponibles, 0),
        estacionesCarga: estaciones.filter(e => e.tiene_cargador_electrico).length
    };

    // Actualizar elementos del DOM con animación
    Object.keys(stats).forEach(stat => {
        const elements = document.querySelectorAll([data-stat="${stat}"]);
        elements.forEach(element => {
            this.animarNumero(element, stats[stat]);
        });
    });
};

// ============================================
// GRÁFICOS Y ESTADÍSTICAS CON CHART.JS
// ============================================

EcoBici.initializeCharts = function() {
    console.log('📊 Inicializando gráficos...');
    
    // Inicializar todos los gráficos disponibles
    this.crearGraficoCO2();
    this.crearGraficoUsoSemanal();
    this.crearGraficoDistribucionBicicletas();
    this.crearGraficoIngresos();
};

EcoBici.crearGraficoCO2 = function() {
    const canvas = document.getElementById('chart-co2');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    this.charts.co2 = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
            datasets: [{
                label: 'CO₂ Reducido (kg)',
                data: [12.5, 19.2, 25.8, 35.1, 42.7, 55.3],
                borderColor: this.config.colores.verde,
                backgroundColor: ${this.config.colores.verde}20,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: this.config.colores.verde,
                pointBorderColor: '#fff',
                pointBorderWidth: 3,
                pointRadius: 8,
                pointHoverRadius: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: this.config.colores.verde,
                    borderWidth: 2,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return CO₂ ahorrado: ${context.parsed.y} kg;
                        },
                        afterLabel: function(context) {
                            const arboles = (context.parsed.y / 21.8).toFixed(1);
                            return Equivale a: ${arboles} árboles plantados;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' kg';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutBounce'
            }
        }
    });
};

EcoBici.crearGraficoUsoSemanal = function() {
    const canvas = document.getElementById('chart-uso-semanal');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    this.charts.usoSemanal = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
            datasets: [{
                label: 'Recorridos',
                data: [45, 52, 48, 61, 55, 67, 43],
                backgroundColor: [
                    this.config.colores.azul,
                    this.config.colores.celeste,
                    this.config.colores.verde,
                    this.config.colores.azul,
                    this.config.colores.celeste,
                    this.config.colores.verde,
                    this.config.colores.warning
                ],
                borderRadius: 12,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return ${context.parsed.y} recorridos realizados;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeOutBounce'
            }
        }
    });
};

EcoBici.crearGraficoDistribucionBicicletas = function() {
    const canvas = document.getElementById('chart-distribucion');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    this.charts.distribucion = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Disponibles', 'En Uso', 'Mantenimiento', 'Fuera de Servicio'],
            datasets: [{
                data: [65, 25, 8, 2],
                backgroundColor: [
                    this.config.colores.verde,
                    this.config.colores.warning,
                    this.config.colores.danger,
                    '#6b7280'
                ],
                borderWidth: 0,
                cutout: '70%'
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
                            family: 'Montserrat',
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return ${context.label}: ${context.parsed}% (${percentage}% del total);
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 2000
            }
        }
    });
};

EcoBici.crearGraficoIngresos = function() {
    const canvas = document.getElementById('chart-ingresos');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    this.charts.ingresos = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Ingresos por Membresías (Q)',
                data: [15000, 18500, 22000, 19500, 25000, 28000],
                borderColor: this.config.colores.azul,
                backgroundColor: ${this.config.colores.azul}20,
                fill: true,
                tension: 0.3
            }, {
                label: 'Ingresos por Minutos Extra (Q)',
                data: [2500, 3200, 2800, 3500, 4100, 3800],
                borderColor: this.config.colores.celeste,
                backgroundColor: ${this.config.colores.celeste}20,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return ${context.dataset.label}: Q${context.parsed.y.toLocaleString()};
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Q' + value.toLocaleString();
                        }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
};

// ============================================
// ANIMACIONES Y EFECTOS VISUALES
// ============================================

EcoBici.initializeAnimations = function() {
    console.log('✨ Inicializando animaciones...');
    
    this.initializeCounters();
    this.initializeScrollAnimations();
    this.initializeHoverEffects();
    this.initializeParticleEffects();
};

EcoBici.initializeCounters = function() {
    const counters = document.querySelectorAll('[data-counter]');
    
    counters.forEach(counter => {
        const target = parseInt(counter.dataset.counter) || parseInt(counter.textContent);
        const duration = parseInt(counter.dataset.duration) || 2000;
        
        // Iniciar animación cuando el elemento sea visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animarNumero(entry.target, target, duration);
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(counter);
    });
};

EcoBici.animarNumero = function(element, target, duration = 2000) {
    const start = parseInt(element.textContent) || 0;
    const increment = (target - start) / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        
        if ((increment > 0 && current >= target) || (increment < 0 && current <= target)) {
            current = target;
            clearInterval(timer);
        }
        
        element.textContent = Math.floor(current).toLocaleString();
    }, 16);
};

EcoBici.initializeScrollAnimations = function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observar elementos que deben animarse
    document.querySelectorAll('.card, .alert, .table, .hero-section').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        observer.observe(el);
    });
};

EcoBici.initializeHoverEffects = function() {
    // Efecto de elevación en cards
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Efecto de brillo en botones
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
};

EcoBici.initializeParticleEffects = function() {
    // Crear partículas flotantes en el hero section
    const heroSection = document.querySelector('.hero-section');
    if (!heroSection) return;
    
    for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'floating-particle';
        particle.style.cssText = `
            position: absolute;
            width: ${Math.random() * 6 + 2}px;
            height: ${Math.random() * 6 + 2}px;
            background: rgba(37, 99, 235, 0.3);
            border-radius: 50%;
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            animation: float ${Math.random() * 10 + 10}s linear infinite;
            pointer-events: none;
        `;
        heroSection.appendChild(particle);
    }
};

// ============================================
// VALIDACIÓN DE FORMULARIOS AVANZADA
// ============================================

EcoBici.initializeFormValidation = function() {
    console.log('✔️ Inicializando validación de formularios...');
    
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!EcoBici.validateForm(this)) {
                e.preventDefault();
                e.stopPropagation();
                EcoBici.mostrarNotificacion('Por favor corrige los errores en el formulario', 'warning');
            }
            
            this.classList.add('was-validated');
        });
        
        // Validación en tiempo real
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                EcoBici.validateField(input);
            });
            
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) {
                    EcoBici.validateField(input);
                }
            });
        });
    });
};

EcoBici.validateForm = function(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!this.validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
};

EcoBici.validateField = function(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let message = '';
    
    // Validaciones específicas
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = 'Este campo es obligatorio';
    } else if (type === 'email' && value && !this.isValidEmail(value)) {
        isValid = false;
        message = 'Ingresa un email válido';
    } else if (field.name === 'dpi' && value && !this.isValidDPI(value)) {
        isValid = false;
        message = 'DPI debe tener exactamente 13 dígitos';
    } else if (field.name === 'telefono' && value && !this.isValidPhone(value)) {
        isValid = false;
        message = 'Formato: +502XXXXXXXX o 8 dígitos';
    } else if (type === 'password' && field.name === 'password' && value && value.length < 8) {
        isValid = false;
        message = 'La contraseña debe tener al menos 8 caracteres';
    } else if (field.name === 'password_confirmation') {
        const password = document.querySelector('input[name="password"]');
        if (password && value !== password.value) {
            isValid = false;
            message = 'Las contraseñas no coinciden';
        }
    }
    
    // Actualizar clases y mensajes
    field.classList.toggle('is-invalid', !isValid);
    field.classList.toggle('is-valid', isValid && value);
    
    // Mostrar mensaje de error
    let feedback = field.parentNode.querySelector('.invalid-feedback');
    if (!feedback && !isValid) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        field.parentNode.appendChild(feedback);
    }
    
    if (feedback) {
        feedback.textContent = message;
        feedback.style.display = !isValid ? 'block' : 'none';
    }
    
    return isValid;
};

EcoBici.isValidEmail = function(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
};

EcoBici.isValidDPI = function(dpi) {
    return /^\d{13}$/.test(dpi);
};

EcoBici.isValidPhone = function(phone) {
    return /^(\+502)?[0-9]{8}$/.test(phone.replace(/\s/g, ''));
};

// ============================================
// SISTEMA DE NOTIFICACIONES AVANZADO
// ============================================

EcoBici.initializeNotifications = function() {
    console.log('🔔 Inicializando sistema de notificaciones...');
    
    // Auto-hide para alertas existentes
    document.querySelectorAll('.alert[role="alert"]').forEach(alert => {
        setTimeout(() => {
            this.fadeOutAlert(alert);
        }, 5000);
    });
    
    // Crear contenedor de notificaciones si no existe
    if (!document.getElementById('notifications-container')) {
        const container = document.getElementById('notifications-container');
    if (!container) return;

    const alertId = 'alert-' + Date.now();
    const alertContainer = document.createElement('div');
    alertContainer.id = alertId;
    alertContainer.className = alert alert-${tipo} alert-dismissible fade show mb-2;
    alertContainer.style.cssText = `
        animation: slideInRight 0.5s ease-out;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border: none;
        border-radius: 12px;
    `;
    
    alertContainer.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${this.getIconForType(tipo)} me-2"></i>
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
    if (!alert) return;
    
    alert.style.animation = 'slideOutRight 0.5s ease-in forwards';
    setTimeout(() => {
        alert.remove();
    }, 500);
};

EcoBici.fadeOutAlert = function(alert) {
    alert.style.opacity = '0';
    alert.style.transform = 'translateY(-20px)';
    alert.style.transition = 'all 0.3s ease';
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 300);
};

EcoBici.getIconForType = function(tipo) {
    const icons = {
        success: 'check-circle',
        danger: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle',
        primary: 'info-circle'
    };
    return icons[tipo] || 'info-circle';
};

// ============================================
// ACTUALIZACIONES EN TIEMPO REAL
// ============================================

EcoBici.initializeRealTimeUpdates = function() {
    console.log('🔄 Inicializando actualizaciones en tiempo real...');
    
    // Actualizar estadísticas cada minuto
    const statsInterval = setInterval(() => {
        this.actualizarEstadisticasGenerales();
    }, 60000);
    
    // Verificar uso activo cada 30 segundos
    const usoInterval = setInterval(() => {
        this.verificarUsoActivo();
    }, 30000);
    
    this.intervals.push(statsInterval, usoInterval);
};

EcoBici.actualizarEstadisticasGenerales = function() {
    fetch(this.config.apiEndpoints.estadisticas)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success && data.data) {
                Object.keys(data.data).forEach(key => {
                    const elements = document.querySelectorAll([data-stat="${key}"]);
                    elements.forEach(element => {
                        this.animarNumero(element, data.data[key], 1000);
                    });
                });
            }
        })
        .catch(error => {
            console.log('⚠️ No se pudieron actualizar las estadísticas:', error.message);
        });
};

EcoBici.verificarUsoActivo = function() {
    const usoActivoElement = document.querySelector('[data-uso-activo]');
    if (!usoActivoElement) return;
    
    fetch(this.config.apiEndpoints.usoActual)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Actualizar tiempo transcurrido
                const tiempoElement = usoActivoElement.querySelector('[data-tiempo-transcurrido]');
                if (tiempoElement && data.data.tiempo_transcurrido) {
                    tiempoElement.textContent = this.formatearTiempo(data.data.tiempo_transcurrido);
                }
                
                // Actualizar información de la bicicleta
                const bicicletaElement = usoActivoElement.querySelector('[data-bicicleta-codigo]');
                if (bicicletaElement && data.data.bicicleta) {
                    bicicletaElement.textContent = data.data.bicicleta.codigo;
                }
            }
        })
        .catch(error => {
            console.log('⚠️ No se pudo verificar uso activo:', error.message);
        });
};

// ============================================
// EVENT LISTENERS Y INTERACCIONES
// ============================================

EcoBici.initializeEventListeners = function() {
    console.log('👂 Configurando event listeners...');
    
    // Ripple effect en cards
    this.initializeRippleEffect();
    
    // Smooth scroll para enlaces internos
    this.initializeSmoothScroll();
    
    // Filtros dinámicos
    this.initializeFilters();
    
    // Tooltips
    this.initializeTooltips();
};

EcoBici.initializeRippleEffect = function() {
    document.querySelectorAll('.card, .btn').forEach(element => {
        element.addEventListener('click', function(e) {
            const ripple = document.createElement('div');
            ripple.classList.add('ripple-effect');
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
};

EcoBici.initializeSmoothScroll = function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
};

EcoBici.initializeFilters = function() {
    // Filtros en página de selección de bicicletas
    const filtroEstacion = document.getElementById('filtro-estacion');
    const filtroTipo = document.getElementById('filtro-tipo');
    
    if (filtroEstacion && filtroTipo) {
        [filtroEstacion, filtroTipo].forEach(filtro => {
            filtro.addEventListener('change', this.aplicarFiltrosBicicletas);
        });
    }
    
    // Filtros en otras páginas
    document.querySelectorAll('[data-filter]').forEach(filter => {
        filter.addEventListener('change', function() {
            EcoBici.aplicarFiltroGenerico(this);
        });
    });
};

EcoBici.aplicarFiltrosBicicletas = function() {
    const filtroEstacion = document.getElementById('filtro-estacion');
    const filtroTipo = document.getElementById('filtro-tipo');
    
    const estacionSeleccionada = filtroEstacion.value;
    const tipoSeleccionado = filtroTipo.value;
    
    // Filtrar grupos de estaciones
    document.querySelectorAll('.estacion-group').forEach(group => {
        const nombreEstacion = group.dataset.estacion;
        const mostrarEstacion = !estacionSeleccionada || nombreEstacion.includes(estacionSeleccionada);
        group.style.display = mostrarEstacion ? 'block' : 'none';
    });
    
    // Filtrar bicicletas por tipo
    document.querySelectorAll('.bicicleta-card').forEach(card => {
        const tipoBicicleta = card.dataset.tipo;
        const mostrarTipo = !tipoSeleccionado || tipoBicicleta === tipoSeleccionado;
        const columna = card.closest('.col-lg-4, .col-md-6');
        if (columna) {
            columna.style.display = mostrarTipo ? 'block' : 'none';
        }
    });
    
    // Actualizar contadores
    EcoBici.actualizarContadoresFiltros();
};

EcoBici.actualizarContadoresFiltros = function() {
    document.querySelectorAll('.estacion-group').forEach(group => {
        const bicicletasVisibles = group.querySelectorAll('.col-lg-4:not([style*="display: none"]), .col-md-6:not([style*="display: none"])').length;
        const badge = group.querySelector('.badge');
        if (badge) {
            badge.textContent = ${bicicletasVisibles} disponibles;
        }
    });
};

EcoBici.aplicarFiltroGenerico = function(filterElement) {
    const filterType = filterElement.dataset.filter;
    const filterValue = filterElement.value;
    
    document.querySelectorAll([data-filter-target="${filterType}"]).forEach(target => {
        const targetValue = target.dataset.filterValue;
        const shouldShow = !filterValue || targetValue === filterValue;
        target.style.display = shouldShow ? 'block' : 'none';
    });
};

EcoBici.initializeTooltips = function() {
    // Inicializar tooltips de Bootstrap si está disponible
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
};

// ============================================
// FUNCIONES ESPECÍFICAS DE PÁGINAS
// ============================================

// Selección de bicicletas
EcoBici.seleccionarBicicleta = function(bicicletaId, estacionId) {
    if (!confirm('¿Estás seguro de que quieres usar esta bicicleta?')) {
        return;
    }
    
    // Mostrar indicador de carga
    const card = event.target.closest('.bicicleta-card');
    const button = card.querySelector('button');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Iniciando...';
    button.disabled = true;
    
    // Crear y enviar formulario
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = /bicicletas/${bicicletaId}/usar;
    
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

// Finalizar uso de bicicleta
EcoBici.finalizarUso = function(usoId) {
    const form = document.getElementById('form-finalizar-uso');
    if (!form) return;
    
    const estacionFin = form.querySelector('[name="estacion_fin_id"]');
    if (!estacionFin || !estacionFin.value) {
        this.mostrarNotificacion('Por favor selecciona una estación de destino', 'warning');
        estacionFin?.focus();
        return;
    }
    
    if (!confirm('¿Estás seguro de que quieres finalizar este recorrido?')) {
        return;
    }
    
    // Mostrar indicador de carga
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Finalizando recorrido...';
    submitBtn.disabled = true;
    
    // Mostrar notificación
    this.mostrarNotificacion('Procesando finalización del recorrido...', 'info', 0);
    
    // Enviar formulario
    form.submit();
};

// Cálculos en tiempo real
EcoBici.calcularEstimacionCO2 = function(distanciaKm) {
    const co2PorKm = 0.23; // kg CO₂ por km evitado vs auto
    return (distanciaKm * co2PorKm).toFixed(2);
};

EcoBici.calcularPuntosVerdes = function(co2Reducido, tipoBicicleta = 'tradicional') {
    let puntos = Math.floor(co2Reducido * 10); // 1 punto por cada 0.1 kg CO₂
    
    // Multiplicadores según tipo
    switch (tipoBicicleta) {
        case 'electrica':
            puntos = Math.floor(puntos * 1.5);
            break;
        case 'premium':
            puntos = puntos * 2;
            break;
    }
    
    return puntos;
};

EcoBici.actualizarEstimacionesRecorrido = function(distancia, duracion) {
    const co2Estimado = this.calcularEstimacionCO2(distancia);
    const puntosEstimados = this.calcularPuntosVerdes(co2Estimado);
    
    // Actualizar elementos en la página
    const co2Element = document.querySelector('[data-co2-estimado]');
    const puntosElement = document.querySelector('[data-puntos-estimados]');
    
    if (co2Element) {
        co2Element.textContent = ${co2Estimado} kg CO₂;
    }
    
    if (puntosElement) {
        puntosElement.textContent = ${puntosEstimados} puntos;
    }
};

// ============================================
// UTILIDADES Y HELPERS
// ============================================

EcoBici.formatearTiempo = function(minutos) {
    if (minutos < 60) {
        return ${minutos} min;
    }
    
    const horas = Math.floor(minutos / 60);
    const minutosRestantes = minutos % 60;
    
    if (horas < 24) {
        return ${horas}h ${minutosRestantes}min;
    }
    
    const dias = Math.floor(horas / 24);
    const horasRestantes = horas % 24;
    
    return ${dias}d ${horasRestantes}h;
};

EcoBici.formatearDistancia = function(km) {
    if (km < 1) {
        return ${(km * 1000).toFixed(0)} m;
    }
    return ${km.toFixed(2)} km;
};

EcoBici.formatearCO2 = function(kg) {
    return ${kg.toFixed(2)} kg CO₂;
};

EcoBici.formatearMoneda = function(cantidad) {
    return Q${cantidad.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '    const container = document.,')};
};

EcoBici.obtenerEquivalenciasCO2 = function(totalCO2) {
    return {
        arboles_plantados: (totalCO2 / 21.8).toFixed(1), // 1 árbol absorbe 21.8 kg CO₂/año
        autos_detenidos_un_dia: (totalCO2 / 4.6).toFixed(1), // Auto promedio emite 4.6 kg CO₂/día
        km_auto_evitados: (totalCO2 / 0.23).toFixed(1), // 0.23 kg CO₂ por km en auto
        energia_casa_dias: (totalCO2 / 12).toFixed(1) // Casa promedio 12 kg CO₂/día
    };
};

EcoBici.debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

EcoBici.throttle = function(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
};

// ============================================
// CLEANUP Y DESTRUCCIÓN
// ============================================

EcoBici.destroy = function() {
    console.log('🧹 Limpiando EcoBici...');
    
    // Limpiar intervalos
    this.intervals.forEach(interval => clearInterval(interval));
    this.intervals = [];
    
    // Limpiar gráficos
    Object.values(this.charts).forEach(chart => {
        if (chart && typeof chart.destroy === 'function') {
            chart.destroy();
        }
    });
    this.charts = {};
    
    // Limpiar mapa
    if (this.mapa) {
        this.mapa.remove();
        this.mapa = null;
    }
    
    this.marcadores = [];
    
    console.log('✅ Cleanup completado');
};

// Cleanup automático al cerrar la página
window.addEventListener('beforeunload', () => {
    EcoBici.destroy();
});

// ============================================
// ESTILOS CSS ADICIONALES INYECTADOS
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

    @keyframes fadeInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    @keyframes pulseMarker {
        0% {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(37, 99, 235, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { 
            transform: rotate(360deg); 
        }
    }

    .floating-particle {
        position: absolute !important;
        pointer-events: none;
    }

    .popup-estacion-custom {
        border-radius: 15px !important;
    }

    .popup-estacion-custom .leaflet-popup-content-wrapper {
        border-radius: 15px !important;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    .marcador-estacion-custom {
        background: transparent !important;
        border: none !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #notifications-container {
            left: 10px !important;
            right: 10px !important;
            max-width: none !important;
        }
        
        .popup-estacion-custom {
            max-width: 250px !important;
        }
    }
`;

document.head.appendChild(additionalStyles);

// ============================================
// EXPORT PARA TESTING Y DEBUG
// ============================================

// Exponer funciones útiles en modo desarrollo
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    window.EcoBiciDebug = {
        mostrarEstadisticas: () => {
            console.log('📊 Estadísticas EcoBici:', {
                mapaCargado: !!EcoBici.mapa,
                marcadoresActivos: EcoBici.marcadores.length,
                graficosActivos: Object.keys(EcoBici.charts).length,
                intervalosActivos: EcoBici.intervals.length
            });
        },
        simularNotificacion: (mensaje, tipo) => {
            EcoBici.mostrarNotificacion(mensaje || 'Notificación de prueba', tipo || 'info');
        },
        reiniciarComponente: (componente) => {
            switch(componente) {
                case 'mapa':
                    EcoBici.initializeMapas();
                    break;
                case 'graficos':
                    EcoBici.initializeCharts();
                    break;
                case 'animaciones':
                    EcoBici.initializeAnimations();
                    break;
                default:
                    EcoBici.init();
            }
        }
    };
    
    console.log('🔧 Modo desarrollo activado. Usa window.EcoBiciDebug para funciones de debug.');
}

// Mensaje final de carga
console.log('✅ EcoBici JavaScript completamente cargado e inicializado');
console.log('🚴‍♀️ ¡Listo para conquistar Puerto Barrios de manera sostenible!');

// Export para uso en módulos (si se requiere)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EcoBici;
}createElement('div');
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
};

EcoBici.mostrarNotificacion = function(mensaje, tipo = 'info', duracion = 5000) {
    const container = document.getElementById('notifications-container');
    if (!container) return;

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
    
    alertContainer.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${this.getIconForType(tipo)} me-2"></i>
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
