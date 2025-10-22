<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class ValoracionController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        $this->apiBase = rtrim(env('API_BASE', 'http://localhost:3000'), '/');
    }

    // Mostrar listado
    public function index()
    {
        $resp = Http::get($this->apiBase . '/api/valoraciones');
        $valoraciones = $resp->json() ?? [];

        return view('valoraciones.index', compact('valoraciones'));
    }

    // Crear nueva valoración
    public function create()
    {
        return view('valoraciones.create');
    }

    // Guardar valoración
    public function store(Request $request)
    {
        $data = $request->validate([
            'puntuacion' => ['required', 'integer', 'min:1', 'max:5'],
            'comentario' => ['nullable', 'string', 'max:255'],
        ]);

        $resp = Http::post($this->apiBase . '/api/valoraciones', $data);

        if ($resp->successful()) {
            Session::flash('success', 'Valoración registrada con éxito');
            return redirect()->route('valoraciones.index');
        }

        return back()->withErrors(['api' => 'Error al registrar la valoración'])->withInput();
    }

    // Generar reporte PDF con todas las valoraciones
    public function reporte()
    {
        $resp = Http::get($this->apiBase . '/api/valoraciones');
        $valoraciones = $resp->json() ?? [];

        $pdf = Pdf::loadView('valoraciones.reporte', compact('valoraciones'))
                  ->setPaper('A4', 'portrait');

        return $pdf->stream('reporte_valoraciones.pdf');
    }
}
