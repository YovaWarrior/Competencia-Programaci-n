@extends('layouts.app')

@section('title', 'Pago de Membresía - EcoBici')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('membresias.index') }}">Membresías</a>
                    </li>
                    <li class="breadcrumb-item active">Pago</li>
                </ol>
            </nav>

            <!-- Información de la membresía seleccionada -->
            <div class="card mb-4 shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Confirmar Pago de Membresía
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="text-primary fw-bold">{{ $membresia->nombre }}</h5>
                            <p class="text-muted mb-2">{{ $membresia->descripcion }}</p>
                            
                            <!-- Detalles de la membresía -->
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Duración:</strong> 
                                    <span class="badge bg-info">{{ ucfirst($membresia->duracion) }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Tipo:</strong>
                                    <span class="badge bg-secondary">{{ ucfirst($membresia->tipo_bicicleta) }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Minutos incluidos:</strong>
                                    <span class="text-success">{{ number_format($membresia->minutos_incluidos) }} min</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Válida por:</strong>
                                    <span class="text-info">{{ $membresia->duracion_dias }} días</span>
                                </div>
                            </div>

                            <!-- Beneficios -->
                            @if($membresia->beneficios && count($membresia->beneficios) > 0)
                            <div class="mt-3">
                                <strong class="text-warning">
                                    <i class="fas fa-star me-1"></i>
                                    Beneficios incluidos:
                                </strong>
                                <ul class="list-unstyled mt-2">
                                    @foreach($membresia->beneficios as $beneficio)
                                    <li>
                                        <i class="fas fa-check text-success me-2"></i>
                                        <small>{{ $beneficio }}</small>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="price-display-large">
                                Q{{ number_format($membresia->precio, 0) }}
                            </div>
                            <small class="text-muted">{{ $membresia->duracion === 'mensual' ? 'por mes' : 'por año' }}</small>
                            
                            @if($membresia->duracion === 'anual')
                                <div class="mt-2">
                                    <span class="badge bg-success">
                                        💰 ¡Ahorra 20%!
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de pago -->
            <div class="card shadow-lg">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Información de Pago
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('membresias.procesar-pago', $membresia->id) }}" id="payment-form">
                        @csrf
                        
                        <!-- Método de pago -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-wallet me-2"></i>
                                Método de Pago
                            </label>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-option">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="tarjeta" required>
                                        <label class="form-check-label" for="tarjeta">
                                            <div class="payment-card">
                                                <i class="fas fa-credit-card fa-2x text-primary mb-2"></i>
                                                <div>Tarjeta de Crédito/Débito</div>
                                                <small class="text-muted">Visa, MasterCard</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-option">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="transferencia" value="transferencia" required>
                                        <label class="form-check-label" for="transferencia">
                                            <div class="payment-card">
                                                <i class="fas fa-university fa-2x text-success mb-2"></i>
                                                <div>Transferencia Bancaria</div>
                                                <small class="text-muted">Bancos nacionales</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-option">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="efectivo" value="efectivo" required>
                                        <label class="form-check-label" for="efectivo">
                                            <div class="payment-card">
                                                <i class="fas fa-money-bill fa-2x text-warning mb-2"></i>
                                                <div>Efectivo</div>
                                                <small class="text-muted">En estaciones EcoBici</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('metodo_pago')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Referencia de pago -->
                        <div class="mb-4">
                            <label for="referencia_pago" class="form-label fw-bold">
                                <i class="fas fa-receipt me-2"></i>
                                Referencia de Pago
                            </label>
                            <input type="text" 
                                   class="form-control @error('referencia_pago') is-invalid @enderror" 
                                   id="referencia_pago" 
                                   name="referencia_pago" 
                                   placeholder="Número de transacción, recibo o referencia"
                                   value="{{ old('referencia_pago') }}"
                                   required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Ingresa el número de transacción, recibo bancario o código de referencia del pago realizado.
                            </div>
                            @error('referencia_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Información adicional según método de pago -->
                        <div id="payment-info" class="mb-4" style="display: none;">
                            <!-- Información de tarjeta -->
                            <div id="tarjeta-info" class="payment-info-section">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-credit-card me-2"></i>Pago con Tarjeta</h6>
                                    <p class="mb-2">Realiza tu pago de <strong>Q{{ number_format($membresia->precio, 0) }}</strong> a través de nuestro procesador seguro.</p>
                                    <p class="mb-0"><small>Una vez procesado el pago, ingresa el número de autorización en el campo de referencia.</small></p>
                                </div>
                            </div>

                            <!-- Información de transferencia -->
                            <div id="transferencia-info" class="payment-info-section">
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-university me-2"></i>Datos para Transferencia</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Banco:</strong> Banco Industrial<br>
                                            <strong>Cuenta:</strong> 123-456789-0<br>
                                            <strong>Tipo:</strong> Monetaria
                                        </div>
                                        <div class="col-md-6">
                                            <strong>A nombre de:</strong> Municipalidad de Puerto Barrios<br>
                                            <strong>Monto:</strong> Q{{ number_format($membresia->precio, 0) }}<br>
                                        </div>
                                    </div>
                                    <hr>
                                    <p class="mb-0"><small>Después de realizar la transferencia, ingresa el número de comprobante en el campo de referencia.</small></p>
                                </div>
                            </div>

                            <!-- Información de efectivo -->
                            <div id="efectivo-info" class="payment-info-section">
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-money-bill me-2"></i>Pago en Efectivo</h6>
                                    <p class="mb-2">Dirígete a cualquiera de nuestras estaciones EcoBici habilitadas para recibir pagos:</p>
                                    <ul class="mb-2">
                                        <li>Estación Central - Puerto Barrios</li>
                                        <li>Terminal de Buses - Zona 1</li>
                                        <li>Parque Central - Zona 2</li>
                                    </ul>
                                    <p class="mb-0"><small>El personal te proporcionará un comprobante que debes ingresar como referencia.</small></p>
                                </div>
                            </div>
                        </div>

                        <!-- Términos y condiciones -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terminos" required>
                                <label class="form-check-label" for="terminos">
                                    Acepto los <a href="#" target="_blank">términos y condiciones</a> de uso de EcoBici y 
                                    la <a href="#" target="_blank">política de privacidad</a>
                                </label>
                            </div>
                        </div>

                        <!-- Resumen final -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Resumen de Compra
                                </h6>
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Membresía:</strong> {{ $membresia->nombre }}<br>
                                        <strong>Duración:</strong> {{ ucfirst($membresia->duracion) }}<br>
                                        <strong>Minutos incluidos:</strong> {{ number_format($membresia->minutos_incluidos) }}
                                    </div>
                                    <div class="col-6 text-end">
                                        <div class="h4 text-primary">
                                            <strong>Total: Q{{ number_format($membresia->precio, 0) }}</strong>
                                        </div>
                                        <small class="text-muted">
                                            @if($membresia->duracion === 'anual')
                                                Equivale a Q{{ number_format($membresia->precio / 12, 0) }} por mes
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('membresias.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver a Membresías
                            </a>
                            <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                                <i class="fas fa-check me-2"></i>
                                Confirmar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .price-display-large {
        font-size: 2.5rem;
        font-weight: bold;
        color: #2563eb;
        text-shadow: 0 2px 4px rgba(37, 99, 235, 0.1);
    }

    .payment-option {
        height: 100%;
    }

    .payment-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        cursor: pointer;
    }

    .payment-card:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
    }

    .form-check-input:checked + .form-check-label .payment-card {
        border-color: #16a34a;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    }

    .payment-info-section {
        display: none;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
    }

    .benefit-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .payment-info-section.active {
        display: block;
        animation: fadeIn 0.3s ease-in;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="metodo_pago"]');
    const paymentInfo = document.getElementById('payment-info');
    const submitBtn = document.getElementById('submit-btn');
    const form = document.getElementById('payment-form');

    // Manejar cambio de método de pago
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            showPaymentInfo(this.value);
        });
    });

    function showPaymentInfo(method) {
        // Ocultar todas las secciones
        document.querySelectorAll('.payment-info-section').forEach(section => {
            section.classList.remove('active');
        });

        // Mostrar la sección correspondiente
        const targetSection = document.getElementById(method + '-info');
        if (targetSection) {
            paymentInfo.style.display = 'block';
            targetSection.classList.add('active');
        }
    }

    // Validación del formulario
    form.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="metodo_pago"]:checked');
        const referencia = document.getElementById('referencia_pago').value.trim();
        const terminos = document.getElementById('terminos').checked;

        if (!selectedMethod) {
            e.preventDefault();
            alert('Por favor selecciona un método de pago');
            return;
        }

        if (!referencia) {
            e.preventDefault();
            alert('Por favor ingresa la referencia de pago');
            return;
        }

        if (!terminos) {
            e.preventDefault();
            alert('Debes aceptar los términos y condiciones');
            return;
        }

        // Mostrar indicador de carga
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
        submitBtn.disabled = true;
    });

    // Preseleccionar método si viene en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const preselectedMethod = urlParams.get('metodo');
    if (preselectedMethod && document.getElementById(preselectedMethod)) {
        document.getElementById(preselectedMethod).checked = true;
        showPaymentInfo(preselectedMethod);
    }
});
</script>
@endsection