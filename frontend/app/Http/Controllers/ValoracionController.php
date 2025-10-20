<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ValoracionController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:3000/api/valoraciones');
        $valoraciones = $response->json() ?? [];
        return view('valoraciones.index', compact('valoraciones'));
    }
}
