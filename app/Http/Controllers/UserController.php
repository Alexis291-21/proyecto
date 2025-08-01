<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Aplica políticas de autorización para todas las acciones de recurso.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'usuario');
    }

    /**
     * Mostrar listado de usuarios.
     */
    public function index()
    {
        $usuarios = User::all();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Almacenar un nuevo usuario.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'rol'      => 'required|in:recepcionista,medico,admin',
        ]);

        User::create([
            'name'     => $data['nombre'] . ' ' . $data['apellido'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'rol'      => $data['rol'],
        ]);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Actualizar datos de un usuario existente.
     */
    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $usuario->id,
            'rol'      => 'required|in:recepcionista,medico,admin',
        ]);

        $usuario->update([
            'name'  => $data['nombre'] . ' ' . $data['apellido'],
            'email' => $data['email'],
            'rol'   => $data['rol'],
        ]);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(User $usuario)
    {
        $usuario->delete();

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario eliminado correctamente.');
    }
}
