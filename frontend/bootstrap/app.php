<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 👇 Importamos tu middleware personalizado
use App\Http\Middleware\FirebaseAuth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Rutas principales de la aplicación
        web: __DIR__ . '/../routes/web.php',

        // ⚠️ Laravel espera por defecto este archivo (aunque esté vacío)
        // Crea routes/api.php si no existe, o deja esta línea para evitar errores
        api: __DIR__ . '/../routes/api.php',

        // Rutas de comandos
        commands: __DIR__ . '/../routes/console.php',

        // Ruta de health-check opcional
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // 👇 Aquí registramos tus middlewares personalizados
        $middleware->alias([
            'auth.firebase' => FirebaseAuth::class, // Middleware para proteger rutas con sesión Firebase
        ]);

        // Si quieres aplicar middlewares globales, puedes usar:
        // $middleware->web(append: [\App\Http\Middleware\MiGlobal::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
