@extends('layouts.admin')

@section('title', 'Gestión de Estaciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestión de Estaciones</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item active">Estaciones</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaEstacionModal">
                <i class="fas fa-plus"></i> Nueva Estación
            </button>
            <button class="btn btn-success" onclick="exportarExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Estaciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEstaciones ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
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
                                Estaciones Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estacionesActivas ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                En Mantenimiento
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estacionesMantenimiento ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
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
                                Capacidad Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $capacidadTotal ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bicycle fa-2x text-gray-300"></i>
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
            <form method="GET" action="{{ route('admin.estaciones.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="estado">Estado</label>
                        <select class="form-control" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="mantenimiento" {{ request('estado') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="inactiva" {{ request('estado') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="buscar">Buscar</label>
                        <input type="text" class="form-control" name="buscar" placeholder="Nombre o ubicación" value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="capacidad_min">Capacidad Mínima</label>
                        <input type="number" class="form-control" name="capacidad_min" value="{{ request('capacidad_min') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.estaciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Estaciones -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Estaciones ({{ $estaciones->total() ?? 0 }} registros)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Capacidad</th>
                            <th>Disponibles</th>
                            <th>Estado</th>
                            <th>Última Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estaciones ?? [] as $estacion)
                        <tr>
                            <td>{{ $estacion->id }}</td>
                            <td>
                                <div class="font-weight-bold">{{ $estacion->nombre }}</div>
                                <small class="text-muted">{{ $estacion->codigo ?? 'Sin código' }}</small>
                            </td>
                            <td>
                                <div>{{ $estacion->direccion }}</div>
                                @if($estacion->latitud && $estacion->longitud)
                                <small class="text-muted">{{ $estacion->latitud }}, {{ $estacion->longitud }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $estacion->capacidad_total ?? 0 }} espacios</span>
                            </td>
                            <td>
                                @php
                                    $disponibles = $estacion->capacidad_total - ($estacion->bicicletas_count ?? 0);
                                    $porcentaje = $estacion->capacidad_total > 0 ? ($disponibles / $estacion->capacidad_total) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar 
                                        @if($porcentaje > 50) bg-success 
                                        @elseif($porcentaje > 20) bg-warning 
                                        @else bg-danger @endif" 
                                        role="progressbar" 
                                        style="width: {{ $porcentaje }}%"
                                        aria-valuenow="{{ $disponibles }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="{{ $estacion->capacidad_total }}">
                                        {{ $disponibles }}/{{ $estacion->capacidad_total }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @switch($estacion->estado ?? 'activa')
                                    @case('activa')
                                        <span class="badge badge-success">Activa</span>
                                        @break
                                    @case('mantenimiento')
                                        <span class="badge badge-warning">Mantenimiento</span>
                                        @break
                                    @default
                                        <span class="badge badge-danger">Inactiva</span>
                                @endswitch
                            </td>
                            <td>{{ $estacion->updated_at ? $estacion->updated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.estaciones.show', $estacion->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-warning" onclick="editarEstacion({{ $estacion->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($estacion->estado != 'mantenimiento')
                                    <button class="btn btn-sm btn-secondary" onclick="cambiarEstado({{ $estacion->id }}, 'mantenimiento')">
                                        <i class="fas fa-tools"></i>
                                    </button>
                                    @else
                                    <button class="btn btn-sm btn-success" onclick="cambiarEstado({{ $estacion->id }}, 'activa')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-map-marker-alt fa-3x mb-3 d-block"></i>
                                No se encontraron estaciones
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if(isset($estaciones) && $estaciones->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $estaciones->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Nueva Estación -->
<div class="modal fade" id="nuevaEstacionModal" tabindex="-1" aria-labelledby="nuevaEstacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevaEstacionModalLabel">Nueva Estación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.estaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" class="form-control" id="codigo" name="codigo">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección *</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="capacidad_total" class="form-label">Capacidad *</label>
                                <input type="number" class="form-control" id="capacidad_total" name="capacidad_total" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="latitud" class="form-label">Latitud</label>
                                <input type="number" class="form-control" id="latitud" name="latitud" step="any">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="longitud" class="form-label">Longitud</label>
                                <input type="number" class="form-control" id="longitud" name="longitud" step="any">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Estación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function exportarExcel() {
    alert('Función de exportación en desarrollo');
}

function editarEstacion(id) {
    // Implementar edición de estación
    window.location.href = `/admin/estaciones/${id}/edit`;
}

function cambiarEstado(id, nuevoEstado) {
    const mensaje = nuevoEstado === 'mantenimiento' ? 
        '¿Poner la estación en mantenimiento?' : 
        '¿Activar la estación?';
    
    if (confirm(mensaje)) {
        fetch(`/admin/estaciones/${id}/estado`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ estado: nuevoEstado })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al cambiar el estado');
            }
        });
    }
}
</script>
@endsection
