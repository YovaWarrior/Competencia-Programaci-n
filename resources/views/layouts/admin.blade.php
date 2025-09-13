<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Admin Panel') - EcoBici PuertoBarrios</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Admin Styles -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0.25rem 0;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: #3498db;
            color: #fff;
        }
        .content-wrapper {
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <h4 class="text-white">
                                <i class="fas fa-bicycle"></i> EcoBici PuertoBarrios
                            </h4>
                        </a>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.usuarios.index') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">
                                <i class="fas fa-users"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.usuarios.catalogo') ? 'active' : '' }}" href="{{ route('admin.usuarios.catalogo') }}">
                                <i class="fas fa-book"></i> Catálogo de Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.bicicletas.*') ? 'active' : '' }}" href="{{ route('admin.bicicletas.index') }}">
                                <i class="fas fa-bicycle"></i> Bicicletas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.estaciones.*') ? 'active' : '' }}" href="{{ route('admin.estaciones.index') }}">
                                <i class="fas fa-map-marker-alt"></i> Estaciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reportes-danos.*') ? 'active' : '' }}" href="{{ route('admin.reportes-danos.index') }}">
                                <i class="fas fa-exclamation-triangle"></i> Reportes de Daños
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}" href="{{ route('admin.reportes.index') }}">
                                <i class="fas fa-chart-bar"></i> Reportes
                            </a>
                        </li>
                        
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                        
                        <li class="nav-item">
                            <a class="navbar-brand text-white font-weight-bold" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-bicycle me-2"></i>EcoBici PuertoBarrios
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start w-100" style="color: #ecf0f1;">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content-wrapper">
                <!-- Top navbar -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="text-muted">
                                <i class="fas fa-user-shield"></i> 
                                Administrador: {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                            </span>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-refresh"></i> Actualizar
                        </button>
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('scripts')
</body>
</html>
