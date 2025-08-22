<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        $rules = [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|max:100|unique:users,email',
            // aquí cambié min:8 -> min:4
            'password' => 'required|string|min:4',
            'rol'      => 'required|in:recepcionista,medico,admin',
        ];

        $messages = [
            'email.email'  => 'El correo electrónico debe tener un formato válido.',
            'email.max'    => 'El correo electrónico no puede exceder 100 caracteres.',
            'email.unique' => 'El correo electrónico ya está en uso. Elige otro.',
            // actualicé el mensaje a 4 caracteres
            'password.min' => 'La contraseña debe tener al menos 4 caracteres.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Redirigimos con errores y con todos los inputs (incluye password)
            return redirect()->route('usuarios.index')
                             ->withErrors($validator)
                             ->withInput($request->all());
        }

        $data = $validator->validated();

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
        $rules = [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|max:100|unique:users,email,' . $usuario->id,
            'rol'      => 'required|in:recepcionista,medico,admin',
        ];

        $messages = [
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.max'   => 'El correo electrónico no puede exceder 100 caracteres.',
            'email.unique'=> 'El correo electrónico ya está en uso. Elige otro.',
        ];

        $data = $request->validate($rules, $messages);

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
