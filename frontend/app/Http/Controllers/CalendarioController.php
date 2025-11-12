<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class CalendarioController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get('http://localhost:3000/api/pedidos');
            $pedidos = $response->json() ?? [];
        } catch (\Exception $e) {
            $pedidos = [];
        }

        // Construir eventos válidos
        $eventos = collect($pedidos)
            ->filter(fn($p) => !empty($p['fecha_entrega']))
            ->map(fn($p) => [
                'title' => $p['descripcion'] ?? ('Pedido #' . ($p['id_pedido'] ?? '')),
                'start' => $p['fecha_entrega'],
                'backgroundColor' => match ($p['estado'] ?? '') {
                    'Pendiente'   => '#edb629ff',
                    'En Progreso' => '#17a2b8',
                    default       => '#28a745',
                },
                'borderColor' => '#fff',
                'textColor' => '#fff',
                'extendedProps' => [
                    'id_pedido'      => $p['id_pedido'] ?? null,
                    'cliente_nombre' => $p['cliente_nombre'] ?? '',
                    'descripcion'    => $p['descripcion'] ?? '',
                    'total'          => $p['total'] ?? 0,
                    'estado'         => $p['estado'] ?? '',
                    'fecha_entrega'  => $p['fecha_entrega'] ?? '',
                ]
            ])
            ->values()
            ->all();

        // 🧠 DEBUG TEMPORAL — para confirmar datos
        // dd($eventos);

        return view('calendario.index', compact('eventos'));
    }
}
