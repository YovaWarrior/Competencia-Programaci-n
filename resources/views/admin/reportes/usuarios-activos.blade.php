@extends('layouts.admin')

@section('title', 'Usuarios Activos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Usuarios Activos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Usuarios Activos</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="{{ route('admin.reportes.exportar', 'usuarios-activos') }}" class="btn btn-outline-success">
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
                                Total Usuarios Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['total_usuarios_activos']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                                CO₂ Total Reducido
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['total_co2_reducido'], 2) }} kg</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-leaf fa-2x text-gray-300"></i>
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
                                Total Minutos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($estadisticas['total_minutos']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios Activos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Usuarios Activos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Membresía</th>
                            <th>Total Usos</th>
                            <th>CO₂ Reducido</th>
                            <th>Tiempo Total</th>
                            <th>Puntos Verdes</th>
                            <th>Último Uso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuariosActivos as $usuario)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $usuario->foto_url }}" class="rounded-circle me-2" width="40" height="40" alt="Avatar">
                                    <div>
                                        <div class="font-weight-bold">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                                        <div class="text-muted small">{{ $usuario->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($usuario->membresiaActiva)
                                    <span class="badge badge-success">{{ $usuario->membresiaActiva->membresia->nombre }}</span>
                                    <div class="text-muted small">Vence: {{ $usuario->membresiaActiva->fecha_fin->format('d/m/Y') }}</div>
                                @else
                                    <span class="badge badge-secondary">Sin membresía</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-weight-bold text-primary">{{ number_format($usuario->total_usos) }}</span>
                            </td>
                            <td>
                                <span class="text-success">{{ number_format($usuario->uso_bicicletas_sum_co2_reducido ?? 0, 2) }} kg</span>
                            </td>
                            <td>
                                <span class="text-info">{{ number_format(($usuario->uso_bicicletas_sum_duracion_minutos ?? 0) / 60, 1) }} hrs</span>
                            </td>
                            <td>
                                <span class="text-warning">{{ number_format($usuario->puntos_verdes) }}</span>
                            </td>
                            <td>
                                @if($usuario->usosBicicletas->first())
                                    {{ $usuario->usosBicicletas->first()->fecha_hora_inicio->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Sin usos</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3 text-gray-300"></i>
                                <p>No hay usuarios activos registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($usuariosActivos->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $usuariosActivos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
