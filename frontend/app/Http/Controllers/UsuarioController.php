<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class UsuarioController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:3000/api/usuarios');
        $usuarios = $response->json() ?? [];
        return view('usuarios.index', compact('usuarios'));
    }
}