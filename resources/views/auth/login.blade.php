@extends('layouts.app')

@section('title', 'Login')

@section('stylesheet')
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <style>
        /* Carga la fuente Poppins */
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Fondo full-screen y centrado */
        .bg-login {
            background-image: url('/images/fondo-nieve.jpg');
            background-size: cover;
            background-position: center;
        }

        /* Panel de login semitransparente */
        .login-panel {
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Texto y botones en blanco */
        .login-panel h3,
        .login-panel label,
        .login-panel input,
        .login-panel button {
            color: #fff;
        }

        /* Inputs con bordes y fondo translúcido */
        .login-panel input {
            border: 1px solid rgba(255, 255, 255, 0.7);
            background-color: rgba(255, 255, 255, 0.1);
        }

        .login-panel input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Botón submit */
        .login-panel button[type="submit"] {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .login-panel button[type="submit"]:hover {
            background-color: rgba(0, 0, 0, 1);
        }

        /* Botón del ojo sin fondo ni borde */
        .password-toggle-button {
            background: transparent;
            border: none;
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease-out;
        }
    </style>
@endsection

@section('body')
<div class="min-h-screen flex items-center justify-center bg-login">
    <div class="w-full max-w-md rounded-2xl shadow-lg px-8 py-10 login-panel animate-fadeIn">
        <h3 class="text-2xl font-bold text-center mb-6">Iniciar Sesión</h3>

        @if(session('success'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 8000)"
                x-show="show"
                class="bg-green-600 bg-opacity-50 px-4 py-3 rounded mb-4 text-sm text-center"
            >
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 8000)"
                x-show="show"
                class="bg-red-600 bg-opacity-50 px-4 py-3 rounded mb-4 text-sm text-center"
            >
                {{ session('error') }}
            </div>
        @elseif($errors->any())
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 8000)"
                x-show="show"
                class="bg-red-600 bg-opacity-50 px-4 py-3 rounded mb-4 text-sm text-center"
            >
                Las credenciales ingresadas no son correctas.
            </div>
        @endif

        <form action="{{ route('auth.login') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Correo -->
            <div>
                <label for="email" class="block font-semibold mb-1">Correo Electrónico</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    required
                    placeholder="usuario@ejemplo.com"
                    class="w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white"
                />
            </div>

            <!-- Contraseña con ícono que aparece al enfocar o escribir -->
            <div x-data="{ show: false, active: false, input: '' }">
                <label for="password" class="block font-semibold mb-1">Contraseña</label>
                <div class="relative">
                    <input
                        :type="show ? 'text' : 'password'"
                        name="password"
                        id="password"
                        x-model="input"
                        required
                        placeholder="ingrese su contraseña"
                        class="w-full px-4 py-3 rounded-lg pr-10 focus:outline-none focus:ring-2 focus:ring-white"
                        @focus="active = true"
                        @blur="if (!input) active = false"
                    />
                    <button
                        x-show="active || input"
                        x-transition
                        type="button"
                        @click="show = !show"
                        class="password-toggle-button absolute inset-y-0 right-0 px-4 flex items-center"
                        tabindex="-1"
                    >
                        <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-700"></i>
                    </button>
                </div>
            </div>

            <!-- Botón de ingreso -->
            <button
                type="submit"
                class="w-full py-3 rounded-lg font-semibold transition duration-300"
            >
                Ingresar
            </button>
        </form>
    </div>
</div>
@endsection

@section('javascripts')
    <!-- Tailwind y AlpineJS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
