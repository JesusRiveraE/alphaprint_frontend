<?php
namespace App\Http\Controllers;

// ⛔️ No se necesita 'use Illuminate\Support\Facades\Http;'

class UsuarioController extends Controller
{
    public function index()
    {
        // ✅ Solo pasa un array vacío. Esto arregla el crash.
        $usuarios = []; 
        return view('usuarios.index', compact('usuarios'));
    }
}