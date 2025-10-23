<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 Consultas al backend
        $clientes = Http::get('http://localhost:3000/api/clientes')->json() ?? [];
        $pedidos = Http::get('http://localhost:3000/api/pedidos')->json() ?? [];
        $valoraciones = Http::get('http://localhost:3000/api/valoraciones')->json() ?? [];
        $notificaciones = Http::get('http://localhost:3000/api/notificaciones')->json() ?? [];
        $bitacora = Http::get('http://localhost:3000/api/bitacora')->json() ?? [];

        // 🔹 Totales
        $totalClientes = count($clientes);
        $totalPedidos = count($pedidos);
        $totalValoraciones = count($valoraciones);
        $totalNotificaciones = count($notificaciones);

        // 🔹 Estadísticas
        $pedidosPorEstado = collect($pedidos)->groupBy('estado')->map->count();

        // 🔹 Promedio general de valoraciones
        $promedioValoracion = count($valoraciones)
            ? round(array_sum(array_column($valoraciones, 'puntuacion')) / count($valoraciones), 2)
            : 0;

        // 🔹 Distribución de valoraciones (para histograma)
        $valoracionesPorPuntuacion = collect($valoraciones)->groupBy('puntuacion')->map->count();

        // 🔹 Últimos registros
        $ultimosPedidos = array_slice(array_reverse($pedidos), 0, 5);
        $ultimasBitacora = array_slice(array_reverse($bitacora), 0, 5);
        $ultimasNotificaciones = array_slice(array_reverse($notificaciones), 0, 5);

        // 🔹 Porcentajes de estados
        $porcentajePendientes = $totalPedidos > 0
            ? round(($pedidosPorEstado['Pendiente'] ?? 0) * 100 / $totalPedidos, 1)
            : 0;
        $porcentajeProgreso = $totalPedidos > 0
            ? round(($pedidosPorEstado['En Progreso'] ?? 0) * 100 / $totalPedidos, 1)
            : 0;
        $porcentajeCompletados = $totalPedidos > 0
            ? round(($pedidosPorEstado['Completado'] ?? 0) * 100 / $totalPedidos, 1)
            : 0;

        return view('dashboard.index', compact(
            'totalClientes',
            'totalPedidos',
            'totalValoraciones',
            'totalNotificaciones',
            'pedidosPorEstado',
            'valoracionesPorPuntuacion',
            'promedioValoracion',
            'ultimosPedidos',
            'ultimasBitacora',
            'ultimasNotificaciones',
            'porcentajePendientes',
            'porcentajeProgreso',
            'porcentajeCompletados'
        ));
    }
}
