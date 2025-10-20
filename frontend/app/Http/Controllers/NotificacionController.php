<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificacionController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:3000/api/notificaciones');
        $notificaciones = $response->json();
        return view('notificaciones.index', compact('notificaciones'));
    }
}
