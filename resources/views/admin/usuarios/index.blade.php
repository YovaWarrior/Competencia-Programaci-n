@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestión de Usuarios</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.usuarios.catalogo') }}" class="btn btn-primary">
                <i class="fas fa-list"></i> Ver Catálogo Completo
            </a>
            <button type="button" class="btn btn-success" onclick="exportarUsuarios()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" class="form-control" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, email o DPI...">
                </div>
                <div class="col-md-2 mb-3">
                    <select class="form-control" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <select class="form-control" name="membresia">
                        <option value="">Todas las membresías</option>
                        <option value="con_membresia" {{ request('membresia') == 'con_membresia' ? 'selected' : '' }}>Con membresía</option>
                        <option value="sin_membresia" {{ request('membresia') == 'sin_membresia' ? 'selected' : '' }}>Sin membresía</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
                <div class="col-md-2 mb-3">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Usuarios Registrados ({{ $usuarios->total() }} total)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Usuario</th>
                            <th>Contacto</th>
                            <th>Membresía</th>
                            <th>Estado</th>
                            <th>Estadísticas</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($usuario->foto)
                                        <img src="{{ asset('storage/' . $usuario->foto) }}" class="rounded-circle me-2" width="40" height="40" alt="Foto">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            <span class="text-white font-weight-bold">{{ substr($usuario->nombre, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                                        <small class="text-muted">DPI: {{ $usuario->dpi }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $usuario->email }}</div>
                                <small class="text-muted">{{ $usuario->telefono ?: 'Sin teléfono' }}</small>
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
                                    <span class="badge badge-danger">Suspendido</span>
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    <div><strong>Usos:</strong> {{ $usuario->usos_bicicletas_count }}</div>
                                    <div><strong>Reportes:</strong> 0</div>
                                    <div><strong>Puntos:</strong> {{ number_format($usuario->puntos_verdes) }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="small">{{ $usuario->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted small">{{ $usuario->created_at->diffForHumans() }}</div>
                            </td>
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
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>No se encontraron usuarios.</p>
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

<!-- Modal para cambiar estado -->
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
                        <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
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

@push('scripts')
<script>
function exportarUsuarios() {
    window.location.href = "{{ route('admin.reportes.exportar', 'usuarios') }}";
}

function cambiarEstadoUsuario(usuarioId, accion) {
    const modal = $('#cambiarEstadoModal');
    const form = $('#cambiarEstadoForm');
    const texto = $('#cambiarEstadoTexto');
    const boton = $('#confirmarCambio');
    
    if (accion === 'suspender') {
        form.attr('action', `/admin/usuarios/${usuarioId}/suspender`);
        texto.text('¿Está seguro de que desea suspender este usuario?');
        boton.removeClass('btn-success').addClass('btn-warning').text('Suspender');
    } else {
        form.attr('action', `/admin/usuarios/${usuarioId}/activar`);
        texto.text('¿Está seguro de que desea activar este usuario?');
        boton.removeClass('btn-warning').addClass('btn-success').text('Activar');
    }
    
    modal.modal('show');
}
</script>
@endpush