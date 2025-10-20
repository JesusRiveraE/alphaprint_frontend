<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // Consumir datos desde tu backend en Node.js
        $clientes = Http::get('http://localhost:3000/api/clientes')->json();
        $pedidos = Http::get('http://localhost:3000/api/pedidos')->json();
        $empleados = Http::get('http://localhost:3000/api/empleados')->json();
        $usuarios = Http::get('http://localhost:3000/api/usuarios')->json();
        $valoraciones = Http::get('http://localhost:3000/api/valoraciones')->json();
        $notificaciones = Http::get('http://localhost:3000/api/notificaciones')->json();
        $bitacora = Http::get('http://localhost:3000/api/bitacora')->json();

        // Métricas básicas
        $totalClientes = count($clientes ?? []);
        $totalPedidos = count($pedidos ?? []);
        $totalEmpleados = count($empleados ?? []);
        $totalUsuarios = count($usuarios ?? []);
        $totalValoraciones = count($valoraciones ?? []);
        $totalNotificaciones = count($notificaciones ?? []);

        // Ejemplo: Pedidos por estado
        $pedidosPendientes = collect($pedidos)->where('estado', 'Pendiente')->count();
        $pedidosProgreso   = collect($pedidos)->where('estado', 'En Progreso')->count();
        $pedidosCompletados= collect($pedidos)->where('estado', 'Completado')->count();

        return view('dashboard.index', compact(
            'totalClientes',
            'totalPedidos',
            'totalEmpleados',
            'totalUsuarios',
            'totalValoraciones',
            'totalNotificaciones',
            'pedidosPendientes',
            'pedidosProgreso',
            'pedidosCompletados',
            'bitacora'
        ));
    }
}
