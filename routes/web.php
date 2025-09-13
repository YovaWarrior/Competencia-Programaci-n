<?php

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
    Route::get('/estadisticas-generales', [ReporteController::class, 'estadisticasGenerales'])->name('api.estadisticas');
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
        Route::get('/comparar', [MembresiaController::class, 'compararPlanes'])->name('comparar');
        Route::post('/calcular-ahorro', [MembresiaController::class, 'calcularAhorro'])->name('calcular-ahorro');
        Route::delete('/cambio-programado/{userMembresia}', [MembresiaController::class, 'cancelarCambioProgramado'])->name('cancelar-cambio');
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
        Route::post('/{ruta}/usar', [RutaController::class, 'usarRuta'])->name('usar');
    });
    
    // Mapa interactivo
    Route::prefix('mapa')->name('mapa.')->group(function () {
        Route::get('/', [MapaController::class, 'index'])->name('index');
        Route::post('/calcular-ruta', [MapaController::class, 'calcularRuta'])->name('calcular-ruta');
    });
    
    // APIs para usuarios autenticados
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/mapa/rutas', [MapaController::class, 'rutasApi'])->name('mapa.rutas');
        Route::get('/usuario/estadisticas', [DashboardController::class, 'estadisticasUsuario'])->name('usuario.estadisticas');
    });
    
    // Reportes (solo usuarios con membresía activa)
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/co2', [ReporteController::class, 'co2'])->name('co2');
        Route::get('/uso', [ReporteController::class, 'uso'])->name('uso');
        Route::get('/ingresos', [ReporteController::class, 'ingresos'])->name('ingresos');
        Route::get('/bicicleta/{bicicleta}/historial', [ReporteController::class, 'bicicletaHistorial'])->name('bicicleta-historial');
        Route::get('/exportar/{tipo}', [ReporteController::class, 'exportar'])->name('exportar');
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
    Route::get('/estadisticas', [AdminController::class, 'estadisticas'])->name('estadisticas');
    
    // Gestión de usuarios
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [AdminController::class, 'usuarios'])->name('index');
        Route::get('/{usuario}', [AdminController::class, 'usuarioDetalle'])->name('show');
        Route::post('/{usuario}/suspender', [AdminController::class, 'suspenderUsuario'])->name('suspender');
        Route::post('/{usuario}/activar', [AdminController::class, 'activarUsuario'])->name('activar');
        Route::get('/{usuario}/historial', [AdminController::class, 'historialUsuario'])->name('historial');
    });
    
    // Gestión de bicicletas
    Route::prefix('bicicletas')->name('bicicletas.')->group(function () {
        Route::get('/', [AdminController::class, 'bicicletas'])->name('index');
        Route::get('/crear', [AdminController::class, 'crearBicicleta'])->name('crear');
        Route::post('/', [AdminController::class, 'guardarBicicleta'])->name('store');
        Route::get('/{bicicleta}', [AdminController::class, 'bicicletaDetalle'])->name('show');
        Route::get('/{bicicleta}/editar', [AdminController::class, 'editarBicicleta'])->name('edit');
        Route::put('/{bicicleta}', [AdminController::class, 'actualizarBicicleta'])->name('update');
        Route::post('/{bicicleta}/estado', [AdminController::class, 'cambiarEstadoBicicleta'])->name('cambiar-estado');
        Route::delete('/{bicicleta}', [AdminController::class, 'eliminarBicicleta'])->name('destroy');
    });
    
    // Gestión de estaciones
    Route::prefix('estaciones')->name('estaciones.')->group(function () {
        Route::get('/', [AdminController::class, 'estaciones'])->name('index');
        Route::get('/crear', [AdminController::class, 'crearEstacion'])->name('crear');
        Route::post('/', [AdminController::class, 'guardarEstacion'])->name('store');
        Route::get('/{estacion}', [AdminController::class, 'estacionDetalle'])->name('show');
        Route::get('/{estacion}/editar', [AdminController::class, 'editarEstacion'])->name('edit');
        Route::put('/{estacion}', [AdminController::class, 'actualizarEstacion'])->name('update');
        Route::post('/{estacion}/estado', [AdminController::class, 'cambiarEstadoEstacion'])->name('cambiar-estado');
    });
    
    // Gestión de membresías
    Route::prefix('membresias')->name('membresias.')->group(function () {
        Route::get('/', [AdminController::class, 'membresias'])->name('index');
        Route::get('/crear', [AdminController::class, 'crearMembresia'])->name('crear');
        Route::post('/', [AdminController::class, 'guardarMembresia'])->name('store');
        Route::get('/{membresia}/editar', [AdminController::class, 'editarMembresia'])->name('edit');
        Route::put('/{membresia}', [AdminController::class, 'actualizarMembresia'])->name('update');
        Route::post('/{membresia}/activar', [AdminController::class, 'activarMembresia'])->name('activar');
        Route::post('/{membresia}/desactivar', [AdminController::class, 'desactivarMembresia'])->name('desactivar');
    });
    
    // Gestión de reportes de daños
    Route::prefix('reportes-danos')->name('reportes-danos.')->group(function () {
        Route::get('/', [AdminController::class, 'reportesDanos'])->name('index');
        Route::get('/{reporte}', [AdminController::class, 'reporteDanoDetalle'])->name('show');
        Route::post('/{reporte}/actualizar', [AdminController::class, 'actualizarReporteDano'])->name('actualizar');
        Route::post('/{reporte}/resolver', [AdminController::class, 'resolverReporteDano'])->name('resolver');
    });
    
    // Reportes administrativos
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/uso', [ReporteController::class, 'uso'])->name('uso');
        Route::get('/ingresos', [ReporteController::class, 'ingresos'])->name('ingresos');
        Route::get('/co2', [ReporteController::class, 'co2'])->name('co2');
        Route::get('/usuarios-activos', [AdminController::class, 'reporteUsuariosActivos'])->name('usuarios-activos');
        Route::get('/bicicletas-populares', [AdminController::class, 'reporteBicicletasPopulares'])->name('bicicletas-populares');
        Route::get('/exportar/{tipo}', [ReporteController::class, 'exportar'])->name('exportar');
    });
    
    // Configuración del sistema
    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        Route::get('/', [AdminController::class, 'configuracion'])->name('index');
        Route::post('/actualizar', [AdminController::class, 'actualizarConfiguracion'])->name('actualizar');
        Route::post('/backup', [AdminController::class, 'crearBackup'])->name('backup');
        Route::get('/logs', [AdminController::class, 'verLogs'])->name('logs');
    });
    
    // APIs administrativas
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard-stats', [AdminController::class, 'estadisticasDashboard'])->name('dashboard-stats');
        Route::get('/usuarios/buscar', [AdminController::class, 'buscarUsuarios'])->name('usuarios.buscar');
        Route::get('/bicicletas/disponibles', [AdminController::class, 'bicicletasDisponibles'])->name('bicicletas.disponibles');
    });
});

/*
|--------------------------------------------------------------------------
| RUTAS DE DESARROLLO Y TESTING
|--------------------------------------------------------------------------
*/

if (app()->environment(['local', 'testing'])) {
    // Ruta para testing rápido
    Route::get('/test', function () {
        return response()->json([
            'status' => 'OK',
            'environment' => app()->environment(),
            'usuarios' => \App\Models\User::count(),
            'bicicletas' => \App\Models\Bicicleta::count(),
            'estaciones' => \App\Models\Estacion::count(),
            'membresias' => \App\Models\Membresia::count(),
            'rutas' => \App\Models\Ruta::count(),
        ]);
    })->name('test');
    
    // Ruta para seeders rápidos
    Route::get('/seed-quick', function () {
        Artisan::call('db:seed', ['--class' => 'QuickTestSeeder']);
        return response()->json(['message' => 'Seeders ejecutados correctamente']);
    })->name('seed-quick');
    
    // Ruta para limpiar cache
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return response()->json(['message' => 'Cache limpiado correctamente']);
    })->name('clear-cache');
}

/*
|--------------------------------------------------------------------------
| RUTAS DE WEBHOOKS Y APIS EXTERNAS
|--------------------------------------------------------------------------
*/

Route::prefix('webhooks')->group(function () {
    // Webhook para pagos (si se implementa pasarela de pago)
    Route::post('/pagos/confirmacion', function (Request $request) {
        // Lógica para procesar confirmaciones de pago
        return response()->json(['status' => 'received']);
    })->name('webhooks.pagos');
    
    // Webhook para notificaciones push
    Route::post('/notificaciones', function (Request $request) {
        // Lógica para manejar notificaciones push
        return response()->json(['status' => 'processed']);
    })->name('webhooks.notificaciones');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE MANTENIMIENTO Y SISTEMA
|--------------------------------------------------------------------------
*/

// Página de mantenimiento personalizada
Route::get('/mantenimiento', function () {
    return view('mantenimiento');
})->name('mantenimiento');

// Health check para monitoreo
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
        'services' => [
            'database' => DB::connection()->getPdo() ? 'OK' : 'ERROR',
            'cache' => Cache::store()->getStore() ? 'OK' : 'ERROR',
        ]
    ]);
})->name('health');

/*
|--------------------------------------------------------------------------
| FALLBACK ROUTES
|--------------------------------------------------------------------------
*/

// Ruta 404 personalizada
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});