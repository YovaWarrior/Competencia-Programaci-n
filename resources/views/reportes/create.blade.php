@extends('layouts.app')

@section('title', 'Reportar Daño')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Reportar Daño en Bicicleta
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('reportes.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="bicicleta_id" class="form-label">Bicicleta</label>
                            <select class="form-select @error('bicicleta_id') is-invalid @enderror" 
                                    id="bicicleta_id" name="bicicleta_id" required>
                                <option value="">Selecciona la bicicleta</option>
                                @foreach($bicicletas as $bicicleta)
                                    <option value="{{ $bicicleta->id }}" {{ old('bicicleta_id') == $bicicleta->id ? 'selected' : '' }}>
                                        Bicicleta #{{ $bicicleta->numero_serie }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bicicleta_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tipo_dano" class="form-label">Tipo de Daño</label>
                            <input type="text" class="form-control @error('tipo_dano') is-invalid @enderror" 
                                   id="tipo_dano" name="tipo_dano" value="{{ old('tipo_dano') }}" 
                                   placeholder="Ej: Llanta ponchada, frenos defectuosos, cadena rota..." required>
                            @error('tipo_dano')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="severidad" class="form-label">¿Qué tan grave es?</label>
                            <select class="form-select @error('severidad') is-invalid @enderror" 
                                    id="severidad" name="severidad" required>
                                <option value="">Selecciona...</option>
                                <option value="leve" {{ old('severidad') == 'leve' ? 'selected' : '' }}>
                                    🟢 Leve - Se puede usar con cuidado
                                </option>
                                <option value="moderado" {{ old('severidad') == 'moderado' ? 'selected' : '' }}>
                                    🟡 Moderado - Necesita reparación pronto
                                </option>
                                <option value="severo" {{ old('severidad') == 'severo' ? 'selected' : '' }}>
                                    🔴 Severo - No se debe usar
                                </option>
                            </select>
                            @error('severidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Describe el problema</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Explica brevemente qué está mal con la bicicleta..." required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-paper-plane me-1"></i>
                                Enviar Reporte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
