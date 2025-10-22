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
    | CRUD completo de pedidos, conectado a la API Node.js.
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

    // Mostrar detalles de pedido
    Route::get('/pedidos/{id}/show', [PedidoController::class, 'show'])->name('pedidos.show');

    // Generar PDF del pedido
    Route::get('/pedidos/{id}/reporte', [PedidoController::class, 'reporte'])->name('pedidos.reporte');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: CLIENTES
    |--------------------------------------------------------------------------
    | CRUD completo de clientes, conectado a la API Node.js.
    | Mantiene la misma estructura visual y lógica que el módulo de pedidos.
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

    // Mostrar detalles de cliente
    Route::get('/clientes/{id}/show', [ClienteController::class, 'show'])->name('clientes.show');

    // Generar PDF del cliente
    Route::get('/clientes/{id}/reporte', [ClienteController::class, 'reporte'])->name('clientes.reporte');

    /*
    |--------------------------------------------------------------------------
    | MÓDULOS RESTANTES
    |--------------------------------------------------------------------------
    */
    Route::get('/valoraciones', [ValoracionController::class, 'index'])->name('valoraciones.index');
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/archivos', [ArchivoController::class, 'index'])->name('archivos.index');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: HISTORIAL DE ESTADO DE PEDIDOS
    |--------------------------------------------------------------------------
    */
    Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');
    Route::get('/historial/{id_pedido}', [HistorialController::class, 'show'])->name('historial.show');

    // Logout
    Route::get('/logout', function () {
        Session::forget(['firebase_user', 'db_user_id', 'db_user_role']);
        return redirect()->route('login');
    })->name('logout');
});
