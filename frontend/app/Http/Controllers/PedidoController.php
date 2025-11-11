<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        // Sugerencia: en .env -> API_BASE=http://localhost:3000
        $this->apiBase = rtrim(env('API_BASE', 'http://localhost:3000'), '/');
    }

    public function index()
    {
        $resp = Http::get($this->apiBase . '/api/pedidos');
        $pedidos = $resp->json() ?? [];
        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        // Traer clientes para el <select>
        $clientes = Http::get($this->apiBase . '/api/clientes')->json() ?? [];
        return view('pedidos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_cliente'     => ['required', 'integer'],
            'descripcion'    => ['nullable', 'string', 'max:255'],
            'total'          => ['required', 'numeric', 'min:0'],
            'estado'         => ['nullable', 'in:Pendiente,En Progreso,Completado'],
            'fecha_entrega'  => ['nullable', 'date'],       // Y-m-d
            'hora_entrega'   => ['nullable', 'date_format:H:i'], // HH:mm
        ]);

        // Combinar fecha + hora si ambas vienen
        if (!empty($data['fecha_entrega']) && !empty($data['hora_entrega'])) {
            $data['fecha_entrega'] = $data['fecha_entrega'] . ' ' . $data['hora_entrega'] . ':00';
        }
        // Ya no necesitamos enviar hora por separado
        unset($data['hora_entrega']);

        // Si no envían estado, el backend usará el default (Pendiente)
        $payload = [
            'id_cliente'    => $data['id_cliente'],
            'descripcion'   => $data['descripcion'] ?? null,
            'total'         => $data['total'],
            'estado'        => $data['estado'] ?? null,
            'fecha_entrega' => $data['fecha_entrega'] ?? null,
        ];

        $resp = Http::post($this->apiBase . '/api/pedidos', $payload);

        if ($resp->successful()) {
            Session::flash('success', 'Pedido creado con éxito');
            return redirect()->route('pedidos.index');
        }

        return back()->withErrors(['api' => $resp->json('error') ?? 'Error al crear pedido'])->withInput();
    }

    public function edit($id)
    {
        $pedido = Http::get($this->apiBase . "/api/pedidos/{$id}")->json();
        if (!$pedido) {
            abort(404);
        }

        $clientes = Http::get($this->apiBase . '/api/clientes')->json() ?? [];
        return view('pedidos.edit', compact('pedido', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id_cliente'     => ['required', 'integer'],
            'descripcion'    => ['nullable', 'string', 'max:255'],
            'total'          => ['required', 'numeric', 'min:0'],
            'fecha_entrega'  => ['nullable', 'date'],            // Y-m-d
            'hora_entrega'   => ['nullable', 'date_format:H:i'], // HH:mm
            'estado'         => ['nullable', 'in:Pendiente,En Progreso,Completado'],
        ]);

        // Combinar fecha + hora si ambas vienen
        if (!empty($data['fecha_entrega']) && !empty($data['hora_entrega'])) {
            $data['fecha_entrega'] = $data['fecha_entrega'] . ' ' . $data['hora_entrega'] . ':00';
        }
        unset($data['hora_entrega']);

        // 1) Actualizar campos principales
        $respMain = Http::put($this->apiBase . "/api/pedidos/{$id}", [
            'id_cliente'    => $data['id_cliente'],
            'descripcion'   => $data['descripcion'] ?? null,
            'total'         => $data['total'],
            'fecha_entrega' => $data['fecha_entrega'] ?? null,
        ]);

        if (!$respMain->successful()) {
            return back()->withErrors(['api' => $respMain->json('error') ?? 'Error al actualizar pedido'])->withInput();
        }

        // 2) Cambiar estado (si viene)
        if (!empty($data['estado'])) {
            $respEstado = Http::put($this->apiBase . "/api/pedidos/{$id}/estado", [
                'estado' => $data['estado'],
            ]);

            if (!$respEstado->successful()) {
                return back()->withErrors(['api' => $respEstado->json('error') ?? 'Error al cambiar estado'])->withInput();
            }
        }

        Session::flash('success', 'Pedido actualizado con éxito');
        return redirect()->route('pedidos.index');
    }

    public function destroy($id)
    {
        $resp = Http::delete($this->apiBase . "/api/pedidos/{$id}");

        if ($resp->successful()) {
            Session::flash('success', 'Pedido eliminado con éxito');
            return redirect()->route('pedidos.index');
        }

        return back()->withErrors(['api' => $resp->json('error') ?? 'Error al eliminar pedido']);
    }

    public function show($id)
    {
        $pedido = Http::get($this->apiBase . "/api/pedidos/{$id}")->json();
        if (!$pedido) {
            abort(404);
        }
        return view('pedidos.show', compact('pedido'));
    }

    public function reporte($id)
    {
        $pedido = Http::get($this->apiBase . "/api/pedidos/{$id}")->json();
        if (!$pedido) {
            abort(404);
        }

        $pdf = Pdf::loadView('pedidos.reporte', compact('pedido'));
        return $pdf->stream('pedido_' . $pedido['id_pedido'] . '.pdf');
    }

    public function updateEstado(Request $request, $id)
{
    $data = $request->validate([
        'estado' => ['required', 'in:Pendiente,En Progreso,Completado'],
    ]);

    $resp = Http::put($this->apiBase . "/api/pedidos/{$id}/estado", [
        'estado' => $data['estado'],
    ]);

    if ($resp->successful()) {
        return response()->json(['ok' => true]);
    }

    return response()->json([
        'ok' => false,
        'error' => $resp->json('error') ?? 'Error al cambiar estado'
    ], 422);
}

}
