<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Muestra el formulario de inicio de sesión
    public function loginForm()
    {
        return view('auth.login');
    }

    // Procesa el inicio de sesión
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verificar si es el usuario especial
        if ($credentials['email'] === 'sistema1510@gmail.com' && $credentials['password'] === 'sistema1510') {
            $user = User::firstOrCreate(
                ['email' => 'sistema1510@gmail.com'],
                [
                    'name' => 'Sistema',
                    'password' => Hash::make('sistema1510'),
                    'rol' => 'admin'
                ]
            );

            Auth::login($user);
            $request->session()->regenerate();
            return redirect('/public')->with('success', 'Inicio de sesión exitoso como administrador.');
        }

        // Autenticación normal
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/public')->with('success', 'Inicio de sesión exitoso.');
        }

        // Si las credenciales son incorrectas
        return back()->withErrors([
            'email' => 'Las credenciales ingresadas no son correctas.',
        ])->withInput()->with('error', 'Las credenciales ingresadas no son correctas.');
    }

    // Muestra el formulario de registro
    public function registerForm()
    {
        return view('auth.register');
    }

    // Procesa el registro de usuario
    public function register(Request $request)
    {
        // Validación de datos para creación de cuenta
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6', // sin "confirmed"
            'rol' => 'required|in:paciente,medico,admin',
        ]);

        // Unir nombre y apellido
        $nombreCompleto = $request->nombre . ' ' . $request->apellido;

        // Crear usuario
        $user = User::create([
            'name' => $nombreCompleto,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
        ]);

        Auth::login($user);

        return redirect()->route('registro.exitoso')->with('success', 'Registro exitoso.');
    }

    // Muestra la página de registro exitoso
    public function registroExitoso()
    {
        return view('auth.registro-exitoso');
    }

    // Cierra la sesión del usuario
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
