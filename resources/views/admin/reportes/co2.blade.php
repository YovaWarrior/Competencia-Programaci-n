@extends('layouts.admin')

@section('title', 'Reporte de Impacto CO₂')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Reporte de Impacto CO₂</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Impacto CO₂</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.reportes.exportar', 'co2') }}?{{ http_build_query(request()->query()) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </a>
            <button class="btn btn-info" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Estadísticas de Impacto Ambiental -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                CO₂ Total Reducido
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($reporteCo2->sum('co2_reducido'), 2) }} kg</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-leaf fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Viajes Ecológicos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reporteCo2->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bicycle fa-2x text-gray-300"></i>
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
                                Promedio por Viaje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reporteCo2->count() > 0 ? number_format($reporteCo2->avg('co2_reducido'), 2) : '0.00' }} kg</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                CO₂ Este Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($reporteCo2->where('created_at', '>=', now()->startOfMonth())->sum('co2_reducido'), 2) }} kg</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional de Impacto -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Equivalencias Ambientales</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalCo2 = $reporteCo2->sum('co2_reducido');
                        $arboles = round($totalCo2 / 22); // 1 árbol absorbe ~22kg CO2/año
                        $autos = round($totalCo2 / 4600); // 1 auto emite ~4.6 toneladas CO2/año
                        $km_auto = round($totalCo2 / 0.21); // 1 km en auto emite ~0.21kg CO2
                    @endphp
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="text-success">
                                <i class="fas fa-tree fa-3x mb-2"></i>
                                <div class="h4 font-weight-bold">{{ number_format($arboles) }}</div>
                                <small>Árboles equivalentes</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-primary">
                                <i class="fas fa-car fa-3x mb-2"></i>
                                <div class="h4 font-weight-bold">{{ number_format($autos) }}</div>
                                <small>Autos menos en circulación</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-warning">
                                <i class="fas fa-road fa-3x mb-2"></i>
                                <div class="h4 font-weight-bold">{{ number_format($km_auto) }}</div>
                                <small>Km evitados en auto</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Top Usuarios Ecológicos</h6>
                </div>
                <div class="card-body">
                    @php
                        $topUsuarios = $reporteCo2->groupBy('user_id')->map(function($usos) {
                            return [
                                'usuario' => $usos->first()->user,
                                'total_co2' => $usos->sum('co2_reducido'),
                                'viajes' => $usos->count()
                            ];
                        })->sortByDesc('total_co2')->take(5);
                    @endphp
                    @foreach($topUsuarios as $usuario)
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <span class="text-white font-weight-bold">{{ substr($usuario['usuario']->nombre, 0, 1) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">{{ $usuario['usuario']->nombre }} {{ $usuario['usuario']->apellido }}</div>
                            <small class="text-muted">{{ $usuario['viajes'] }} viajes • {{ number_format($usuario['total_co2'], 2) }} kg CO₂</small>
                        </div>
                        <div class="text-success font-weight-bold">
                            <i class="fas fa-leaf"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.co2') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" class="form-control" name="fecha_fin" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="usuario">Usuario</label>
                        <input type="text" class="form-control" name="usuario" placeholder="Buscar por nombre" value="{{ request('usuario') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.reportes.co2') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Impacto CO₂ -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detalle de Impacto Ambiental ({{ $reporteCo2->total() }} registros)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Bicicleta</th>
                            <th>Distancia</th>
                            <th>CO₂ Reducido</th>
                            <th>Duración</th>
                            <th>Fecha</th>
                            <th>Impacto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reporteCo2 as $uso)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        <span class="text-white font-weight-bold">{{ substr($uso->user->nombre, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $uso->user->nombre }} {{ $uso->user->apellido }}</div>
                                        <div class="text-muted small">{{ $uso->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $uso->bicicleta->codigo }}</span>
                            </td>
                            <td>
                                @if($uso->distancia_recorrida)
                                    <span class="font-weight-bold">{{ number_format($uso->distancia_recorrida, 2) }} km</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-leaf text-success me-2"></i>
                                    <span class="font-weight-bold text-success">{{ number_format($uso->co2_reducido, 2) }} kg</span>
                                </div>
                            </td>
                            <td>
                                @if($uso->fecha_fin)
                                    {{ $uso->fecha_inicio->diffForHumans($uso->fecha_fin, true) }}
                                @else
                                    <span class="text-warning">En curso</span>
                                @endif
                            </td>
                            <td>{{ $uso->fecha_inicio->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                    $impacto = '';
                                    if($uso->co2_reducido >= 5) $impacto = 'Alto';
                                    elseif($uso->co2_reducido >= 2) $impacto = 'Medio';
                                    else $impacto = 'Bajo';
                                    
                                    $badgeClass = $impacto == 'Alto' ? 'success' : ($impacto == 'Medio' ? 'warning' : 'info');
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">{{ $impacto }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-leaf fa-3x mb-3 d-block"></i>
                                No se encontraron registros de impacto CO₂
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($reporteCo2->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $reporteCo2->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportarExcel() {
    window.location.href = "{{ route('admin.reportes.exportar', 'co2') }}?" + new URLSearchParams(window.location.search);
}
</script>
@endsection
