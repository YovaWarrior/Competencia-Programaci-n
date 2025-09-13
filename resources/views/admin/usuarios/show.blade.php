@extends('layouts.admin')

@section('title', 'Detalle de Usuario')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detalle de Usuario</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">{{ $usuario->nombre }} {{ $usuario->apellido }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            @if($usuario->activo)
                <button class="btn btn-warning" onclick="cambiarEstadoUsuario({{ $usuario->id }}, 'suspender')">
                    <i class="fas fa-user-slash"></i> Suspender
                </button>
            @else
                <button class="btn btn-success" onclick="cambiarEstadoUsuario({{ $usuario->id }}, 'activar')">
                    <i class="fas fa-user-check"></i> Activar
                </button>
            @endif
        </div>
    </div>

    <!-- Información Personal -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                </div>
                <div class="card-body text-center">
                    @if($usuario->foto)
                        <img src="{{ asset('storage/' . $usuario->foto) }}" class="rounded-circle mb-3" width="120" height="120" alt="Foto">
                    @else
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                            <span class="text-white h2 mb-0">{{ substr($usuario->nombre, 0, 1) }}</span>
                        </div>
                    @endif
                    <h5 class="card-title">{{ $usuario->nombre }} {{ $usuario->apellido }}</h5>
                    <p class="text-muted">{{ $usuario->email }}</p>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h6 class="text-primary">{{ number_format($usuario->puntos_verdes) }}</h6>
                                <small class="text-muted">Puntos Verdes</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="text-success">{{ number_format($usuario->co2_reducido_total, 2) }} kg</h6>
                            <small class="text-muted">CO₂ Reducido</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Datos Personales</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>DPI:</strong></td>
                                    <td>{{ $usuario->dpi }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Teléfono:</strong></td>
                                    <td>{{ $usuario->telefono ?: 'No registrado' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Nacimiento:</strong></td>
                                    <td>
                                        @if($usuario->fecha_nacimiento)
                                            {{ $usuario->fecha_nacimiento->format('d/m/Y') }}
                                            ({{ $usuario->fecha_nacimiento->age }} años)
                                        @else
                                            No registrada
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        @if($usuario->activo)
                                            <span class="badge badge-success">Activo</span>
                                        @else
                                            <span class="badge badge-danger">Suspendido</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Registro:</strong></td>
                                    <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Última Actividad:</strong></td>
                                    <td>{{ $usuario->updated_at->diffForHumans() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Membresías -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Historial de Membresías</h6>
                </div>
                <div class="card-body">
                    @if($usuario->membresias->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Membresía</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th>Monto Pagado</th>
                                        <th>Método de Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuario->membresias as $membresia)
                                    <tr>
                                        <td>{{ $membresia->membresia->nombre }}</td>
                                        <td>{{ $membresia->fecha_inicio->format('d/m/Y') }}</td>
                                        <td>{{ $membresia->fecha_fin->format('d/m/Y') }}</td>
                                        <td>
                                            @if($membresia->activa)
                                                <span class="badge badge-success">Activa</span>
                                            @else
                                                <span class="badge badge-secondary">Expirada</span>
                                            @endif
                                        </td>
                                        <td>Q{{ number_format($membresia->monto_pagado, 2) }}</td>
                                        <td>{{ ucfirst($membresia->metodo_pago) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No tiene historial de membresías.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Uso -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Historial de Uso de Bicicletas (Últimos 10)</h6>
                </div>
                <div class="card-body">
                    @if($usuario->usosBicicletas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Bicicleta</th>
                                        <th>Duración</th>
                                        <th>Distancia</th>
                                        <th>CO₂ Reducido</th>
                                        <th>Puntos Ganados</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuario->usosBicicletas->take(10) as $uso)
                                    <tr>
                                        <td>{{ $uso->fecha_hora_inicio->format('d/m/Y H:i') }}</td>
                                        <td>{{ $uso->bicicleta->codigo }}</td>
                                        <td>{{ $uso->duracion_minutos }} min</td>
                                        <td>{{ number_format($uso->distancia_recorrida, 2) }} km</td>
                                        <td>{{ number_format($uso->co2_reducido, 2) }} kg</td>
                                        <td>{{ $uso->puntos_verdes_ganados }}</td>
                                        <td>
                                            <span class="badge badge-{{ $uso->estado == 'completado' ? 'success' : 'warning' }}">
                                                {{ ucfirst($uso->estado) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No tiene historial de uso de bicicletas.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes de Daños -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Reportes de Daños</h6>
                </div>
                <div class="card-body">
                    @if($usuario->reportesDanos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Bicicleta</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Fecha Resolución</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuario->reportesDanos as $reporte)
                                    <tr>
                                        <td>{{ $reporte->fecha_reporte->format('d/m/Y H:i') }}</td>
                                        <td>{{ $reporte->bicicleta->codigo }}</td>
                                        <td>{{ $reporte->descripcion }}</td>
                                        <td>
                                            <span class="badge badge-{{ $reporte->estado == 'reparado' ? 'success' : ($reporte->estado == 'en_revision' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($reporte->estado) }}
                                            </span>
                                        </td>
                                        <td>{{ $reporte->fecha_resolucion ? $reporte->fecha_resolucion->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No ha reportado daños.</p>
                    @endif
                </div>
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