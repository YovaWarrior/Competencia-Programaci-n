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
                    <form method="POST" action="{{ route('membresias.procesar-pago', $membresia->id) }}" id="payment-form" enctype="multipart/form-data">
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
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="deposito" value="deposito" required>
                                        <label class="form-check-label" for="deposito">
                                            <div class="payment-card">
                                                <i class="fas fa-money-bill fa-2x text-warning mb-2"></i>
                                                <div>Depósito Bancario</div>
                                                <small class="text-muted">En ventanilla/ATM</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('metodo_pago')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Formularios específicos por método de pago -->
                        <div id="payment-forms">
                            <!-- Formulario para Tarjeta -->
                            <div id="tarjeta-form" class="payment-form-section">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Datos de la Tarjeta</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <label for="numero_tarjeta" class="form-label">Número de Tarjeta *</label>
                                                <input type="text" class="form-control" id="numero_tarjeta" name="numero_tarjeta" 
                                                       placeholder="1234 5678 9012 3456" maxlength="19">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="cvv" class="form-label">CVV *</label>
                                                <input type="text" class="form-control" id="cvv" name="cvv" 
                                                       placeholder="123" maxlength="4">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-8">
                                                <label for="nombre_titular" class="form-label">Nombre del Titular *</label>
                                                <input type="text" class="form-control" id="nombre_titular" name="nombre_titular" 
                                                       placeholder="Como aparece en la tarjeta">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="fecha_expiracion" class="form-label">Fecha de Expiración *</label>
                                                <input type="text" class="form-control" id="fecha_expiracion" name="fecha_expiracion" 
                                                       placeholder="MM/AA" maxlength="5">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="alert alert-info">
                                                <i class="fas fa-shield-alt me-2"></i>
                                                <small>Tu información está protegida con encriptación SSL de 256 bits. Total: <strong>Q{{ number_format($membresia->precio, 0) }}</strong></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulario para Transferencia -->
                            <div id="transferencia-form" class="payment-form-section">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-university me-2"></i>Datos para Transferencia</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="alert alert-success">
                                                    <strong>Cuenta Destino:</strong><br>
                                                    <strong>Banco:</strong> Banco Industrial<br>
                                                    <strong>Cuenta:</strong> 123-456789-0<br>
                                                    <strong>Tipo:</strong> Monetaria<br>
                                                    <strong>A nombre de:</strong> Municipalidad de Puerto Barrios<br>
                                                    <strong>Monto:</strong> Q{{ number_format($membresia->precio, 0) }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="comprobante_transferencia" class="form-label">Comprobante de Transferencia *</label>
                                                <input type="file" class="form-control" id="comprobante_transferencia" 
                                                       name="comprobante_transferencia" accept=".jpg,.jpeg,.png,.pdf">
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Sube una foto o PDF del comprobante de transferencia
                                                </div>
                                                <div class="invalid-feedback"></div>
                                                
                                                <!-- Preview del archivo -->
                                                <div id="preview-transferencia" class="mt-2" style="display: none;">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-image text-success me-2"></i>
                                                        <span id="filename-transferencia"></span>
                                                        <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="removeFile('transferencia')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="numero_referencia_transferencia" class="form-label">Número de Referencia *</label>
                                                <input type="text" class="form-control" id="numero_referencia_transferencia" 
                                                       name="numero_referencia_transferencia" placeholder="Ej: TRF123456789">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="banco_origen" class="form-label">Banco de Origen *</label>
                                                <select class="form-select" id="banco_origen" name="banco_origen">
                                                    <option value="">Selecciona tu banco</option>
                                                    <option value="banco_industrial">Banco Industrial</option>
                                                    <option value="banrural">Banrural</option>
                                                    <option value="bac">BAC Credomatic</option>
                                                    <option value="bantrab">Bantrab</option>
                                                    <option value="banco_agromercantil">Banco Agromercantil</option>
                                                    <option value="otro">Otro</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulario para Depósito -->
                            <div id="deposito-form" class="payment-form-section">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0"><i class="fas fa-money-bill me-2"></i>Comprobante de Depósito</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="alert alert-warning">
                                                    <strong>Datos para Depósito:</strong><br>
                                                    <strong>Banco:</strong> Banco Industrial<br>
                                                    <strong>Cuenta:</strong> 123-456789-0<br>
                                                    <strong>Tipo:</strong> Monetaria<br>
                                                    <strong>A nombre de:</strong> Municipalidad de Puerto Barrios<br>
                                                    <strong>Monto exacto:</strong> Q{{ number_format($membresia->precio, 0) }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="comprobante_deposito" class="form-label">Comprobante de Depósito *</label>
                                                <input type="file" class="form-control" id="comprobante_deposito" 
                                                       name="comprobante_deposito" accept=".jpg,.jpeg,.png,.pdf">
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Sube una foto clara del comprobante de depósito
                                                </div>
                                                <div class="invalid-feedback"></div>
                                                
                                                <!-- Preview del archivo -->
                                                <div id="preview-deposito" class="mt-2" style="display: none;">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-image text-success me-2"></i>
                                                        <span id="filename-deposito"></span>
                                                        <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="removeFile('deposito')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="numero_boleta" class="form-label">Número de Boleta *</label>
                                                <input type="text" class="form-control" id="numero_boleta" 
                                                       name="numero_boleta" placeholder="Ej: 001234567">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="fecha_deposito" class="form-label">Fecha de Depósito *</label>
                                                <input type="date" class="form-control" id="fecha_deposito" 
                                                       name="fecha_deposito" max="{{ date('Y-m-d') }}">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="sucursal" class="form-label">Sucursal/ATM *</label>
                                                <input type="text" class="form-control" id="sucursal" 
                                                       name="sucursal" placeholder="Ej: Agencia Central">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
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
                                Procesar Pago
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

    .payment-form-section {
        display: none;
        margin-bottom: 20px;
    }

    .payment-form-section.active {
        display: block;
        animation: fadeIn 0.3s ease-in;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .file-preview {
        max-width: 200px;
        max-height: 150px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="metodo_pago"]');
    const submitBtn = document.getElementById('submit-btn');
    const form = document.getElementById('payment-form');

    // Manejar cambio de método de pago
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            showPaymentForm(this.value);
            clearValidationErrors();
        });
    });

    function showPaymentForm(method) {
        // Ocultar todos los formularios
        document.querySelectorAll('.payment-form-section').forEach(section => {
            section.classList.remove('active');
        });

        // Mostrar el formulario correspondiente
        const targetForm = document.getElementById(method + '-form');
        if (targetForm) {
            targetForm.classList.add('active');
        }
    }

    // Validaciones específicas para tarjeta
    function setupCardValidation() {
        const numeroTarjeta = document.getElementById('numero_tarjeta');
        const cvv = document.getElementById('cvv');
        const fechaExpiracion = document.getElementById('fecha_expiracion');

        // Formatear número de tarjeta
        numeroTarjeta?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
            e.target.value = formattedValue;
        });

        // Solo números en CVV
        cvv?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        // Formatear fecha MM/AA
        fechaExpiracion?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0,2) + '/' + value.substring(2,4);
            }
            e.target.value = value;
        });
    }

    // Manejar archivos
    function setupFileHandlers() {
        const fileInputs = ['comprobante_transferencia', 'comprobante_deposito'];
        
        fileInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                input.addEventListener('change', function(e) {
                    handleFileUpload(e, inputId);
                });
            }
        });
    }

    function handleFileUpload(e, inputId) {
        const file = e.target.files[0];
        const previewId = 'preview-' + inputId.split('_')[1];
        const filenameId = 'filename-' + inputId.split('_')[1];
        
        if (file) {
            // Validar tamaño (5MB máximo)
            if (file.size > 5 * 1024 * 1024) {
                showError(e.target, 'El archivo no debe superar los 5MB');
                e.target.value = '';
                return;
            }

            // Validar tipo
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!validTypes.includes(file.type)) {
                showError(e.target, 'Solo se permiten archivos JPG, PNG o PDF');
                e.target.value = '';
                return;
            }

            // Mostrar preview
            document.getElementById(previewId).style.display = 'block';
            document.getElementById(filenameId).textContent = file.name;
            clearError(e.target);
        }
    }

    function removeFile(type) {
        const input = document.getElementById('comprobante_' + type);
        const preview = document.getElementById('preview-' + type);
        
        input.value = '';
        preview.style.display = 'none';
    }

    // Validación del formulario
    function validateForm() {
        const selectedMethod = document.querySelector('input[name="metodo_pago"]:checked');
        
        if (!selectedMethod) {
            alert('Por favor selecciona un método de pago');
            return false;
        }

        if (selectedMethod.value === 'tarjeta') {
            return validateCardForm();
        } else if (selectedMethod.value === 'transferencia') {
            return validateTransferForm();
        } else if (selectedMethod.value === 'deposito') {
            return validateDepositForm();
        }

        return true;
    }

    function validateCardForm() {
        const fields = [
            { id: 'numero_tarjeta', name: 'Número de tarjeta', pattern: /^\d{4}\s\d{4}\s\d{4}\s\d{4}$/ },
            { id: 'cvv', name: 'CVV', pattern: /^\d{3,4}$/ },
            { id: 'nombre_titular', name: 'Nombre del titular', required: true },
            { id: 'fecha_expiracion', name: 'Fecha de expiración', pattern: /^\d{2}\/\d{2}$/ }
        ];

        let isValid = true;

        fields.forEach(field => {
            const input = document.getElementById(field.id);
            const value = input.value.trim();

            if (!value) {
                showError(input, `El campo ${field.name} es requerido`);
                isValid = false;
            } else if (field.pattern && !field.pattern.test(value)) {
                showError(input, `El formato del ${field.name} no es válido`);
                isValid = false;
            } else {
                clearError(input);
            }
        });

        // Validar fecha no expirada
        const fechaExp = document.getElementById('fecha_expiracion').value;
        if (fechaExp.length === 5) {
            const [mes, año] = fechaExp.split('/');
            const fechaExpiracion = new Date(2000 + parseInt(año), parseInt(mes) - 1);
            const fechaActual = new Date();
            
            if (fechaExpiracion < fechaActual) {
                showError(document.getElementById('fecha_expiracion'), 'La tarjeta está expirada');
                isValid = false;
            }
        }

        return isValid;
    }

    function validateTransferForm() {
        const fields = [
            { id: 'comprobante_transferencia', name: 'Comprobante', file: true },
            { id: 'numero_referencia_transferencia', name: 'Número de referencia', required: true },
            { id: 'banco_origen', name: 'Banco de origen', required: true }
        ];

        let isValid = true;

        fields.forEach(field => {
            const input = document.getElementById(field.id);
            const value = field.file ? input.files[0] : input.value.trim();

            if (!value) {
                showError(input, `El campo ${field.name} es requerido`);
                isValid = false;
            } else {
                clearError(input);
            }
        });

        return isValid;
    }

    function validateDepositForm() {
        const fields = [
            { id: 'comprobante_deposito', name: 'Comprobante', file: true },
            { id: 'numero_boleta', name: 'Número de boleta', required: true },
            { id: 'fecha_deposito', name: 'Fecha de depósito', required: true },
            { id: 'sucursal', name: 'Sucursal/ATM', required: true }
        ];

        let isValid = true;

        fields.forEach(field => {
            const input = document.getElementById(field.id);
            const value = field.file ? input.files[0] : input.value.trim();

            if (!value) {
                showError(input, `El campo ${field.name} es requerido`);
                isValid = false;
            } else {
                clearError(input);
            }
        });

        // Validar que la fecha no sea futura
        const fechaDeposito = document.getElementById('fecha_deposito').value;
        if (fechaDeposito) {
            const fecha = new Date(fechaDeposito);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            
            if (fecha > hoy) {
                showError(document.getElementById('fecha_deposito'), 'La fecha no puede ser futura');
                isValid = false;
            }
        }

        return isValid;
    }

    function showError(input, message) {
        input.classList.add('is-invalid');
        const feedback = input.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }

    function clearError(input) {
        input.classList.remove('is-invalid');
        const feedback = input.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = '';
        }
    }

    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(feedback => {
            feedback.textContent = '';
        });
    }

    // Validación en tiempo real
    function setupRealTimeValidation() {
        // Validación para campos de tarjeta
        const cardFields = ['numero_tarjeta', 'cvv', 'nombre_titular', 'fecha_expiracion'];
        cardFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('blur', function() {
                    if (document.querySelector('input[name="metodo_pago"]:checked')?.value === 'tarjeta') {
                        validateCardField(this);
                    }
                });
            }
        });

        // Validación para otros campos requeridos
        const requiredFields = ['numero_referencia_transferencia', 'banco_origen', 'numero_boleta', 'fecha_deposito', 'sucursal'];
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        showError(this, 'Este campo es requerido');
                    } else {
                        clearError(this);
                    }
                });
            }
        });
    }

    function validateCardField(field) {
        const value = field.value.trim();
        
        switch(field.id) {
            case 'numero_tarjeta':
                if (!value) {
                    showError(field, 'El número de tarjeta es requerido');
                } else if (!/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/.test(value)) {
                    showError(field, 'Formato de tarjeta inválido');
                } else {
                    clearError(field);
                }
                break;
                
            case 'cvv':
                if (!value) {
                    showError(field, 'El CVV es requerido');
                } else if (!/^\d{3,4}$/.test(value)) {
                    showError(field, 'CVV debe tener 3 o 4 dígitos');
                } else {
                    clearError(field);
                }
                break;
                
            case 'nombre_titular':
                if (!value) {
                    showError(field, 'El nombre del titular es requerido');
                } else if (value.length < 3) {
                    showError(field, 'Nombre demasiado corto');
                } else {
                    clearError(field);
                }
                break;
                
            case 'fecha_expiracion':
                if (!value) {
                    showError(field, 'La fecha de expiración es requerida');
                } else if (!/^\d{2}\/\d{2}$/.test(value)) {
                    showError(field, 'Formato debe ser MM/AA');
                } else {
                    // Validar que no esté expirada
                    const [mes, año] = value.split('/');
                    const fechaExp = new Date(2000 + parseInt(año), parseInt(mes) - 1);
                    const fechaActual = new Date();
                    
                    if (fechaExp < fechaActual) {
                        showError(field, 'La tarjeta está expirada');
                    } else {
                        clearError(field);
                    }
                }
                break;
        }
    }

    // Event listeners del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const terminos = document.getElementById('terminos').checked;
        if (!terminos) {
            alert('Debes aceptar los términos y condiciones');
            return;
        }

        if (validateForm()) {
            // Mostrar indicador de carga
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando Pago...';
            submitBtn.disabled = true;
            
            // Enviar formulario
            this.submit();
        }
    });

    // Inicializar validaciones
    setupCardValidation();
    setupFileHandlers();
    setupRealTimeValidation();

    // Hacer la función removeFile global
    window.removeFile = removeFile;

    // Preseleccionar método si viene en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const preselectedMethod = urlParams.get('metodo');
    if (preselectedMethod && document.getElementById(preselectedMethod)) {
        document.getElementById(preselectedMethod).checked = true;
        showPaymentForm(preselectedMethod);
    }

    // Configurar fecha máxima para depósito (hoy)
    const fechaDeposito = document.getElementById('fecha_deposito');
    if (fechaDeposito) {
        fechaDeposito.max = new Date().toISOString().split('T')[0];
    }

    // Mejorar experiencia de usuario
    document.querySelectorAll('input[type="text"], input[type="date"], select').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.2s ease';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Animación suave para cambio de métodos de pago
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            const selectedCard = this.closest('.payment-option').querySelector('.payment-card');
            
            // Remover animación de otras tarjetas
            document.querySelectorAll('.payment-card').forEach(card => {
                card.style.transform = 'scale(1)';
                card.style.boxShadow = '';
            });
            
            // Animar tarjeta seleccionada
            selectedCard.style.transform = 'scale(1.05)';
            selectedCard.style.boxShadow = '0 10px 30px rgba(34, 197, 94, 0.3)';
            
            setTimeout(() => {
                selectedCard.style.transform = 'scale(1)';
            }, 200);
        });
    });

    // Contador de caracteres para campos de texto
    const textInputs = document.querySelectorAll('input[type="text"]');
    textInputs.forEach(input => {
        if (input.maxLength > 0) {
            const counter = document.createElement('small');
            counter.className = 'text-muted float-end';
            counter.textContent = `0/${input.maxLength}`;
            input.parentElement.appendChild(counter);
            
            input.addEventListener('input', function() {
                counter.textContent = `${this.value.length}/${this.maxLength}`;
                
                if (this.value.length > this.maxLength * 0.9) {
                    counter.className = 'text-warning float-end';
                } else {
                    counter.className = 'text-muted float-end';
                }
            });
        }
    });

    // Tooltip para información adicional
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (typeof bootstrap !== 'undefined') {
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });
    }
});
</script>

@endsection