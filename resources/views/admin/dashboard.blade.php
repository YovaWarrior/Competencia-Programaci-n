@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard Administrativo</h1>
            <p class="text-muted">Panel de control del sistema BikeShare</p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Usuarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['usuarios_total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Usuarios Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['usuarios_activos']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Bicicletas Disponibles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['bicicletas_disponibles']) }}/{{ number_format($stats['bicicletas_total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bicycle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Recorridos Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['recorridos_hoy']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-route fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Accesos Rápidos</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Gestión de Usuarios -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-primary text-white shadow">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                Gestión de Usuarios
                                            </div>
                                            <div class="text-white-50 small">Administrar usuarios del sistema</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light btn-sm me-2">
                                            <i class="fas fa-list"></i> Lista
                                        </a>
                                        <a href="{{ route('admin.usuarios.catalogo') }}" class="btn btn-outline-light btn-sm">
                                            <i class="fas fa-book"></i> Catálogo Completo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gestión de Bicicletas -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-success text-white shadow">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                Gestión de Bicicletas
                                            </div>
                                            <div class="text-white-50 small">Administrar flota de bicicletas</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bicycle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.bicicletas.index') }}" class="btn btn-light btn-sm">
                                            <i class="fas fa-list"></i> Ver Todas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gestión de Estaciones -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-info text-white shadow">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                Gestión de Estaciones
                                            </div>
                                            <div class="text-white-50 small">Administrar puntos de servicio</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.estaciones.index') }}" class="btn btn-light btn-sm">
                                            <i class="fas fa-list"></i> Ver Todas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reportes de Daños -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-warning text-white shadow">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                Reportes Pendientes
                                            </div>
                                            <div class="text-white-50 small">{{ $stats['reportes_pendientes'] }} reportes por revisar</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.reportes-danos.index') }}" class="btn btn-light btn-sm">
                                            <i class="fas fa-eye"></i> Revisar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Adicionales -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ingresos del Mes</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h4 mb-0 font-weight-bold text-success">Q{{ number_format($stats['ingresos_mes'], 2) }}</div>
                        <div class="text-muted">Total en membresías</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">CO₂ Reducido Este Mes</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h4 mb-0 font-weight-bold text-info">{{ number_format($stats['co2_mes'], 2) }} kg</div>
                        <div class="text-muted">Impacto ambiental positivo</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Uso Semanal -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Uso de Bicicletas - Últimos 7 Días</h6>
                </div>
                <div class="card-body">
                    <canvas id="usoSemanalChart" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Gráfico de uso semanal
const ctx = document.getElementById('usoSemanalChart').getContext('2d');
const usoSemanalChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($usosSemana, 'fecha')) !!},
        datasets: [{
            label: 'Usos por día',
            data: {!! json_encode(array_column($usosSemana, 'usos')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush