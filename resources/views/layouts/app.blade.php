<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'EcoBici Puerto Barrios')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom CSS -->
    <style>
        :root {
            --ecobici-azul: #2563eb;
            --ecobici-verde: #16a34a;
            --ecobici-celeste: #0ea5e9;
            --ecobici-azul-claro: #dbeafe;
            --ecobici-verde-claro: #dcfce7;
            --ecobici-celeste-claro: #e0f2fe;
        }
        
        * {
            font-family: 'Montserrat', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--ecobici-azul-claro) 0%, var(--ecobici-verde-claro) 100%);
            min-height: 100vh;
        }
        
        .navbar {
            background: linear-gradient(90deg, var(--ecobici-azul) 0%, var(--ecobici-verde) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);

            /* Corrección para el dropdown */
            position: relative;
            z-index: 1100;
        }
        
        /* Asegura que el dropdown se muestre por encima */
        .navbar .dropdown-menu {
            position: absolute !important;
            z-index: 1150 !important;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--ecobici-azul) 0%, var(--ecobici-celeste) 100%);
            border: none;
        }
        
        .btn-success {
            background: linear-gradient(45deg, var(--ecobici-verde) 0%, var(--ecobici-celeste) 100%);
            border: none;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .text-primary {
            color: var(--ecobici-azul) !important;
        }
        
        .text-success {
            color: var(--ecobici-verde) !important;
        }
        
        .bg-primary {
            background: linear-gradient(135deg, var(--ecobici-azul) 0%, var(--ecobici-celeste) 100%) !important;
        }
        
        .bg-success {
            background: linear-gradient(135deg, var(--ecobici-verde) 0%, var(--ecobici-celeste) 100%) !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-bicycle me-2"></i>
                EcoBici Puerto Barrios
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('estaciones.mapa') }}">
                                <i class="fas fa-map-marked-alt me-1"></i> Mapa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('bicicletas.seleccionar') }}">
                                <i class="fas fa-bicycle me-1"></i> Usar Bicicleta
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('rutas.index') }}">
                                <i class="fas fa-route me-1"></i> Mis Rutas
                            </a>
                        </li>
                    @endauth
                </ul>
                
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                {{ Auth::user()->nombre }}
                                @if(Auth::user()->puntos_verdes > 0)
                                    <span class="badge bg-success ms-1">{{ Auth::user()->puntos_verdes }} 🌱</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('profile') }}">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="{{ route('membresias.index') }}">Membresías</a></li>
                                <li><a class="dropdown-item" href="{{ route('bicicletas.historial') }}">Historial</a></li>
                                @if(Auth::user()->esAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Panel Admin</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Contenido principal -->
    <main class="container my-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>EcoBici Puerto Barrios</h5>
                    <p class="mb-0">Movilidad sostenible para un futuro más verde</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1">Universidad Mariano Gálvez</p>
                    <p class="mb-0">Competencia Día del Programador 2025</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Inicialización global de EcoBici -->
    <script>
        // Asegurar que EcoBici esté disponible globalmente
        window.EcoBici = window.EcoBici || {};
        
        // Configuración global básica
        window.EcoBici.config = window.EcoBici.config || {
            mapCenter: [15.7278, -88.5944],
            mapZoom: 13,
            apiEndpoints: {
                estaciones: '/api/estaciones',
                mapaEstaciones: '/api/mapa/estaciones'
            }
        };
        
        // Funciones básicas si no están cargadas
        window.EcoBici.mostrarNotificacion = window.EcoBici.mostrarNotificacion || function(mensaje, tipo = 'info', duracion = 5000) {
            console.log(${tipo.toUpperCase()}: ${mensaje});
        };
        
        window.EcoBici.usarBicicleta = window.EcoBici.usarBicicleta || function(bicicletaId, estacionId) {
            if (!bicicletaId || !estacionId) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = /bicicletas/${bicicletaId}/usar;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            const estacionInput = document.createElement('input');
            estacionInput.type = 'hidden';
            estacionInput.name = 'estacion_inicio_id';
            estacionInput.value = estacionId;
            
            form.appendChild(csrfInput);
            form.appendChild(estacionInput);
            document.body.appendChild(form);
            
            this.mostrarNotificacion('Iniciando tu recorrido EcoBici...', 'info', 3000);
            form.submit();
        };
    </script>
    
    @stack('scripts')
</body>
</html>