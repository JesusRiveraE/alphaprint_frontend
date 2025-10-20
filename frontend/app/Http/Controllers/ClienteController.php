<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ClienteController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:3000/api/clientes');
        $clientes = $response->json() ?? [];
        return view('clientes.index', compact('clientes'));
    }
}
