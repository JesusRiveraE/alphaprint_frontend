<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Leemos el rol que guardamos en /firebase/login
        $role = Session::get('db_user_role');

        if ($role !== 'Admin') {
            // Si la petición espera JSON, devolvemos 403 en JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Acceso denegado. No tienes permisos de administrador.',
                ], 403);
            }

            // Para peticiones normales (vistas), redirigimos a algo seguro
            return redirect()
                ->route('notificaciones.index')
                ->with('error', 'No tienes permisos de administrador.');
        }

        return $next($request);
    }
}
