<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        // De momento no necesitamos lógica complicada, solo mostrar la vista
        return view('home.index');
    }
}
