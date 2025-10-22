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
    | Rutas RESTful completas para crear, editar y eliminar pedidos.
    | Usa el PedidoController del frontend Laravel que se comunica con la API Node.js.
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

    // Mostrar detalles
Route::get('/pedidos/{id}/show', [PedidoController::class, 'show'])->name('pedidos.show');

// Generar PDF del pedido
Route::get('/pedidos/{id}/reporte', [PedidoController::class, 'reporte'])->name('pedidos.reporte');


    // 🔹 MÓDULOS RESTANTES
    Route::get('/valoraciones', [ValoracionController::class, 'index'])->name('valoraciones.index');
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/archivos', [ArchivoController::class, 'index'])->name('archivos.index');

    // 🔹 Módulo adicional: Historial de estado de pedidos
    Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');
    Route::get('/historial/{id_pedido}', [HistorialController::class, 'show'])->name('historial.show');

    // Logout
    Route::get('/logout', function () {
        Session::forget(['firebase_user', 'db_user_id', 'db_user_role']);
        return redirect()->route('login');
    })->name('logout');
});
