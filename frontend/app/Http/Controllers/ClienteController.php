<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ClienteController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        $this->apiBase = rtrim(env('API_BASE', 'http://localhost:3000'), '/');
    }

    /**
     * Formatea una fecha a America/Tegucigalpa con el patrón d/m/Y H:i:s.
     */
    private function fmtTegus(?string $dateTime): ?string
    {
        if (empty($dateTime)) return null;

        try {
            return Carbon::parse($dateTime)
                ->timezone('America/Tegucigalpa')
                ->format('d/m/Y H:i:s');
        } catch (\Throwable $e) {
            // Si llega una fecha inválida, devolvemos la cadena original
            return $dateTime;
        }
    }

    /**
     * Normaliza un cliente para asegurar fecha_creacion en TZ -6 y formato estándar.
     */
    private function normalizeCliente(array $c): array
    {
        // Conserva original por si te sirve (no lo usan las vistas, pero es útil)
        if (isset($c['fecha_creacion'])) {
            $c['fecha_creacion_original'] = $c['fecha_creacion'];
            $c['fecha_creacion'] = $this->fmtTegus($c['fecha_creacion']); // sobrescribe para que las vistas no cambien
        }
        return $c;
    }

    /**
     * Normaliza lista de clientes.
     */
    private function normalizeClientes(array $clientes): array
    {
        return array_map(function ($c) {
            return is_array($c) ? $this->normalizeCliente($c) : $c;
        }, $clientes);
    }

    public function index()
    {
        $resp = Http::get($this->apiBase . '/api/clientes');
        $clientes = $resp->json() ?? [];

        // Formato y TZ -6 para todas las fechas
        if (is_array($clientes)) {
            $clientes = $this->normalizeClientes($clientes);
        }

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

        // Normaliza fechas para el formulario (si las muestras)
        $cliente = $this->normalizeCliente($cliente);

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

        // Normaliza la fecha para la vista de detalle
        $cliente = $this->normalizeCliente($cliente);

        return view('clientes.show', compact('cliente'));
    }

    public function reporte($id)
    {
        $cliente = Http::get($this->apiBase . "/api/clientes/{$id}")->json();
        if (!$cliente) abort(404);

        // Normaliza antes de mandar al PDF
        $cliente = $this->normalizeCliente($cliente);

        $pdf = Pdf::loadView('clientes.reporte', compact('cliente'));
        return $pdf->stream('cliente_' . $cliente['id_cliente'] . '.pdf');
    }
}