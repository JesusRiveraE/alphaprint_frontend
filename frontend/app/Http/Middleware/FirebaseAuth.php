<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FirebaseAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Si no hay sesión activa, redirige al login
        if (!session()->has('firebase_user')) {
            return redirect()->route('login');
        }

        // Registrar la hora en que se inició la sesión (solo la primera vez)
        if (!session()->has('session_started_at')) {
            session(['session_started_at' => now('America/Tegucigalpa')]);
        }

        return $next($request);
    }
}
