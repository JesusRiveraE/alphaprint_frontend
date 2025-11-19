<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class BitacoraController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://localhost:3000/api/bitacora');

            if ($response->successful()) {
                $bitacora = $response->json() ?? [];
            } else {
                $bitacora = [];
            }
        } catch (\Throwable $e) {
            // Si la API cae o no responde
            $bitacora = [];
        }

        return view('bitacora.index', compact('bitacora'));
    }
}
