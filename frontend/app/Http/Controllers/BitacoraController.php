<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class BitacoraController extends Controller
{
    public function index()
    {
        // Llamada al backend Node
        try {
            $resp = Http::timeout(10)->get('http://localhost:3000/api/bitacora');
            $bitacora = $resp->successful() ? ($resp->json() ?? []) : [];
        } catch (\Throwable $e) {
            $bitacora = [];
        }

        // Asegurar llaves y tipado básico
        $bitacora = collect($bitacora)->map(function ($row) {
            return [
                'id_bitacora' => Arr::get($row, 'id_bitacora'),
                'usuario'     => Arr::get($row, 'usuario', 'Sistema'),
                'modulo'      => Arr::get($row, 'modulo', '—'),
                'accion'      => Arr::get($row, 'accion', ''),
                'fecha'       => Arr::get($row, 'fecha'), // se formatea en la vista a UTC-6
            ];
        })->all();

        return view('bitacora.index', compact('bitacora'));
    }
}
