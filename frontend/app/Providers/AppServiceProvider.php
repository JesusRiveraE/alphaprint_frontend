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
        // 🔹 Compositor global: Notificaciones y Entregas próximas
        View::composer('*', function ($view) {
            // -------------------------------
            // 🔸 NOTIFICACIONES
            // -------------------------------
            try {
                $respNoti = Http::get('http://localhost:3000/api/notificaciones');
                $notificaciones = $respNoti->json() ?? [];
            } catch (\Throwable $e) {
                $notificaciones = [];
            }

            // -------------------------------
            // 🔸 CALENDARIO (Próximas entregas)
            // -------------------------------
            $entregas = [];

            if (Session::has('firebase_user')) {
                try {
                    $respPedidos = Http::get('http://localhost:3000/api/pedidos');
                    $pedidos = $respPedidos->json() ?? [];
                    $today = Carbon::today();

                    // Filtrar pedidos con fecha_entrega >= hoy
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
                'navbar_notificaciones' => $notificaciones,
                'navbar_entregas' => $entregas,
            ]);
        });
    }
}
