@extends('layouts.admin')

@section('title', 'Reportes Administrativos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Reportes Administrativos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item active">Reportes</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Usos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_usos']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bicycle fa-2x text-gray-300"></i>
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
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Ingresos Totales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Q{{ number_format($stats['ingresos_totales'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                CO₂ Reducido
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['co2_total'], 2) }} kg</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-leaf fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos a Reportes -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Reportes de Uso</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Análisis detallado del uso de bicicletas en el sistema.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reportes.uso') }}" class="btn btn-primary">
                            <i class="fas fa-chart-line"></i> Ver Reporte de Uso
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Reportes Financieros</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Análisis de ingresos y transacciones del sistema.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reportes.ingresos') }}" class="btn btn-success">
                            <i class="fas fa-money-bill-wave"></i> Ver Ingresos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-info">Impacto Ambiental</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Reporte del impacto positivo en reducción de CO₂.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reportes.co2') }}" class="btn btn-info">
                            <i class="fas fa-leaf"></i> Ver Impacto CO₂
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">Reportes Especiales</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Reportes específicos de usuarios y bicicletas populares.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reportes.usuarios-activos') }}" class="btn btn-warning">
                            <i class="fas fa-user-check"></i> Usuarios Activos
                        </a>
                        <a href="{{ route('admin.reportes.bicicletas-populares') }}" class="btn btn-outline-warning">
                            <i class="fas fa-star"></i> Bicicletas Populares
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
