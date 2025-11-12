<?php
namespace App\Http\Controllers;

// 1. Quita "use Illuminate\Support\Facades\DB;" (ya no se usa)

class EmpleadoController extends Controller
{
    public function index()
    {
        // 2. Vuelve a poner el array vacío.
        // El JavaScript se encargará de cargar los datos.
        $empleados = []; 
        return view('empleados.index', compact('empleados'));
    }
}