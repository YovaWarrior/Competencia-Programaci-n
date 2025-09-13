@extends('layouts.admin')

@section('title', 'Catálogo de Usuarios Registrados')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Catálogo de Usuarios Registrados</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">Catálogo Completo</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-success" onclick="exportarCatalogo()">
                <i class="fas fa-download"></i> Exportar Excel
            </button>
            <button type="button" class="btn btn-primary" onclick="imprimirCatalogo()">
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
                                Total Usuarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['total_usuarios']) }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['usuarios_activos']) }}</div>
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
                                Con Membresía Activa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['usuarios_con_membresia']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
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
                                Edad Promedio
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['promedio_edad'], 1) }} años</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-birthday-cake fa-2x text-gray-300"></i>
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
            <form method="GET" action="{{ route('admin.usuarios.catalogo') }}" class="row">
                <div class="col-md-3 mb-3">
                    <label for="buscar" class="form-label">Buscar por nombre/email/DPI</label>
                    <input type="text" class="form-control" id="buscar" name="buscar" value="{{ request('buscar') }}" placeholder="Escriba para buscar...">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="membresia" class="form-label">Membresía</label>
                    <select class="form-control" id="membresia" name="membresia">
                        <option value="">Todas</option>
                        <option value="con_membresia" {{ request('membresia') == 'con_membresia' ? 'selected' : '' }}>Con membresía</option>
                        <option value="sin_membresia" {{ request('membresia') == 'sin_membresia' ? 'selected' : '' }}>Sin membresía</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="fecha_registro" class="form-label">Fecha de registro</label>
                    <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" value="{{ request('fecha_registro') }}">
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                    <a href="{{ route('admin.usuarios.catalogo') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado Completo de Usuarios ({{ $usuarios->total() }} registros)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="usuariosTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>DPI</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Fecha Nacimiento</th>
                            <th>Edad</th>
                            <th>Membresía Actual</th>
                            <th>Estado</th>
                            <th>Puntos Verdes</th>
                            <th>CO₂ Reducido (kg)</th>
                            <th>Total Usos</th>
                            <th>Distancia Total (km)</th>
                            <th>Tiempo Total (min)</th>
                            <th>Reportes</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td class="font-weight-bold">{{ $usuario->dpi }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($usuario->foto)
                                        <img src="{{ asset('storage/' . $usuario->foto) }}" class="rounded-circle me-2" width="32" height="32" alt="Foto">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <span class="text-white font-weight-bold">{{ substr($usuario->nombre, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->telefono ?: 'No registrado' }}</td>
                            <td>{{ $usuario->fecha_nacimiento ? $usuario->fecha_nacimiento->format('d/m/Y') : 'No registrada' }}</td>
                            <td>
                                @if($usuario->fecha_nacimiento)
                                    {{ $usuario->fecha_nacimiento->age }} años
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($usuario->membresiaActiva)
                                    <span class="badge badge-success">{{ $usuario->membresiaActiva->membresia->nombre }}</span>
                                    <small class="d-block text-muted">
                                        Vence: {{ $usuario->membresiaActiva->fecha_fin->format('d/m/Y') }}
                                    </small>
                                @else
                                    <span class="badge badge-secondary">Sin membresía</span>
                                @endif
                            </td>
                            <td>
                                @if($usuario->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ number_format($usuario->puntos_verdes) }}</span>
                            </td>
                            <td class="text-center">{{ number_format($usuario->total_co2_reducido ?? 0, 2) }}</td>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ number_format($usuario->total_usos ?? 0) }}</span>
                            </td>
                            <td class="text-center">{{ number_format($usuario->total_distancia ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($usuario->total_tiempo ?? 0) }}</td>
                            <td class="text-center">
                                <span class="text-muted">0</span>
                            </td>
                            <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.usuarios.show', $usuario->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($usuario->activo)
                                        <button class="btn btn-warning btn-sm" onclick="cambiarEstadoUsuario({{ $usuario->id }}, 'suspender')" title="Suspender">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-success btn-sm" onclick="cambiarEstadoUsuario({{ $usuario->id }}, 'activar')" title="Activar">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="16" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>No se encontraron usuarios registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $usuarios->firstItem() ?? 0 }} a {{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }} usuarios
                </div>
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar estado de usuario -->
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Estado de Usuario</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="cambiarEstadoForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p id="cambiarEstadoTexto"></p>
                    <div class="form-group">
                        <label for="motivo">Motivo (opcional)</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" placeholder="Ingrese el motivo del cambio de estado..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="confirmarCambio">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .table th {
        font-size: 0.85rem;
        white-space: nowrap;
    }
    .table td {
        font-size: 0.85rem;
        vertical-align: middle;
    }
    .badge {
        font-size: 0.75rem;
    }
    @media print {
        .btn, .card-header, .pagination, .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .table {
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function exportarCatalogo() {
    window.location.href = "{{ route('admin.reportes.exportar', 'usuarios') }}";
}

function imprimirCatalogo() {
    window.print();
}

function cambiarEstadoUsuario(usuarioId, accion) {
    const modal = $('#cambiarEstadoModal');
    const form = $('#cambiarEstadoForm');
    const texto = $('#cambiarEstadoTexto');
    const boton = $('#confirmarCambio');
    
    if (accion === 'suspender') {
        form.attr('action', `/admin/usuarios/${usuarioId}/suspender`);
        texto.text('¿Está seguro de que desea suspender este usuario? No podrá acceder al sistema hasta que sea reactivado.');
        boton.removeClass('btn-success').addClass('btn-warning').text('Suspender Usuario');
    } else {
        form.attr('action', `/admin/usuarios/${usuarioId}/activar`);
        texto.text('¿Está seguro de que desea activar este usuario? Podrá acceder al sistema normalmente.');
        boton.removeClass('btn-warning').addClass('btn-success').text('Activar Usuario');
    }
    
    modal.modal('show');
}

// Filtro en tiempo real
$(document).ready(function() {
    $('#buscar').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('#usuariosTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });
});
</script>
@endpush
