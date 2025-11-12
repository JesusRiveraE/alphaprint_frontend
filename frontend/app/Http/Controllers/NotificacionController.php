<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificacionController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        $this->apiBase = rtrim(env('API_BASE', 'http://localhost:3000'), '/');
    }

    public function index()
    {
        try {
            $response = Http::timeout(8)->get($this->apiBase.'/api/notificaciones');
            $notificaciones = $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('No se pudo obtener notificaciones: '.$e->getMessage());
            $notificaciones = [];
        }

        return view('notificaciones.index', compact('notificaciones'));
    }

    // PUT /notificaciones/{id}/leido  (AJAX)
    public function markAsRead($id)
    {
        try {
            $res = Http::timeout(8)->put($this->apiBase."/api/notificaciones/{$id}/leido");
            if ($res->successful() && ($res->json('ok') === true || $res->json('message'))) {
                return response()->json(['ok' => true]);
            }
            return response()->json(['ok' => false, 'error' => $res->json('error') ?? 'API no exitosa'], 500);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
