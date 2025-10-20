<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ArchivoController extends Controller
{
    public function index()
    {
        // Consumimos la API REST del backend
        $response = Http::get('http://localhost:3000/api/archivos');
        $archivos = $response->json() ?? [];

        return view('archivos.index', compact('archivos'));
    }
}