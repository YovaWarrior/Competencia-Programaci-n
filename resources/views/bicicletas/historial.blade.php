@extends('layouts.app')

@section('title', 'Historial de Recorridos - EcoBici')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Historial de Recorridos
                            </h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('bicicletas.seleccionar') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-bicycle me-1"></i>
                                Nuevo Recorrido
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($usos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Fecha</th>
                                        <th><i class="fas fa-bicycle me-1"></i>Bicicleta</th>
                                        <th><i class="fas fa-map-marker-alt me-1"></i>Origen</th>
                                        <th><i class="fas fa-flag-checkered me-1"></i>Destino</th>
                                        <th><i class="fas fa-clock me-1"></i>Duración</th>
                                        <th><i class="fas fa-route me-1"></i>Distancia</th>
                                        <th><i class="fas fa-leaf me-1"></i>CO₂ Reducido</th>
                                        <th><i class="fas fa-coins me-1"></i>Puntos</th>
                                        <th><i class="fas fa-star me-1"></i>Calificación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usos as $uso)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $uso->fecha_hora_inicio->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $uso->fecha_hora_inicio->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-bicycle text-{{ $uso->bicicleta->tipo === 'electrica' ? 'warning' : 'info' }} me-2"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ $uso->bicicleta->codigo }}</div>
                                                        <small class="badge bg-{{ $uso->bicicleta->tipo === 'electrica' ? 'warning' : 'info' }}">
                                                            {{ ucfirst($uso->bicicleta->tipo) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-success me-1"></i>
                                                {{ $uso->estacionInicio->nombre }}
                                            </td>
                                            <td>
                                                <i class="fas fa-flag-checkered text-danger me-1"></i>
                                                {{ $uso->estacionFin->nombre }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ floor($uso->duracion_minutos / 60) }}h {{ $uso->duracion_minutos % 60 }}m
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ number_format($uso->distancia_recorrida, 1) }} km</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">
                                                    <i class="fas fa-leaf me-1"></i>
                                                    {{ number_format($uso->co2_reducido, 2) }} kg
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-coins me-1"></i>
                                                    {{ $uso->puntos_verdes_ganados }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($uso->calificacion)
                                                    <div class="text-warning">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star{{ $i <= $uso->calificacion ? '' : '-o' }}"></i>
                                                        @endfor
                                                    </div>
                                                @else
                                                    <span class="text-muted">Sin calificar</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            data-bs-toggle="modal" data-bs-target="#detalleModal{{ $uso->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($uso->ruta)
                                                        <a href="{{ route('rutas.show', $uso->ruta->id) }}" 
                                                           class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-route"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal de detalles -->
                                        <div class="modal fade" id="detalleModal{{ $uso->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            Detalles del Recorrido
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6><i class="fas fa-bicycle text-info me-2"></i>Información de la Bicicleta</h6>
                                                                <ul class="list-unstyled">
                                                                    <li><strong>Código:</strong> {{ $uso->bicicleta->codigo }}</li>
                                                                    <li><strong>Tipo:</strong> {{ ucfirst($uso->bicicleta->tipo) }}</li>
                                                                    <li><strong>Marca:</strong> {{ $uso->bicicleta->marca ?? 'N/A' }}</li>
                                                                </ul>

                                                                <h6><i class="fas fa-clock text-primary me-2"></i>Tiempo</h6>
                                                                <ul class="list-unstyled">
                                                                    <li><strong>Inicio:</strong> {{ $uso->fecha_hora_inicio->format('d/m/Y H:i:s') }}</li>
                                                                    <li><strong>Fin:</strong> {{ $uso->fecha_hora_fin->format('d/m/Y H:i:s') }}</li>
                                                                    <li><strong>Duración:</strong> {{ floor($uso->duracion_minutos / 60) }}h {{ $uso->duracion_minutos % 60 }}m</li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6><i class="fas fa-map-marker-alt text-success me-2"></i>Recorrido</h6>
                                                                <ul class="list-unstyled">
                                                                    <li><strong>Origen:</strong> {{ $uso->estacionInicio->nombre }}</li>
                                                                    <li><strong>Destino:</strong> {{ $uso->estacionFin->nombre }}</li>
                                                                    <li><strong>Distancia:</strong> {{ number_format($uso->distancia_recorrida, 2) }} km</li>
                                                                    @if($uso->ruta)
                                                                        <li><strong>Ruta:</strong> {{ $uso->ruta->nombre }}</li>
                                                                    @endif
                                                                </ul>

                                                                <h6><i class="fas fa-chart-line text-warning me-2"></i>Impacto</h6>
                                                                <ul class="list-unstyled">
                                                                    <li><strong>CO₂ Reducido:</strong> {{ number_format($uso->co2_reducido, 2) }} kg</li>
                                                                    <li><strong>Puntos Ganados:</strong> {{ $uso->puntos_verdes_ganados }}</li>
                                                                    @if($uso->costo_extra > 0)
                                                                        <li><strong>Costo Extra:</strong> Q{{ number_format($uso->costo_extra, 2) }}</li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        
                                                        @if($uso->comentarios)
                                                            <hr>
                                                            <h6><i class="fas fa-comment text-secondary me-2"></i>Comentarios</h6>
                                                            <p class="text-muted">{{ $uso->comentarios }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $usos->links() }}
                        </div>

                        <!-- Resumen estadístico -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-bicycle fa-2x mb-2"></i>
                                        <h5>{{ $usos->total() }}</h5>
                                        <small>Recorridos Totales</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-route fa-2x mb-2"></i>
                                        <h5>{{ number_format($usos->sum('distancia_recorrida'), 1) }} km</h5>
                                        <small>Distancia Total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body text-center">
                                        <i class="fas fa-leaf fa-2x mb-2"></i>
                                        <h5>{{ number_format($usos->sum('co2_reducido'), 1) }} kg</h5>
                                        <small>CO₂ Reducido</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-coins fa-2x mb-2"></i>
                                        <h5>{{ $usos->sum('puntos_verdes_ganados') }}</h5>
                                        <small>Puntos Ganados</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bicycle fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No tienes recorridos registrados</h5>
                            <p class="text-muted">¡Comienza tu primer recorrido en EcoBici!</p>
                            <a href="{{ route('bicicletas.seleccionar') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-bicycle me-2"></i>
                                Iniciar Recorrido
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection