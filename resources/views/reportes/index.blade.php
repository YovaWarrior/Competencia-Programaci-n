@extends('layouts.app')

@section('title', 'Mis Reportes')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-list text-primary me-2"></i>
                        Mis Reportes de Daños
                    </h4>
                    <a href="{{ route('reportes.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus me-1"></i>
                        Reportar Daño
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($reportes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Bicicleta</th>
                                        <th>Problema</th>
                                        <th>Gravedad</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportes as $reporte)
                                        <tr>
                                            <td>
                                                <small>{{ $reporte->fecha_reporte->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <i class="fas fa-bicycle text-primary me-1"></i>
                                                #{{ $reporte->bicicleta->numero_serie ?? 'N/A' }}
                                            </td>
                                            <td>{{ $reporte->tipo_dano }}</td>
                                            <td>
                                                @if($reporte->severidad === 'leve')
                                                    <span class="badge bg-success">🟢 Leve</span>
                                                @elseif($reporte->severidad === 'moderado')
                                                    <span class="badge bg-warning">🟡 Moderado</span>
                                                @else
                                                    <span class="badge bg-danger">🔴 Severo</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($reporte->estado === 'reportado')
                                                    <span class="badge bg-secondary">Reportado</span>
                                                @elseif($reporte->estado === 'en_revision')
                                                    <span class="badge bg-info">En Revisión</span>
                                                @elseif($reporte->estado === 'reparado')
                                                    <span class="badge bg-success">Reparado</span>
                                                @else
                                                    <span class="badge bg-dark">Descartado</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('reportes.show', $reporte) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $reportes->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No has reportado ningún daño</h5>
                            <p class="text-muted">Cuando encuentres algún problema con una bicicleta, puedes reportarlo aquí.</p>
                            <a href="{{ route('reportes.create') }}" class="btn btn-warning">
                                <i class="fas fa-plus me-1"></i>
                                Reportar Primer Daño
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
