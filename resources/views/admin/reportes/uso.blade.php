@extends('layouts.admin')

@section('title', 'Reporte de Uso de Bicicletas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Reporte de Uso de Bicicletas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Uso de Bicicletas</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button class="btn btn-success" onclick="exportarExcel()">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
            <button class="btn btn-info" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.uso') }}">
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
                        <input type="text" class="form-control" name="usuario" placeholder="Buscar por nombre o email" value="{{ request('usuario') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.reportes.uso') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Usos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Historial de Usos ({{ $usos->total() }} registros)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Bicicleta</th>
                            <th>Estación Inicio</th>
                            <th>Estación Fin</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Duración</th>
                            <th>Distancia</th>
                            <th>CO₂ Reducido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usos as $uso)
                        <tr>
                            <td>{{ $uso->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="font-weight-bold">{{ $uso->user->nombre }} {{ $uso->user->apellido }}</div>
                                        <div class="text-muted small">{{ $uso->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $uso->bicicleta->codigo }}</span>
                            </td>
                            <td>{{ $uso->estacionInicio->nombre ?? 'N/A' }}</td>
                            <td>{{ $uso->estacionFin->nombre ?? 'N/A' }}</td>
                            <td>{{ $uso->fecha_inicio->format('d/m/Y H:i') }}</td>
                            <td>{{ $uso->fecha_fin ? $uso->fecha_fin->format('d/m/Y H:i') : 'En curso' }}</td>
                            <td>
                                @if($uso->fecha_fin)
                                    {{ $uso->fecha_inicio->diffForHumans($uso->fecha_fin, true) }}
                                @else
                                    <span class="text-warning">En curso</span>
                                @endif
                            </td>
                            <td>{{ $uso->distancia_recorrida ? number_format($uso->distancia_recorrida, 2) . ' km' : 'N/A' }}</td>
                            <td>
                                @if($uso->co2_reducido > 0)
                                    <span class="text-success font-weight-bold">{{ number_format($uso->co2_reducido, 2) }} kg</span>
                                @else
                                    <span class="text-muted">0 kg</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-bicycle fa-3x mb-3 d-block"></i>
                                No se encontraron registros de uso
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($usos->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $usos->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportarExcel() {
    // Implementar exportación a Excel
    window.location.href = "{{ route('admin.reportes.exportar', 'uso') }}?" + new URLSearchParams(window.location.search);
}
</script>
@endsection
