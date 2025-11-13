<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class EmpleadoController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        $this->apiBase = rtrim(env('API_BASE', 'http://localhost:3000'), '/');
    }

    /**
     * Listado: la tabla se llena por JS.
     */
    public function index()
    {
        return view('empleados.index');
    }

    /**
     * Formulario de nuevo empleado.
     * (Usuarios se cargan por JS desde la API.)
     */
    public function create()
    {
        return view('empleados.create');
    }

    /**
     * Guardar empleado nuevo.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'     => ['required', 'string', 'max:150'],
            'telefono'   => ['nullable', 'string', 'max:20'],
            'area'       => ['required', 'string', 'max:100'],
            'id_usuario' => ['required', 'integer'],
        ]);

        $firebaseToken = $request->input('firebase_token');

        $resp = Http::withToken($firebaseToken)
            ->post($this->apiBase . '/api/empleados', $data);

        if ($resp->successful()) {
            Session::flash('success', 'Empleado creado con éxito');
            return redirect()->route('empleados.index');
        }

        $body    = $resp->json();
        $mensaje = $body['error'] ?? $body['details'] ?? 'Error al crear empleado';

        return back()
            ->withErrors(['api' => $mensaje])
            ->withInput();
    }

    /**
     * Editar empleado existente.
     * OJO: aquí ya NO llamamos a la API.
     * Solo pasamos el ID, y el JS de la vista se encarga de consultar /api/empleados/{id}.
     */
    public function edit($id)
    {
        return view('empleados.edit', [
            'empleadoId' => $id,
        ]);
    }

    /**
     * Actualizar empleado.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre'     => ['required', 'string', 'max:150'],
            'telefono'   => ['nullable', 'string', 'max:20'],
            'area'       => ['required', 'string', 'max:100'],
            'id_usuario' => ['required', 'integer'],
        ]);

        $firebaseToken = $request->input('firebase_token');

        $resp = Http::withToken($firebaseToken)
            ->put($this->apiBase . "/api/empleados/{$id}", $data);

        if ($resp->successful()) {
            Session::flash('success', 'Empleado actualizado con éxito');
            return redirect()->route('empleados.index');
        }

        $body    = $resp->json();
        $mensaje = $body['error'] ?? $body['details'] ?? 'Error al actualizar empleado';

        return back()
            ->withErrors(['api' => $mensaje])
            ->withInput();
    }

    /**
     * Eliminar empleado (actualmente lo haces por JS contra la API).
     */
    public function destroy($id)
    {
        $resp = Http::delete($this->apiBase . "/api/empleados/{$id}");

        if ($resp->successful()) {
            Session::flash('success', 'Empleado eliminado con éxito');
            return redirect()->route('empleados.index');
        }

        return back()->withErrors(['api' => 'Error al eliminar empleado']);
    }
}