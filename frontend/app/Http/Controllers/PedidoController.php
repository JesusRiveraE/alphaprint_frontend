<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PedidoController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:3000/api/pedidos');
        $pedidos = $response->json();
        return view('pedidos.index', compact('pedidos'));
    }
}
