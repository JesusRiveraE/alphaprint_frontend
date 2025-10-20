<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BitacoraController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:3000/api/bitacora');
        $bitacora = $response->json();
        return view('bitacora.index', compact('bitacora'));
    }
}
