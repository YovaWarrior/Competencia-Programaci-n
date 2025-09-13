@extends('layouts.app')

@section('title', 'Recorrido en Curso - EcoBici')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header del recorrido -->
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
                                <strong>{{ $uso->bicicleta->codigo }}</strong> 
                                <span class="badge bg-{{ $uso->bicicleta->tipo === 'electrica' ? 'warning' : 'info' }}">
                                    {{ ucfirst($uso->bicicleta->tipo) }}
                                </span>
                            </p>
                            
                            <h6><i class="fas fa-map-marker-alt text-danger me-2"></i>Estación de Inicio</h6>
                            <p class="mb-2">{{ $uso->estacionInicio->nombre }}</p>
                            
                            <h6><i class="fas fa-clock text-primary me-2"></i>Hora de Inicio</h6>
                            <p class="mb-0">{{ $uso->fecha_hora_inicio->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="bg-light rounded p-4">
                                    <i class="fas fa-route fa-3x text-success mb-3"></i>
                                    <h5>¡Disfruta tu recorrido!</h5>
                                    <p class="text-muted">Recuerda devolver la bicicleta en una estación activa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario para finalizar recorrido -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-flag-checkered me-2"></i>
                        Finalizar Recorrido
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bicicletas.finalizar-uso', $uso->id) }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estacion_fin_id" class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        Estación de Destino *
                                    </label>
                                    <select class="form-select @error('estacion_fin_id') is-invalid @enderror" 
                                            id="estacion_fin_id" name="estacion_fin_id" required>
                                        <option value="">Selecciona una estación</option>
                                        @foreach($estaciones as $estacion)
                                            <option value="{{ $estacion->id }}" {{ old('estacion_fin_id') == $estacion->id ? 'selected' : '' }}>
                                                {{ $estacion->nombre }} 
                                                ({{ $estacion->bicicletas_disponibles }}/{{ $estacion->capacidad }} disponibles)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('estacion_fin_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="calificacion" class="form-label">
                                        <i class="fas fa-star me-1"></i>
                                        Calificación del Recorrido
                                    </label>
                                    <select class="form-select @error('calificacion') is-invalid @enderror" 
                                            id="calificacion" name="calificacion">
                                        <option value="">Sin calificar</option>
                                        <option value="5" {{ old('calificacion') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ Excelente</option>
                                        <option value="4" {{ old('calificacion') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ Muy Bueno</option>
                                        <option value="3" {{ old('calificacion') == '3' ? 'selected' : '' }}>⭐⭐⭐ Bueno</option>
                                        <option value="2" {{ old('calificacion') == '2' ? 'selected' : '' }}>⭐⭐ Regular</option>
                                        <option value="1" {{ old('calificacion') == '1' ? 'selected' : '' }}>⭐ Malo</option>
                                    </select>
                                    @error('calificacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comentarios" class="form-label">
                                <i class="fas fa-comment me-1"></i>
                                Comentarios (Opcional)
                            </label>
                            <textarea class="form-control @error('comentarios') is-invalid @enderror" 
                                      id="comentarios" name="comentarios" rows="3" 
                                      placeholder="Comparte tu experiencia, sugerencias o reporta algún problema...">{{ old('comentarios') }}</textarea>
                            @error('comentarios')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver al Dashboard
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-flag-checkered me-2"></i>
                                Finalizar Recorrido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Cronómetro
function actualizarTiempo() {
    const inicio = new Date('{{ $uso->fecha_hora_inicio->toISOString() }}');
    const ahora = new Date();
    const diferencia = ahora - inicio;
    
    const horas = Math.floor(diferencia / (1000 * 60 * 60));
    const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
    const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);
    
    const tiempoFormateado = 
        String(horas).padStart(2, '0') + ':' +
        String(minutos).padStart(2, '0') + ':' +
        String(segundos).padStart(2, '0');
    
    document.getElementById('tiempo-transcurrido').textContent = tiempoFormateado;
}

// Actualizar cada segundo
setInterval(actualizarTiempo, 1000);
actualizarTiempo(); // Ejecutar inmediatamente

// Confirmación antes de finalizar
document.querySelector('form').addEventListener('submit', function(e) {
    const estacion = document.getElementById('estacion_fin_id');
    if (!estacion.value) {
        e.preventDefault();
        alert('Por favor selecciona una estación de destino');
        return;
    }
    
    if (!confirm('¿Estás seguro de que quieres finalizar el recorrido?')) {
        e.preventDefault();
    }
});
</script>
@endpush