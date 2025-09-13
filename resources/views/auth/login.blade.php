@extends('layouts.app')

@section('title', 'Iniciar Sesión - EcoBici')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Iniciar Sesión
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Recordarme
                        </label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Iniciar Sesión
                        </button>
                    </div>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p class="mb-0">¿No tienes cuenta? 
                        <a href="{{ route('register') }}" class="text-decoration-none">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
                
                <!-- Credenciales de prueba -->
                <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                        <strong>Credenciales de prueba:</strong><br>
                        Admin: admin@ecobici.gt / admin123<br>
                        Usuario: maria@example.com / password123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
