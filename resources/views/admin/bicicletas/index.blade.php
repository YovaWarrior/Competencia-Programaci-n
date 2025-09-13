@extends('layouts.admin')

@section('title', 'Gestión de Bicicletas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestión de Bicicletas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item active">Bicicletas</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaBicicletaModal">
                <i class="fas fa-plus"></i> Nueva Bicicleta
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
                                Total Bicicletas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBicicletas ?? 0 }}</div>
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
                                Disponibles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bicicletasDisponibles ?? 0 }}</div>
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
                                En Uso
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bicicletasEnUso ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                En Mantenimiento
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bicicletasMantenimiento ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
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
            <form method="GET" action="{{ route('admin.bicicletas.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="estado">Estado</label>
                        <select class="form-control" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="en_uso" {{ request('estado') == 'en_uso' ? 'selected' : '' }}>En Uso</option>
                            <option value="mantenimiento" {{ request('estado') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="fuera_servicio" {{ request('estado') == 'fuera_servicio' ? 'selected' : '' }}>Fuera de Servicio</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="estacion">Estación</label>
                        <select class="form-control" name="estacion">
                            <option value="">Todas las estaciones</option>
                            @foreach($estaciones ?? [] as $estacion)
                            <option value="{{ $estacion->id }}" {{ request('estacion') == $estacion->id ? 'selected' : '' }}>
                                {{ $estacion->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="buscar">Buscar</label>
                        <input type="text" class="form-control" name="buscar" placeholder="Código o modelo" value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.bicicletas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Bicicletas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Bicicletas ({{ $bicicletas->total() ?? 0 }} registros)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Modelo</th>
                            <th>Estado</th>
                            <th>Estación Actual</th>
                            <th>Batería</th>
                            <th>Última Actividad</th>
                            <th>Usos Totales</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bicicletas ?? [] as $bicicleta)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $bicicleta->codigo }}</div>
                                <small class="text-muted">ID: {{ $bicicleta->id }}</small>
                            </td>
                            <td>
                                <div>{{ $bicicleta->modelo ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $bicicleta->marca ?? 'Sin marca' }}</small>
                            </td>
                            <td>
                                @switch($bicicleta->estado ?? 'disponible')
                                    @case('disponible')
                                        <span class="badge badge-success">Disponible</span>
                                        @break
                                    @case('en_uso')
                                        <span class="badge badge-warning">En Uso</span>
                                        @break
                                    @case('mantenimiento')
                                        <span class="badge badge-info">Mantenimiento</span>
                                        @break
                                    @default
                                        <span class="badge badge-danger">Fuera de Servicio</span>
                                @endswitch
                            </td>
                            <td>
                                @if($bicicleta->estacion_actual)
                                    <div class="font-weight-bold">{{ $bicicleta->estacionActual->nombre ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $bicicleta->estacionActual->direccion ?? '' }}</small>
                                @else
                                    <span class="text-muted">Sin estación</span>
                                @endif
                            </td>
                            <td>
                                @if($bicicleta->nivel_bateria !== null)
                                    @php
                                        $bateria = $bicicleta->nivel_bateria;
                                        $colorBateria = $bateria > 50 ? 'success' : ($bateria > 20 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $colorBateria }}" 
                                             role="progressbar" 
                                             style="width: {{ $bateria }}%"
                                             aria-valuenow="{{ $bateria }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $bateria }}%
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($bicicleta->ultima_actividad)
                                    {{ $bicicleta->ultima_actividad->diffForHumans() }}
                                @else
                                    <span class="text-muted">Sin actividad</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $bicicleta->usos_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.bicicletas.show', $bicicleta->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-warning" onclick="editarBicicleta({{ $bicicleta->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($bicicleta->estado != 'mantenimiento')
                                    <button class="btn btn-sm btn-secondary" onclick="cambiarEstado({{ $bicicleta->id }}, 'mantenimiento')">
                                        <i class="fas fa-tools"></i>
                                    </button>
                                    @else
                                    <button class="btn btn-sm btn-success" onclick="cambiarEstado({{ $bicicleta->id }}, 'disponible')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-bicycle fa-3x mb-3 d-block"></i>
                                No se encontraron bicicletas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if(isset($bicicletas) && $bicicletas->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $bicicletas->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Nueva Bicicleta -->
<div class="modal fade" id="nuevaBicicletaModal" tabindex="-1" aria-labelledby="nuevaBicicletaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevaBicicletaModalLabel">Nueva Bicicleta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.bicicletas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo" class="form-label">Código *</label>
                                <input type="text" class="form-control" id="codigo" name="codigo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modelo" class="form-label">Modelo *</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estacion_actual" class="form-label">Estación Inicial</label>
                                <select class="form-control" id="estacion_actual" name="estacion_actual">
                                    <option value="">Seleccionar estación</option>
                                    @foreach($estaciones ?? [] as $estacion)
                                    <option value="{{ $estacion->id }}">{{ $estacion->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nivel_bateria" class="form-label">Nivel de Batería (%)</label>
                                <input type="number" class="form-control" id="nivel_bateria" name="nivel_bateria" min="0" max="100" value="100">
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
                    <button type="submit" class="btn btn-primary">Crear Bicicleta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function exportarExcel() {
    alert('Función de exportación en desarrollo');
}

function editarBicicleta(id) {
    window.location.href = `/admin/bicicletas/${id}/edit`;
}

function cambiarEstado(id, nuevoEstado) {
    const mensaje = nuevoEstado === 'mantenimiento' ? 
        '¿Poner la bicicleta en mantenimiento?' : 
        '¿Marcar la bicicleta como disponible?';
    
    if (confirm(mensaje)) {
        fetch(`/admin/bicicletas/${id}/estado`, {
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