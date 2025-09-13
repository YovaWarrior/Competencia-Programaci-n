<?php
// routes/web.php - REEMPLAZAR TODO EL CONTENIDO
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\EstacionController;
use App\Http\Controllers\BicicletaController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MapaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Logout (disponible para autenticados)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Mapa público (sin autenticación)
Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.publico');

// APIs públicas
Route::prefix('api')->group(function () {
    Route::get('/estaciones', [EstacionController::class, 'api'])->name('api.estaciones');
    Route::get('/mapa/estaciones', [MapaController::class, 'estacionesApi'])->name('api.mapa.estaciones');
});

/*
|--------------------------------------------------------------------------
| RUTAS PARA USUARIOS AUTENTICADOS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    
    // Perfil
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // Membresías
    Route::prefix('membresias')->name('membresias.')->group(function () {
        Route::get('/', [MembresiaController::class, 'index'])->name('index');
        Route::get('/{membresia}/pago', [MembresiaController::class, 'pago'])->name('pago');
        Route::post('/{membresia}/pago', [MembresiaController::class, 'procesarPago'])->name('procesar-pago');
        Route::get('/historial', [MembresiaController::class, 'historial'])->name('historial');
    });
    
    // Estaciones
    Route::prefix('estaciones')->name('estaciones.')->group(function () {
        Route::get('/', [EstacionController::class, 'index'])->name('index');
        Route::get('/mapa', [EstacionController::class, 'mapa'])->name('mapa');
        Route::get('/{estacion}', [EstacionController::class, 'show'])->name('show');
    });
    
    // Bicicletas
    Route::prefix('bicicletas')->name('bicicletas.')->group(function () {
        Route::get('/seleccionar', [BicicletaController::class, 'seleccionar'])->name('seleccionar');
        Route::post('/{bicicleta}/usar', [BicicletaController::class, 'usar'])->name('usar');
        Route::get('/usar/{uso}', [BicicletaController::class, 'mostrarUso'])->name('mostrar-uso');
        Route::post('/usar/{uso}/finalizar', [BicicletaController::class, 'finalizarUso'])->name('finalizar-uso');
        Route::get('/historial', [BicicletaController::class, 'historial'])->name('historial');
        Route::get('/{bicicleta}/reportar-dano', [BicicletaController::class, 'reportarDano'])->name('reportar-dano');
        Route::post('/{bicicleta}/reportar-dano', [BicicletaController::class, 'guardarReporteDano'])->name('guardar-reporte-dano');
    });
    
    // Rutas personalizadas
    Route::prefix('rutas')->name('rutas.')->group(function () {
        Route::get('/', [RutaController::class, 'index'])->name('index');
        Route::get('/crear', [RutaController::class, 'crear'])->name('crear');
        Route::post('/', [RutaController::class, 'store'])->name('store');
        Route::get('/{ruta}', [RutaController::class, 'show'])->name('show');
        Route::post('/{ruta}/favorita', [RutaController::class, 'toggleFavorita'])->name('toggle-favorita');
        Route::delete('/{ruta}', [RutaController::class, 'destroy'])->name('destroy');
    });
    
    // Mapa interactivo
    Route::prefix('mapa')->name('mapa.')->group(function () {
        Route::get('/', [MapaController::class, 'index'])->name('index');
        Route::post('/calcular-ruta', [MapaController::class, 'calcularRuta'])->name('calcular-ruta');
    });
    
    // APIs para usuarios autenticados
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/mapa/rutas', [MapaController::class, 'rutasApi'])->name('mapa.rutas');
    });
    
    // Reportes (solo usuarios con membresía activa)
    Route::middleware('membresia.activa')->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/uso', [ReporteController::class, 'uso'])->name('uso');
        Route::get('/ingresos', [ReporteController::class, 'ingresos'])->name('ingresos');
        Route::get('/co2', [ReporteController::class, 'co2'])->name('co2');
        Route::get('/bicicleta/{bicicleta}/historial', [ReporteController::class, 'bicicletasHistorial'])->name('bicicleta-historial');
    });
});

/*
|--------------------------------------------------------------------------
| RUTAS ADMINISTRATIVAS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard administrativo
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gestión de usuarios
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [AdminController::class, 'usuarios'])->name('index');
        Route::get('/{usuario}', [AdminController::class, 'usuarioDetalle'])->name('show');
    });
    
    // Gestión de bicicletas
    Route::prefix('bicicletas')->name('bicicletas.')->group(function () {
        Route::get('/', [AdminController::class, 'bicicletas'])->name('index');
        Route::get('/{bicicleta}', [AdminController::class, 'bicicletaDetalle'])->name('show');
        Route::post('/{bicicleta}/estado', [AdminController::class, 'cambiarEstadoBicicleta'])->name('cambiar-estado');
    });
    
    // Gestión de estaciones
    Route::get('/estaciones', [AdminController::class, 'estaciones'])->name('estaciones.index');
    
    // Gestión de reportes de daños
    Route::prefix('reportes-danos')->name('reportes-danos.')->group(function () {
        Route::get('/', [AdminController::class, 'reportesDanos'])->name('index');
        Route::post('/{reporte}', [AdminController::class, 'actualizarReporteDano'])->name('actualizar');
    });
    
    // Exportar reportes
    Route::get('/exportar/{tipo}', [AdminController::class, 'exportarReporte'])->name('exportar');
    
    // Reportes administrativos (mismo controlador que usuarios pero vista admin)
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/uso', [ReporteController::class, 'uso'])->name('uso');
        Route::get('/ingresos', [ReporteController::class, 'ingresos'])->name('ingresos');
        Route::get('/co2', [ReporteController::class, 'co2'])->name('co2');
    });
});

/*
|--------------------------------------------------------------------------
| RUTAS DE DESARROLLO (solo en modo debug)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    // Ruta para testing rápido
    Route::get('/test', function () {
        return response()->json([
            'usuarios' => \App\Models\User::count(),
            'bicicletas' => \App\Models\Bicicleta::count(),
            'estaciones' => \App\Models\Estacion::count(),
            'membresias' => \App\Models\Membresia::count(),
        ]);
    });
}
