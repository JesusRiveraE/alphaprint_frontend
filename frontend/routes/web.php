<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ValoracionController;
use App\Http\Controllers\DashboardController;

Route::get('/home', [DashboardController::class, 'index'])->name('dashboard');


Route::get('/', function () {
    return view('home');
});

Route::resource('clientes', ClienteController::class);
Route::resource('pedidos', PedidoController::class);
Route::resource('empleados', EmpleadoController::class);
Route::resource('usuarios', UsuarioController::class);
Route::resource('bitacora', BitacoraController::class);
Route::resource('notificaciones', NotificacionController::class);
Route::resource('valoraciones', ValoracionController::class);


Route::get('/', function () {
    return view('welcome');
});
