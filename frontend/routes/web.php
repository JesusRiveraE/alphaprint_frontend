<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use App\Http\Controllers\{
    DashboardController,
    PedidoController,
    ValoracionController,
    UsuarioController,
    EmpleadoController,
    ClienteController,
    BitacoraController,
    NotificacionController,
    ArchivoController,
    HistorialController
};

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
|
| Si ya existe sesión de Firebase, redirige automáticamente al dashboard.
| Si no, muestra la pantalla de login.
|
*/

Route::get('/', function () {
    if (Session::has('firebase_user')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', function () {
    // Si el usuario ya está autenticado, no mostrar login
    if (Session::has('firebase_user')) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

// Endpoint que recibe los datos del usuario autenticado en Firebase
Route::post('/firebase/login', function (Request $request) {
    $payload = $request->input('user');

    if (!$payload || !isset($payload['email'])) {
        return response()->json(['ok' => false, 'error' => 'Payload inválido'], 422);
    }

    // Guardar el usuario Firebase en la sesión
    Session::put('firebase_user', $payload);

    return response()->json(['ok' => true]);
})->name('firebase.login');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Firebase Middleware)
|--------------------------------------------------------------------------
|
| Todas estas rutas requieren sesión activa de Firebase.
|
*/

Route::middleware(['auth.firebase'])->group(function () {

    // Dashboard principal
    Route::get('/home', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: PEDIDOS
    |--------------------------------------------------------------------------
    */
    Route::resource('pedidos', PedidoController::class)->names([
        'index'   => 'pedidos.index',
        'create'  => 'pedidos.create',
        'store'   => 'pedidos.store',
        'edit'    => 'pedidos.edit',
        'update'  => 'pedidos.update',
        'destroy' => 'pedidos.destroy',
    ])->parameters([
        'pedidos' => 'id'
    ]);

    Route::get('/pedidos/{id}/show', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('/pedidos/{id}/reporte', [PedidoController::class, 'reporte'])->name('pedidos.reporte');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: CLIENTES
    |--------------------------------------------------------------------------
    */
    Route::resource('clientes', ClienteController::class)->names([
        'index'   => 'clientes.index',
        'create'  => 'clientes.create',
        'store'   => 'clientes.store',
        'edit'    => 'clientes.edit',
        'update'  => 'clientes.update',
        'destroy' => 'clientes.destroy',
    ])->parameters([
        'clientes' => 'id'
    ]);

    Route::get('/clientes/{id}/show', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{id}/reporte', [ClienteController::class, 'reporte'])->name('clientes.reporte');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: VALORACIONES
    |--------------------------------------------------------------------------
    */
    Route::get('/valoraciones', [ValoracionController::class, 'index'])->name('valoraciones.index');
    Route::get('/valoraciones/create', [ValoracionController::class, 'create'])->name('valoraciones.create');
    Route::post('/valoraciones/store', [ValoracionController::class, 'store'])->name('valoraciones.store');
    Route::get('/valoraciones/reporte', [ValoracionController::class, 'reporte'])->name('valoraciones.reporte');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: BITÁCORA
    |--------------------------------------------------------------------------
    */
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    Route::get('/bitacora/reporte', [BitacoraController::class, 'reporte'])->name('bitacora.reporte');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: ARCHIVOS
    |--------------------------------------------------------------------------
    | Permite listar, crear y registrar archivos vinculados a pedidos.
    */
    Route::get('/archivos', [ArchivoController::class, 'index'])->name('archivos.index');
    Route::get('/archivos/crear', [ArchivoController::class, 'create'])->name('archivos.create');
    Route::post('/archivos', [ArchivoController::class, 'store'])->name('archivos.store');

    /*
    |--------------------------------------------------------------------------
    | MÓDULOS RESTANTES
    |--------------------------------------------------------------------------
    */
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: HISTORIAL DE ESTADO DE PEDIDOS
    |--------------------------------------------------------------------------
    */
    Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');
    Route::get('/historial/{id_pedido}', [HistorialController::class, 'show'])->name('historial.show');

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::get('/logout', function () {
        Session::forget(['firebase_user', 'db_user_id', 'db_user_role']);
        return redirect()->route('login');
    })->name('logout');
});
