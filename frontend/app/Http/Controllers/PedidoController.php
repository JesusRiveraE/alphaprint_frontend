<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf; // 👈 Importación correcta para DomPDF

class PedidoController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        // Puedes mover esto a .env -> API_BASE=http://localhost:3000
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
            'fecha_entrega'  => ['nullable', 'date'],
        ]);

        $resp = Http::post($this->apiBase . '/api/pedidos', $data);

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
            'fecha_entrega'  => ['nullable', 'date'],
            'estado'         => ['nullable', 'in:Pendiente,En Progreso,Completado'],
        ]);

        // Primero actualizamos campos generales
        $respMain = Http::put($this->apiBase . "/api/pedidos/{$id}", [
            'id_cliente'    => $data['id_cliente'],
            'descripcion'   => $data['descripcion'] ?? null,
            'total'         => $data['total'],
            'fecha_entrega' => $data['fecha_entrega'] ?? null,
        ]);

        if (!$respMain->successful()) {
            return back()->withErrors(['api' => $respMain->json('error') ?? 'Error al actualizar pedido'])->withInput();
        }

        // Si se envió estado y cambió, hacemos la llamada específica
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

        // ✅ Uso correcto del facade Pdf
        $pdf = Pdf::loadView('pedidos.reporte', compact('pedido'));
        return $pdf->stream('pedido_' . $pedido['id_pedido'] . '.pdf');
    }
}
