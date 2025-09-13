@extends('layouts.admin')

@section('title', 'Bicicletas Populares')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Bicicletas Populares</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Bicicletas Populares</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="{{ route('admin.reportes.exportar', 'bicicletas-populares') }}" class="btn btn-outline-success">
                <i class="fas fa-download"></i> Exportar
            </a>
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
                                Bicicletas Usadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['total_bicicletas_usadas']) }}</div>
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
                                Promedio de Usos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['promedio_usos'], 1) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                Distancia Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['total_distancia'], 1) }} km</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-route fa-2x text-gray-300"></i>
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
                                Más Popular
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($estadisticas['bicicleta_mas_popular'])
                                    {{ $estadisticas['bicicleta_mas_popular']->codigo }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Bicicletas Populares -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ranking de Bicicletas por Popularidad</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Marca/Modelo</th>
                            <th>Total Usos</th>
                            <th>Distancia Total</th>
                            <th>Tiempo Total</th>
                            <th>CO₂ Reducido</th>
                            <th>Calificación</th>
                            <th>Estación Actual</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bicicletasPopulares as $index => $bicicleta)
                        <tr>
                            <td>
                                @if($index < 3)
                                    <span class="badge badge-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'dark') }}">
                                        {{ $index + 1 }}
                                    </span>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td>
                                <span class="font-weight-bold text-primary">{{ $bicicleta->codigo }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $bicicleta->tipo == 'electrica' ? 'success' : 'info' }}">
                                    {{ ucfirst($bicicleta->tipo) }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $bicicleta->marca }}</div>
                                <div class="text-muted small">{{ $bicicleta->modelo }} ({{ $bicicleta->ano_fabricacion }})</div>
                            </td>
                            <td>
                                <span class="font-weight-bold text-primary">{{ number_format($bicicleta->total_usos) }}</span>
                            </td>
                            <td>
                                <span class="text-info">{{ number_format($bicicleta->total_distancia ?? 0, 1) }} km</span>
                            </td>
                            <td>
                                <span class="text-success">{{ number_format(($bicicleta->total_duracion ?? 0) / 60, 1) }} hrs</span>
                            </td>
                            <td>
                                <span class="text-success">{{ number_format($bicicleta->total_co2 ?? 0, 2) }} kg</span>
                            </td>
                            <td>
                                @if($bicicleta->promedio_calificacion)
                                    <div class="d-flex align-items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($bicicleta->promedio_calificacion) ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        <span class="ml-1 small">({{ number_format($bicicleta->promedio_calificacion, 1) }})</span>
                                    </div>
                                @else
                                    <span class="text-muted">Sin calificar</span>
                                @endif
                            </td>
                            <td>
                                @if($bicicleta->estacionActual)
                                    <span class="text-info">{{ $bicicleta->estacionActual->nombre }}</span>
                                @else
                                    <span class="text-muted">Sin estación</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ 
                                    $bicicleta->estado == 'disponible' ? 'success' : 
                                    ($bicicleta->estado == 'en_uso' ? 'warning' : 
                                    ($bicicleta->estado == 'mantenimiento' ? 'info' : 'danger')) 
                                }}">
                                    {{ ucfirst($bicicleta->estado) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="fas fa-bicycle fa-3x mb-3 text-gray-300"></i>
                                <p>No hay bicicletas con usos registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($bicicletasPopulares->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $bicicletasPopulares->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
