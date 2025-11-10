<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ArchivoController extends Controller
{
    // Lista general de archivos (usa GET /api/archivos)
    public function index()
    {
        $archivos = Http::get('http://localhost:3000/api/archivos')->json() ?? [];
        return view('archivos.index', compact('archivos'));
    }

    // Formulario de alta: cargamos pedidos para el select
    public function create()
    {
        // Trae todos los pedidos para poder anclar el archivo
        $pedidos = Http::get('http://localhost:3000/api/pedidos')->json() ?? [];
        return view('archivos.create', compact('pedidos'));
    }

    // Guarda (POST /api/archivos)
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pedido'  => 'required|integer',
            'url'        => 'required|url',
            'comentario' => 'nullable|string|max:255',
        ]);

        $resp = Http::post('http://localhost:3000/api/archivos', $data);

        if ($resp->successful()) {
            return redirect()->route('archivos.index')->with('status', 'Archivo registrado con éxito');
        }

        return back()->withErrors(['api' => 'Error al registrar el archivo: '.$resp->body()])->withInput();
    }
}
