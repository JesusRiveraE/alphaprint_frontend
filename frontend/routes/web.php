<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\EnsureUserIsAdmin;

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
    HistorialController,
    CalendarioController,
    HomeController, // 👈 NUEVO: controlador para /home
};

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
|
| Si ya existe sesión de Firebase, redirige automáticamente al HOME (/home).
| Si no, muestra la pantalla de login.
|
*/

Route::get('/calendario', [CalendarioController::class, 'index'])
    ->name('calendario.index')
    ->middleware('auth.firebase');

Route::get('/', function () {
    if (Session::has('firebase_user')) {
        // Antes: route('dashboard')
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

Route::get('/login', function () {
    // Si el usuario ya está autenticado, no mostrar login
    if (Session::has('firebase_user')) {
        // Antes: route('dashboard')
        return redirect()->route('home');
    }
    return view('auth.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| Endpoint que recibe los datos del usuario autenticado en Firebase
|--------------------------------------------------------------------------
| - Valida el payload
| - Consulta el rol en la tabla USUARIOS
| - Solo crea sesión si el usuario existe y está activo
*/
Route::post('/firebase/login', function (Request $request) {
    $payload = $request->input('user');

    if (
        !$payload ||
        empty($payload['email']) ||
        empty($payload['uid'])
    ) {
        Log::warning('Login Firebase: payload inválido', ['payload' => $payload]);
        return response()->json([
            'ok'    => false,
            'error' => 'Datos de autenticación incompletos.'
        ], 422);
    }

    $uid   = $payload['uid'];
    $email = $payload['email'];

    try {
        // 1) Buscar por UID
        $usuario = DB::table('USUARIOS')
            ->select('id_usuario', 'rol', 'activo', 'uid_firebase')
            ->where('uid_firebase', $uid)
            ->first();

        // 2) Si no existe, buscar por correo (usuarios viejos)
        if (!$usuario) {
            $usuario = DB::table('USUARIOS')
                ->select('id_usuario', 'rol', 'activo', 'uid_firebase')
                ->where('correo', $email)
                ->first();

            // Vincular UID si estaba vacío
            if ($usuario && empty($usuario->uid_firebase)) {
                DB::table('USUARIOS')
                    ->where('id_usuario', $usuario->id_usuario)
                    ->update(['uid_firebase' => $uid]);

                Log::info('Login Firebase: se vinculó UID a usuario existente', [
                    'id_usuario' => $usuario->id_usuario,
                    'correo'     => $email,
                    'nuevo_uid'  => $uid,
                ]);

                $usuario->uid_firebase = $uid;
            }
        }

    } catch (\Throwable $e) {
        Log::error('Error al consultar USUARIOS en /firebase/login', [
            'uid_firebase' => $uid,
            'email'        => $email,
            'message'      => $e->getMessage(),
        ]);

        return response()->json([
            'ok'    => false,
            'error' => 'Error interno al validar usuario: ' . $e->getMessage(),
        ], 500);
    }

    if (!$usuario) {
        Log::warning('Login Firebase: UID/correo no registrados en USUARIOS', [
            'uid_firebase' => $uid,
            'email'        => $email,
        ]);

        Session::forget(['firebase_user', 'db_user_id', 'db_user_role', 'userRole', 'user_role']);

        return response()->json([
            'ok'    => false,
            'error' => 'Usuario no autorizado en el sistema local'
        ], 403);
    }

    if (!$usuario->activo) {
        Log::warning('Login Firebase: usuario inactivo', [
            'uid_firebase' => $uid,
            'email'        => $email,
            'id_usuario'   => $usuario->id_usuario,
        ]);

        Session::forget(['firebase_user', 'db_user_id', 'db_user_role', 'userRole', 'user_role']);

        return response()->json([
            'ok'    => false,
            'error' => 'Cuenta inactiva. Contacta al administrador.'
        ], 403);
    }

    Session::put('firebase_user', $payload);
    Session::put('db_user_id', $usuario->id_usuario);
    Session::put('db_user_role', $usuario->rol);
    Session::put('userRole', $usuario->rol);
    Session::put('user_role', $usuario->rol);

    Log::info('Login Firebase exitoso', [
        'id_usuario' => $usuario->id_usuario,
        'correo'     => $email,
        'rol'        => $usuario->rol,
    ]);

    return response()->json([
        'ok'  => true,
        'rol' => $usuario->rol,
    ]);
})->name('firebase.login');


/*
|--------------------------------------------------------------------------
| RUTA DE CIERRE DE SESIÓN FORZADO (para cuando Firebase cierra sesión)
|--------------------------------------------------------------------------
*/
Route::get('/force-logout', function () {
    Session::forget(['firebase_user', 'db_user_id', 'db_user_role', 'userRole', 'user_role']);
    Session::invalidate();
    Session::regenerateToken();

    return redirect('/login?reason=forced-logout');
})->name('force.logout');


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::get('/logout', function () {
    // Limpia datos asociados a la sesión
    Session::forget(['firebase_user', 'db_user_id', 'db_user_role', 'userRole', 'user_role']);
    Session::invalidate();
    Session::regenerateToken();

    return redirect()->route('login')->with('status', 'Sesión cerrada correctamente.');
})->name('logout')->middleware('web');


/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Firebase Middleware)
|--------------------------------------------------------------------------
|
| Todas estas rutas requieren sesión activa de Firebase.
|
*/
Route::middleware(['auth.firebase'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | RUTAS ACCESIBLES PARA ADMIN Y EMPLEADO
    |--------------------------------------------------------------------------
    */

    // 🏠 NUEVO HOME: módulo con 5 botones
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // 📊 DASHBOARD: métricas y gráficos
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    // Cambiar estado (AJAX)
    Route::put('/pedidos/{id}/estado',  [PedidoController::class, 'updateEstado'])->name('pedidos.estado');
    Route::get('/pedidos/{id}/show',    [PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('/pedidos/{id}/reporte', [PedidoController::class, 'reporte'])->name('pedidos.reporte');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: ARCHIVOS
    |--------------------------------------------------------------------------
    */
    Route::get('/archivos',       [ArchivoController::class, 'index'])->name('archivos.index');
    Route::get('/archivos/crear', [ArchivoController::class, 'create'])->name('archivos.create');
    Route::post('/archivos',      [ArchivoController::class, 'store'])->name('archivos.store');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: NOTIFICACIONES
    |--------------------------------------------------------------------------
    */
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');

    Route::put('/notificaciones/{id}/leido', [NotificacionController::class, 'markAsRead'])
        ->name('notificaciones.leer');

    Route::post('/notificaciones/marcar-todas', [NotificacionController::class, 'markAllAsRead'])
        ->name('notificaciones.marcarTodas');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: HISTORIAL DE ESTADO DE PEDIDOS
    |--------------------------------------------------------------------------
    */
    Route::get('/historial',             [HistorialController::class, 'index'])->name('historial.index');
    Route::get('/historial/{id_pedido}', [HistorialController::class, 'show'])->name('historial.show');

    /*
    |--------------------------------------------------------------------------
    | PERFIL
    |--------------------------------------------------------------------------
    */
    Route::get('/perfil', [UsuarioController::class, 'perfil'])->name('perfil');

    /*
    |--------------------------------------------------------------------------
    | RUTAS SOLO PARA ADMINISTRADOR
    |--------------------------------------------------------------------------
    |
    | Aquí usamos directamente la clase del middleware EnsureUserIsAdmin,
    | sin alias 'admin'.
    */
    Route::middleware([EnsureUserIsAdmin::class])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | MÓDULO: USUARIOS
        |--------------------------------------------------------------------------
        */
        Route::get('/usuarios',           [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create',    [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');

        /*
        |--------------------------------------------------------------------------
        | MÓDULO: EMPLEADOS
        |--------------------------------------------------------------------------
        */
        Route::resource('empleados', EmpleadoController::class)->names([
            'index'   => 'empleados.index',
            'create'  => 'empleados.create',
            'store'   => 'empleados.store',
            'edit'    => 'empleados.edit',
            'update'  => 'empleados.update',
            'destroy' => 'empleados.destroy',
        ])->parameters([
            'empleados' => 'id',
        ]);

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

        Route::get('/clientes/{id}/show',    [ClienteController::class, 'show'])->name('clientes.show');
        Route::get('/clientes/{id}/reporte', [ClienteController::class, 'reporte'])->name('clientes.reporte');

        /*
        |--------------------------------------------------------------------------
        | MÓDULO: VALORACIONES
        |--------------------------------------------------------------------------
        */
        Route::get('/valoraciones',         [ValoracionController::class, 'index'])->name('valoraciones.index');
        Route::get('/valoraciones/create',  [ValoracionController::class, 'create'])->name('valoraciones.create');
        Route::post('/valoraciones/store',  [ValoracionController::class, 'store'])->name('valoraciones.store');
        Route::get('/valoraciones/reporte', [ValoracionController::class, 'reporte'])->name('valoraciones.reporte');

        /*
        |--------------------------------------------------------------------------
        | MÓDULO: BITÁCORA
        |--------------------------------------------------------------------------
        */
        Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    });

});