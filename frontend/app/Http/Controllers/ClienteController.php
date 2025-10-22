<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class ClienteController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        $this->apiBase = rtrim(env('API_BASE', 'http://localhost:3000'), '/');
    }

    public function index()
    {
        $resp = Http::get($this->apiBase . '/api/clientes');
        $clientes = $resp->json() ?? [];
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo'   => ['nullable', 'email', 'max:100'],
        ]);

        $resp = Http::post($this->apiBase . '/api/clientes', $data);

        if ($resp->successful()) {
            Session::flash('success', 'Cliente creado con éxito');
            return redirect()->route('clientes.index');
        }

        return back()->withErrors(['api' => 'Error al crear cliente'])->withInput();
    }

    public function edit($id)
    {
        $cliente = Http::get($this->apiBase . "/api/clientes/{$id}")->json();
        if (!$cliente) abort(404);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo'   => ['nullable', 'email', 'max:100'],
        ]);

        $resp = Http::put($this->apiBase . "/api/clientes/{$id}", $data);

        if ($resp->successful()) {
            Session::flash('success', 'Cliente actualizado con éxito');
            return redirect()->route('clientes.index');
        }

        return back()->withErrors(['api' => 'Error al actualizar cliente'])->withInput();
    }

    public function destroy($id)
    {
        $resp = Http::delete($this->apiBase . "/api/clientes/{$id}");

        if ($resp->successful()) {
            Session::flash('success', 'Cliente eliminado con éxito');
            return redirect()->route('clientes.index');
        }

        return back()->withErrors(['api' => 'Error al eliminar cliente']);
    }

    public function show($id)
    {
        $cliente = Http::get($this->apiBase . "/api/clientes/{$id}")->json();
        if (!$cliente) abort(404);
        return view('clientes.show', compact('cliente'));
    }

    public function reporte($id)
    {
        $cliente = Http::get($this->apiBase . "/api/clientes/{$id}")->json();
        if (!$cliente) abort(404);

        $pdf = Pdf::loadView('clientes.reporte', compact('cliente'));
        return $pdf->stream('cliente_'.$cliente['id_cliente'].'.pdf');
    }
}
