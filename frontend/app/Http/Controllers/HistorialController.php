<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HistorialController extends Controller
{
    // 🔹 Listar todo el historial de pedidos
    public function index()
    {
        $response = Http::get('http://localhost:3000/api/historial');
        $historial = $response->json();
        return view('historial.index', compact('historial'));
    }

    // 🔹 Mostrar historial de un pedido específico
    public function show($id_pedido)
    {
        $response = Http::get("http://localhost:3000/api/historial/pedido/{$id_pedido}");
        $historial = $response->json();
        return view('historial.show', compact('historial', 'id_pedido'));
    }
}
