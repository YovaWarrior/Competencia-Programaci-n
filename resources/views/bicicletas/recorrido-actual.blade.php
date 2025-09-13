@extends('layouts.app')

@section('title', 'Mi Recorrido Actual - EcoBici')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if($usoEnCurso)
                <!-- Recorrido en curso -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-0">
                                    <i class="fas fa-bicycle me-2"></i>
                                    Recorrido en Curso
                                </h4>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-light text-success fs-6">
                                    <i class="fas fa-clock me-1"></i>
                                    <span id="tiempo-transcurrido">00:00:00</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-bicycle text-success me-2"></i>Bicicleta</h6>
                                <p class="mb-2">
                                    <strong>{{ $usoEnCurso->bicicleta->codigo }}</strong> 
                                    <span class="badge bg-{{ $usoEnCurso->bicicleta->tipo === 'electrica' ? 'warning' : 'info' }}">
                                        {{ ucfirst($usoEnCurso->bicicleta->tipo) }}
                                    </span>
                                </p>
                                
                                <h6><i class="fas fa-map-marker-alt text-danger me-2"></i>Estación de Inicio</h6>
                                <p class="mb-2">{{ $usoEnCurso->estacionInicio->nombre }}</p>
                                
                                <h6><i class="fas fa-clock text-primary me-2"></i>Hora de Inicio</h6>
                                <p class="mb-0">{{ $usoEnCurso->fecha_hora_inicio->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <div class="bg-light rounded p-4">
                                        <i class="fas fa-route fa-3x text-success mb-3"></i>
                                        <h5>¡Disfruta tu recorrido!</h5>
                                        <p class="text-muted">Recuerda devolver la bicicleta en una estación activa</p>
                                        <a href="{{ route('bicicletas.mostrar-uso', $usoEnCurso->id) }}" class="btn btn-success">
                                            <i class="fas fa-flag-checkered me-2"></i>
                                            Finalizar Recorrido
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Información del Recorrido
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary mb-1" id="tiempo-minutos">0</h4>
                                            <small class="text-muted">Minutos</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-1">{{ $usoEnCurso->userMembresia->minutosRestantes() }}</h4>
                                        <small class="text-muted">Min. Restantes</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-leaf me-2"></i>
                                    Impacto Ambiental
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-success mb-1" id="co2-estimado">0.0</h4>
                                            <small class="text-muted">kg CO₂ Reducido</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-warning mb-1" id="puntos-estimados">0</h4>
                                        <small class="text-muted">Puntos Estimados</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consejos y tips -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Consejos para tu Recorrido
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                                    <h6>Seguridad</h6>
                                    <small class="text-muted">Usa casco y respeta las señales de tránsito</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <i class="fas fa-battery-half fa-2x text-warning mb-2"></i>
                                    <h6>Batería</h6>
                                    <small class="text-muted">Revisa el nivel de batería en bicicletas eléctricas</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <i class="fas fa-map-marked-alt fa-2x text-success mb-2"></i>
                                    <h6>Estaciones</h6>
                                    <small class="text-muted">Planifica tu destino cerca de una estación</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- No hay recorrido en curso -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bicycle fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">No tienes un recorrido en curso</h4>
                        <p class="text-muted mb-4">¡Inicia un nuevo recorrido y comienza a explorar la ciudad de forma sostenible!</p>
                        <a href="{{ route('bicicletas.seleccionar') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-bicycle me-2"></i>
                            Iniciar Nuevo Recorrido
                        </a>
                    </div>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-bicycle fa-2x text-primary mb-2"></i>
                                <h5>{{ Auth::user()->usosBicicletas()->where('estado', 'completado')->count() }}</h5>
                                <small class="text-muted">Recorridos Totales</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-leaf fa-2x text-success mb-2"></i>
                                <h5>{{ number_format(Auth::user()->co2_reducido_total, 1) }} kg</h5>
                                <small class="text-muted">CO₂ Reducido</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                                <h5>{{ Auth::user()->puntos_verdes }}</h5>
                                <small class="text-muted">Puntos Verdes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-clock fa-2x text-info mb-2"></i>
                                <h5>{{ Auth::user()->membresiaActiva ? Auth::user()->membresiaActiva->minutosRestantes() : 0 }}</h5>
                                <small class="text-muted">Min. Restantes</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@if($usoEnCurso)
@push('scripts')
<script>
// Cronómetro y cálculos en tiempo real
function actualizarTiempo() {
    const inicio = new Date('{{ $usoEnCurso->fecha_hora_inicio->toISOString() }}');
    const ahora = new Date();
    const diferencia = ahora - inicio;
    
    const horas = Math.floor(diferencia / (1000 * 60 * 60));
    const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
    const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);
    const totalMinutos = Math.floor(diferencia / (1000 * 60));
    
    const tiempoFormateado = 
        String(horas).padStart(2, '0') + ':' +
        String(minutos).padStart(2, '0') + ':' +
        String(segundos).padStart(2, '0');
    
    document.getElementById('tiempo-transcurrido').textContent = tiempoFormateado;
    document.getElementById('tiempo-minutos').textContent = totalMinutos;
    
    // Cálculos estimados
    const distanciaEstimada = (totalMinutos / 60) * 15; // 15 km/h promedio
    const co2Estimado = distanciaEstimada * 0.23; // 0.23 kg CO2 por km
    let puntosEstimados = Math.floor(co2Estimado * 10);
    
    @if($usoEnCurso->bicicleta->tipo === 'electrica')
        puntosEstimados = Math.floor(puntosEstimados * 1.5);
    @endif
    
    document.getElementById('co2-estimado').textContent = co2Estimado.toFixed(1);
    document.getElementById('puntos-estimados').textContent = puntosEstimados;
}

// Actualizar cada segundo
setInterval(actualizarTiempo, 1000);
actualizarTiempo(); // Ejecutar inmediatamente
</script>
@endpush
@endif
