<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmpleadoController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:3000/api/empleados');
        $empleados = $response->json();
        return view('empleados.index', compact('empleados'));
    }
}
