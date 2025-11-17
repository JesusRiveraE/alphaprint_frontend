<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
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
        /* ============================================================
         * 🔐 GATES PARA ROLES (se usan en config/adminlte.php con 'can')
         * ==========================================================*/
        Gate::define('is-admin', function ($user = null) {
            // AdminLTE/GateFilter usará esta habilidad
            return Session::get('db_user_role') === 'Admin';
        });

        Gate::define('is-empleado', function ($user = null) {
            return Session::get('db_user_role') === 'Empleado';
        });

        /* ============================================================
         * 🌐 COMPOSER GLOBAL: NOTIFICACIONES Y PRÓXIMAS ENTREGAS
         * ==========================================================*/
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

            // Filtrar no leídas y ordenarlas de más reciente a más antigua
            $noLeidasAll = collect($notificaciones)
                ->filter(function ($n) {
                    // Considera como "no leída" cuando 'leido' esté vacío, null, false o 0
                    return empty($n['leido']);
                })
                ->sortByDesc('fecha')
                ->values()
                ->all();

            // 🔔 Todas las no leídas al navbar (el scroll hace el resto)
            $navbar_notificaciones = $noLeidasAll;

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
                'navbar_notificaciones'       => $navbar_notificaciones,
                'navbar_notificaciones_badge' => $navbar_notificaciones_badge,
                'navbar_entregas'             => $entregas,
            ]);
        });
    }
}
