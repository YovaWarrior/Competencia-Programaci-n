@extends('layouts.app')

@section('title', 'Bienvenido a EcoBici Puerto Barrios')

@section('content')
<div class="hero-section text-center py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card bg-white shadow-lg">
                <div class="card-body p-5">
                    <i class="fas fa-bicycle fa-4x text-success mb-4"></i>
                    <h1 class="display-4 fw-bold text-primary mb-4">
                        EcoBici Puerto Barrios
                    </h1>
                    <p class="lead text-muted mb-4">
                        El sistema de bicicletas compartidas que está transformando la movilidad en Puerto Barrios. 
                        Únete a la revolución verde y descubre una nueva forma de moverte por la ciudad.
                    </p>
                    
                    <div class="row text-center mb-4">
                        <div class="col-md-4">
                            <i class="fas fa-leaf fa-2x text-success mb-2"></i>
                            <h5>Eco-Amigable</h5>
                            <p class="small">Reduce tu huella de carbono</p>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-map-marked-alt fa-2x text-primary mb-2"></i>
                            <h5>8 Estaciones</h5>
                            <p class="small">Por todo Puerto Barrios</p>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-clock fa-2x text-info mb-2"></i>
                            <h5>24/7</h5>
                            <p class="small">Disponible siempre</p>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-success btn-lg me-md-2">
                                <i class="fas fa-user-plus me-2"></i>Únete Ahora
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg me-md-2">
                                <i class="fas fa-tachometer-alt me-2"></i>Mi Dashboard
                            </a>
                            <a href="{{ route('bicicletas.seleccionar') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-bicycle me-2"></i>Usar Bicicleta
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas -->
<div class="row mt-5">
    <div class="col-md-3">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4>500+</h4>
                <p>Usuarios Activos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <i class="fas fa-bicycle fa-2x mb-2"></i>
                <h4>100</h4>
                <p>Bicicletas Disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <i class="fas fa-route fa-2x mb-2"></i>
                <h4>1,200+</h4>
                <p>Recorridos Realizados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-warning text-white">
            <div class="card-body">
                <i class="fas fa-leaf fa-2x mb-2"></i>
                <h4>250kg</h4>
                <p>CO₂ Reducido</p>
            </div>
        </div>
    </div>
</div>
@endsection
