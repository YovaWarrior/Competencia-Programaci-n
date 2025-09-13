@extends('layouts.app')

@section('title', 'Mi Perfil - EcoBici')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Mi Perfil
                </h4>
            </div>
            <div class="card-body p-4">

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Foto de perfil actual -->
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img src="{{ $user->foto_url }}" alt="Foto de perfil" 
                                 class="rounded-circle border border-3 border-success" 
                                 style="width: 120px; height: 120px; object-fit: cover;" 
                                 id="current-photo">
                            <div class="position-absolute bottom-0 end-0">
                                <label for="foto" class="btn btn-success btn-sm rounded-circle" 
                                       style="width: 35px; height: 35px; cursor: pointer;">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>
                        </div>
                        <h5 class="mt-2 mb-0">{{ $user->nombre }} {{ $user->apellido }}</h5>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                    
                    <!-- Campo oculto para foto -->
                    <input type="file" id="foto" name="foto" accept="image/*" style="display: none;">
                    @error('foto')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" name="nombre" value="{{ old('nombre', $user->nombre) }}" required>
                                </div>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                                           id="apellido" name="apellido" value="{{ old('apellido', $user->apellido) }}" required>
                                </div>
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control @error('telefono') is-invalid @enderror" 
                                           id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}" required>
                                </div>
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">DPI</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" class="form-control" value="{{ $user->dpi }}" readonly>
                                </div>
                                <small class="text-muted">El DPI no se puede modificar</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control" value="{{ $user->fecha_nacimiento->format('Y-m-d') }}" readonly>
                                </div>
                                <small class="text-muted">La fecha de nacimiento no se puede modificar</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>
                            Actualizar Perfil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('foto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const currentPhoto = document.getElementById('current-photo');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            currentPhoto.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
