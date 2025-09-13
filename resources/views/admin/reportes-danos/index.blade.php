@extends('layouts.admin')

@section('title', 'Reportes de Daños')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Reportes de Daños</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item active">Reportes de Daños</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button class="btn btn-success" onclick="exportarExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
            <button class="btn btn-info" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Reportes Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reportesPendientes ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                En Revisión
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reportesRevision ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Resueltos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reportesResueltos ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Total Reportes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReportes ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
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
            <form method="GET" action="{{ route('admin.reportes-danos.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="estado">Estado</label>
                        <select class="form-control" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="revision" {{ request('estado') == 'revision' ? 'selected' : '' }}>En Revisión</option>
                            <option value="resuelto" {{ request('estado') == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="prioridad">Prioridad</label>
                        <select class="form-control" name="prioridad">
                            <option value="">Todas las prioridades</option>
                            <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="critica" {{ request('prioridad') == 'critica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_desde">Fecha Desde</label>
                        <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.reportes-danos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Reportes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Reportes de Daños</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Bicicleta</th>
                            <th>Tipo de Daño</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha Reporte</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportes ?? [] as $reporte)
                        <tr>
                            <td>{{ $reporte->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="font-weight-bold">{{ $reporte->user->nombre ?? 'N/A' }} {{ $reporte->user->apellido ?? '' }}</div>
                                        <div class="text-muted small">{{ $reporte->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $reporte->bicicleta->codigo ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $reporte->tipo_dano ?? 'N/A' }}</td>
                            <td>
                                @switch($reporte->prioridad ?? 'baja')
                                    @case('critica')
                                        <span class="badge badge-danger">Crítica</span>
                                        @break
                                    @case('alta')
                                        <span class="badge badge-warning">Alta</span>
                                        @break
                                    @case('media')
                                        <span class="badge badge-info">Media</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">Baja</span>
                                @endswitch
                            </td>
                            <td>
                                @switch($reporte->estado ?? 'pendiente')
                                    @case('resuelto')
                                        <span class="badge badge-success">Resuelto</span>
                                        @break
                                    @case('revision')
                                        <span class="badge badge-warning">En Revisión</span>
                                        @break
                                    @default
                                        <span class="badge badge-danger">Pendiente</span>
                                @endswitch
                            </td>
                            <td>{{ $reporte->created_at ? $reporte->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.reportes-danos.show', $reporte->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($reporte->estado != 'resuelto')
                                    <button class="btn btn-sm btn-success" onclick="resolverReporte({{ $reporte->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3 d-block"></i>
                                No se encontraron reportes de daños
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if(isset($reportes) && $reportes->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $reportes->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportarExcel() {
    // Implementar exportación a Excel
    alert('Función de exportación en desarrollo');
}

function resolverReporte(id) {
    if (confirm('¿Está seguro de marcar este reporte como resuelto?')) {
        // Implementar resolución de reporte
        fetch(`/admin/reportes-danos/${id}/resolver`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al resolver el reporte');
            }
        });
    }
}
</script>
@endsection