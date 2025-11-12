<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // 🔹 Compositor global: Notificaciones (solo no leídas) y Entregas próximas
        View::composer('*', function ($view) {
            // -------------------------------
            // 🔸 NOTIFICACIONES (solo no leídas para el dropdown)
            // -------------------------------
            try {
                $respNoti = Http::get('http://localhost:3000/api/notificaciones');
                $notificaciones = $respNoti->json() ?? [];
            } catch (\Throwable $e) {
                $notificaciones = [];
            }

            // Filtrar no leídas
            $noLeidasAll = array_values(array_filter($notificaciones, function ($n) {
                // Considera como "no leída" cuando 'leido' esté vacío, null, false o 0
                return empty($n['leido']);
            }));

            // Máximo 5 en el dropdown de la campana
            $navbar_notificaciones = array_slice($noLeidasAll, 0, 5);

            // Conteo total de no leídas para el badge
            $navbar_notificaciones_badge = count($noLeidasAll);

            // -------------------------------
            // 🔸 CALENDARIO (Próximas entregas)
            // -------------------------------
            $entregas = [];

            if (Session::has('firebase_user')) {
                try {
                    $respPedidos = Http::get('http://localhost:3000/api/pedidos');
                    $pedidos = $respPedidos->json() ?? [];
                    $today = Carbon::today();

                    // Filtrar pedidos con fecha_entrega válida y >= hoy
                    $entregas = collect($pedidos)
                        ->filter(function ($p) use ($today) {
                            if (empty($p['fecha_entrega'])) return false;
                            try {
                                $fecha = Carbon::parse($p['fecha_entrega']);
                            } catch (\Throwable $e) {
                                return false;
                            }
                            return $fecha >= $today;
                        })
                        ->sortBy('fecha_entrega')
                        ->take(8)
                        ->values()
                        ->all();
                } catch (\Throwable $e) {
                    $entregas = [];
                }
            }

            // Pasar a todas las vistas
            $view->with([
                // 🔔 Solo no leídas (máx 5) para listar en el dropdown
                'navbar_notificaciones' => $navbar_notificaciones,

                // 🔢 Conteo real de no leídas (para el badge)
                'navbar_notificaciones_badge' => $navbar_notificaciones_badge,

                // 📅 Próximas entregas
                'navbar_entregas' => $entregas,
            ]);
        });
    }
}
