@extends('layouts.admin')

@section('title', 'Reporte de Ingresos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Reporte de Ingresos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reportes.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Ingresos</li>
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

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ingresos Totales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Q{{ number_format($ingresos->sum('monto_pagado'), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Total Transacciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ingresos->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
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
                                Promedio por Transacción
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Q{{ $ingresos->count() > 0 ? number_format($ingresos->avg('monto_pagado'), 2) : '0.00' }}</div>
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
                                Ingresos Este Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Q{{ number_format($ingresos->where('created_at', '>=', now()->startOfMonth())->sum('monto_pagado'), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
            <form method="GET" action="{{ route('admin.reportes.ingresos') }}">
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
                        <label for="metodo_pago">Método de Pago</label>
                        <select class="form-control" name="metodo_pago">
                            <option value="">Todos los métodos</option>
                            <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="tarjeta" {{ request('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="transferencia" {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.reportes.ingresos') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Ingresos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Historial de Ingresos ({{ $ingresos->total() }} registros)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Membresía</th>
                            <th>Monto</th>
                            <th>Método de Pago</th>
                            <th>Referencia</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingresos as $ingreso)
                        <tr>
                            <td>{{ $ingreso->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="font-weight-bold">{{ $ingreso->user->nombre }} {{ $ingreso->user->apellido }}</div>
                                        <div class="text-muted small">{{ $ingreso->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $ingreso->membresia->nombre }}</div>
                                <small class="text-muted">{{ $ingreso->membresia->duracion_dias }} días</small>
                            </td>
                            <td>
                                <span class="font-weight-bold text-success">Q{{ number_format($ingreso->monto_pagado, 2) }}</span>
                            </td>
                            <td>
                                @switch($ingreso->metodo_pago)
                                    @case('efectivo')
                                        <span class="badge badge-success">Efectivo</span>
                                        @break
                                    @case('tarjeta')
                                        <span class="badge badge-primary">Tarjeta</span>
                                        @break
                                    @case('transferencia')
                                        <span class="badge badge-info">Transferencia</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ ucfirst($ingreso->metodo_pago) }}</span>
                                @endswitch
                            </td>
                            <td>
                                <code>{{ $ingreso->referencia_pago ?? 'N/A' }}</code>
                            </td>
                            <td>
                                @switch($ingreso->estado_pago)
                                    @case('pagado')
                                        <span class="badge badge-success">Pagado</span>
                                        @break
                                    @case('pendiente')
                                        <span class="badge badge-warning">Pendiente</span>
                                        @break
                                    @default
                                        <span class="badge badge-danger">Cancelado</span>
                                @endswitch
                            </td>
                            <td>{{ $ingreso->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-dollar-sign fa-3x mb-3 d-block"></i>
                                No se encontraron registros de ingresos
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($ingresos->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $ingresos->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportarExcel() {
    window.location.href = "{{ route('admin.reportes.exportar', 'ingresos') }}?" + new URLSearchParams(window.location.search);
}
</script>
@endsection
