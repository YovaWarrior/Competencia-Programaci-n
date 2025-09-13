@extends('layouts.app')

@section('title', 'Detalle del Reporte')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Reporte #{{ $reporte->id }}
                    </h4>
                    <div>
                        @if($reporte->estado === 'reportado')
                            <span class="badge bg-secondary fs-6">Reportado</span>
                        @elseif($reporte->estado === 'en_revision')
                            <span class="badge bg-info fs-6">En Revisión</span>
                        @elseif($reporte->estado === 'reparado')
                            <span class="badge bg-success fs-6">Reparado</span>
                        @else
                            <span class="badge bg-dark fs-6">Descartado</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información del Reporte</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Fecha:</strong></td>
                                    <td>{{ $reporte->fecha_reporte->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bicicleta:</strong></td>
                                    <td>
                                        <i class="fas fa-bicycle text-primary me-1"></i>
                                        #{{ $reporte->bicicleta->numero_serie ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo de Daño:</strong></td>
                                    <td>{{ $reporte->tipo_dano }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Gravedad:</strong></td>
                                    <td>
                                        @if($reporte->severidad === 'leve')
                                            <span class="badge bg-success">🟢 Leve</span>
                                        @elseif($reporte->severidad === 'moderado')
                                            <span class="badge bg-warning">🟡 Moderado</span>
                                        @else
                                            <span class="badge bg-danger">🔴 Severo</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($reporte->fecha_resolucion)
                                <tr>
                                    <td><strong>Resuelto:</strong></td>
                                    <td>{{ $reporte->fecha_resolucion->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Descripción del Problema</h5>
                            <div class="bg-light p-3 rounded">
                                {{ $reporte->descripcion }}
                            </div>
                            
                            @if($reporte->comentarios_tecnico)
                                <h5 class="mt-4">Comentarios del Técnico</h5>
                                <div class="bg-info bg-opacity-10 p-3 rounded border-start border-info border-4">
                                    {{ $reporte->comentarios_tecnico }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Volver a Mis Reportes
                                </a>
                                
                                @if($reporte->estado === 'reportado')
                                    <span class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Esperando revisión del equipo técnico
                                    </span>
                                @elseif($reporte->estado === 'en_revision')
                                    <span class="text-info">
                                        <i class="fas fa-search me-1"></i>
                                        En proceso de revisión
                                    </span>
                                @elseif($reporte->estado === 'reparado')
                                    <span class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        ¡Problema resuelto!
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
