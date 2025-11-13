<?php

namespace App\Http\Controllers;

class UsuarioController extends Controller
{
    /**
     * Listado de usuarios.
     * La tabla se llena vía JS usando authorizedFetch contra la API.
     */
    public function index()
    {
        // Ya no necesitamos pasar $usuarios, la vista usa solo JS.
        return view('usuarios.index');
    }

    /**
     * Formulario para crear un nuevo usuario.
     * El guardado se hace vía JS (authorizedFetch POST /api/usuarios).
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Formulario para editar un usuario existente.
     * Solo pasamos el ID y la vista llama a la API para traer los datos.
     */
    public function edit($id)
    {
        $usuarioId = $id;
        return view('usuarios.edit', compact('usuarioId'));
    }

    /**
     * Vista de perfil del usuario autenticado (datos Firebase en sesión).
     */
    public function perfil()
    {
        $usuario = session('firebase_user'); // datos de Firebase ya guardados
        return view('perfil.index', compact('usuario'));
    }
}
