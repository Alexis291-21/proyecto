<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Solo usuarios autenticados.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar la vista de perfil.
     */
    public function show()
    {
        return view('profile.index', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Actualizar datos de perfil (nombre, email, teléfono).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate(
            [
                'name'  => ['sometimes', 'required', 'string', 'max:255'],
                'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'phone' => ['sometimes', 'nullable', 'regex:/^9[0-9]{8}$/'],
            ],
            [
                'phone.regex' => 'El teléfono debe comenzar con 9 y tener exactamente 9 dígitos numéricos.',
            ]
        );

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone'];
        }

        $user->save();

        return redirect()
            ->route('perfil.show')
            ->with('success', '¡Perfil actualizado correctamente!');
    }

    /**
     * Actualizar contraseña.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'current_password'      => ['required', 'current_password'],
                'password'              => ['required', 'string', 'min:12', 'confirmed'],
                'password_confirmation' => ['required'],
            ],
            [
                'current_password.current_password' => 'La contraseña actual es incorrecta.',
                'password.min'                      => 'La nueva contraseña debe tener al menos 12 caracteres.',
                'password.confirmed'                => 'Las credenciales no coinciden.',
                'password_confirmation.required'    => 'Debes confirmar la contraseña.',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()
            ->route('perfil.show')
            ->with('success', '¡Contraseña actualizada correctamente!');
    }

    /**
     * Actualizar solo el avatar.
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        // 1) Validación
        $request->validate(
            [
                'avatar' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
            ],
            [
                'avatar.required' => 'Debes seleccionar una imagen.',
                'avatar.image'    => 'El archivo debe ser una imagen.',
                'avatar.mimes'    => 'El avatar debe ser jpg, jpeg o png.',
                'avatar.max'      => 'El avatar no puede superar los 2 MB.',
            ]
        );

        // 2) Borrar avatar anterior si existe
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // 3) Subir y guardar ruta
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return redirect()
            ->route('perfil.show')
            ->with('success', '¡Avatar actualizado correctamente!');
    }

    /**
     * Retirar (eliminar) el avatar actual.
     */
    public function removeAvatar(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = null;
        $user->save();

        return redirect()
            ->route('perfil.show')
            ->with('success', '¡Avatar retirado correctamente!');
    }
}
