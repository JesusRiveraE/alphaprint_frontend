<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            try {
                $response = Http::get('http://localhost:3000/api/notificaciones');
                $notificaciones = $response->json();
            } catch (\Exception $e) {
                $notificaciones = [];
            }

            $view->with('navbar_notificaciones', $notificaciones);
        });
    }
}
