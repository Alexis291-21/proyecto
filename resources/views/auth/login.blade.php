@extends('layouts.app')

@section('title')
    Login
@endsection

@section('stylesheet')
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <style>
        /* Fuente principal */
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Fondo full-screen */
        .bg-login {
            background-image: url('/images/fondo-nieve.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }

        /* Capa oscura encima del fondo */
        .bg-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
        }

        /* Panel de login */
        .login-panel {
            position: relative;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
        }

        /* Títulos */
        .login-panel h3 {
            color: #fff;
            letter-spacing: 0.5px;
        }

        /* Labels */
        .login-panel label {
            color: #e5e5e5;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Inputs */
        .login-panel input {
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transition: all 0.3s ease;
        }

        .login-panel input:focus {
            border-color: #60a5fa;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.4);
        }

        .login-panel input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Botón submit */
        .login-panel button[type="submit"] {
            background: linear-gradient(135deg, #2563eb, #1e3a8a);
            color: #fff;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .login-panel button[type="submit"]:hover {
            background: linear-gradient(135deg, #1d4ed8, #172554);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.4);
        }

        /* Botón del ojo (sin animación extra) */
        .password-toggle-button {
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .password-toggle-button i {
            color: #000; /* negro fijo */
        }

        /* Alertas */
        .alert {
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.9s ease-out;
        }
    </style>
@endsection

@section('body')
<div class="min-h-screen flex items-center justify-center bg-login relative">
    <div class="w-full max-w-md rounded-2xl px-8 py-10 login-panel animate-fadeIn">
        <h3 class="text-3xl font-bold text-center mb-8">Iniciar Sesión</h3>

        @if(session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show"
                class="alert alert-success text-center mb-4">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show"
                class="alert alert-error text-center mb-4">
                {{ session('error') }}
            </div>
        @elseif($errors->any())
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show"
                class="alert alert-error text-center mb-4">
                Las credenciales ingresadas no son correctas.
            </div>
        @endif

        <form action="{{ route('auth.login') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Correo -->
            <div>
                <label for="email" class="block mb-1">Correo Electrónico</label>
                <input type="email" name="email" id="email" required
                    placeholder="usuario@ejemplo.com"
                    class="w-full px-4 py-3 rounded-lg focus:outline-none" />
            </div>

            <!-- Contraseña -->
            <div x-data="{ show: false, active: false, input: '' }">
                <label for="password" class="block mb-1">Contraseña</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password" id="password" x-model="input" required
                        placeholder="Ingrese su contraseña"
                        class="w-full px-4 py-3 rounded-lg pr-12 focus:outline-none"
                        @focus="active = true" @blur="if (!input) active = false" />
                    <button x-show="active || input" x-transition type="button"
                        @click="show = !show"
                        class="password-toggle-button absolute inset-y-0 right-0 px-4 flex items-center"
                        tabindex="-1" aria-label="Mostrar/ocultar contraseña">
                        <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Botón -->
            <button type="submit"
                class="w-full py-3 rounded-lg font-semibold transition duration-300 shadow-md">
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
