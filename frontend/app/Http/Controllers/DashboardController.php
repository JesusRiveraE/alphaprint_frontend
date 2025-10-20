<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 Consultas al backend
        $clientes = Http::get('http://localhost:3000/api/clientes')->json() ?? [];
        $pedidos = Http::get('http://localhost:3000/api/pedidos')->json() ?? [];
        $valoraciones = Http::get('http://localhost:3000/api/valoraciones')->json() ?? [];
        $notificaciones = Http::get('http://localhost:3000/api/notificaciones')->json() ?? [];
        $empleados = Http::get('http://localhost:3000/api/empleados')->json() ?? [];
        $bitacora = Http::get('http://localhost:3000/api/bitacora')->json() ?? [];

        // 🔹 Totales
        $totalClientes = count($clientes);
        $totalPedidos = count($pedidos);
        $totalValoraciones = count($valoraciones);
        $totalNotificaciones = count($notificaciones);
        $totalEmpleados = count($empleados);

        // 🔹 Estadísticas
        $pedidosPorEstado = collect($pedidos)->groupBy('estado')->map->count();
        $empleadosPorArea = collect($empleados)->groupBy('area')->map->count();

        // 🔹 Promedio de valoraciones
        $promedioValoracion = count($valoraciones)
            ? round(array_sum(array_column($valoraciones, 'puntuacion')) / count($valoraciones), 2)
            : 0;

        // 🔹 Últimos registros
        $ultimosPedidos = array_slice(array_reverse($pedidos), 0, 5);
        $ultimasValoraciones = array_slice(array_reverse($valoraciones), 0, 5);
        $ultimasBitacora = array_slice(array_reverse($bitacora), 0, 5);

        return view('dashboard.index', compact(
            'totalClientes',
            'totalPedidos',
            'totalValoraciones',
            'totalNotificaciones',
            'totalEmpleados',
            'pedidosPorEstado',
            'empleadosPorArea',
            'promedioValoracion',
            'ultimosPedidos',
            'ultimasValoraciones',
            'ultimasBitacora'
        ));
    }
}
