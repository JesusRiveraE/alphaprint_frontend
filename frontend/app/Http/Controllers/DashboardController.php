<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 Consultas al backend
        $clientes       = Http::get('http://localhost:3000/api/clientes')->json() ?? [];
        $pedidos        = Http::get('http://localhost:3000/api/pedidos')->json() ?? [];
        $valoraciones   = Http::get('http://localhost:3000/api/valoraciones')->json() ?? [];
        $notificaciones = Http::get('http://localhost:3000/api/notificaciones')->json() ?? [];
        $bitacora       = Http::get('http://localhost:3000/api/bitacora')->json() ?? [];

        // 🔹 Totales generales
        $totalClientes        = count($clientes);
        $totalPedidos         = count($pedidos);
        $totalValoraciones    = count($valoraciones);
        $totalNotificaciones  = count($notificaciones); // por si lo quieres usar en otra parte

        // 🔔 SOLO NOTIFICACIONES NO LEÍDAS (para la caja del dashboard)
        $notificacionesNoLeidas = collect($notificaciones)
            ->filter(fn($n) => empty($n['leido']) || $n['leido'] == 0)
            ->count();

        // 🔹 Estadísticas de pedidos por estado
        $pedidosPorEstado = collect($pedidos)->groupBy('estado')->map->count();

        $totalPendientes   = $pedidosPorEstado['Pendiente']    ?? 0;
        $totalProgreso     = $pedidosPorEstado['En Progreso']  ?? 0;
        $totalCompletados  = $pedidosPorEstado['Completado']   ?? 0;

        // 🔹 Promedio general de valoraciones
        $promedioValoracion = count($valoraciones)
            ? round(array_sum(array_column($valoraciones, 'puntuacion')) / count($valoraciones), 2)
            : 0;

        // 🔹 Distribución de valoraciones (histograma)
        $valoracionesPorPuntuacion = collect($valoraciones)->groupBy('puntuacion')->map->count();

        // 🟢 Pedidos ordenados por fecha de entrega (más próxima primero)
        $ultimosPedidos = collect($pedidos)
            ->sortBy(function ($p) {
                if (empty($p['fecha_entrega'])) {
                    // Si no tiene fecha, lo mandamos al final
                    return Carbon::parse('3000-12-31');
                }
                return Carbon::parse($p['fecha_entrega']);
            })
            ->values()
            ->all();

        // 🟢 Últimas bitácoras (solo 5)
        $ultimasBitacora = collect($bitacora)
            ->sortByDesc(fn($b) => $b['fecha'] ?? 0)
            ->take(5)
            ->values()
            ->all();

        // 🟢 Últimas notificaciones (solo 5 recientes)
        $ultimasNotificaciones = collect($notificaciones)
            ->sortByDesc(fn($n) => $n['fecha'] ?? 0)
            ->take(5)
            ->values()
            ->all();

        return view('dashboard.index', compact(
            'totalClientes',
            'totalPedidos',
            'totalValoraciones',
            'totalNotificaciones',
            'notificacionesNoLeidas',
            'pedidosPorEstado',
            'valoracionesPorPuntuacion',
            'promedioValoracion',
            'ultimosPedidos',
            'ultimasBitacora',
            'ultimasNotificaciones',
            'totalPendientes',
            'totalProgreso',
            'totalCompletados'
        ));
    }
}
