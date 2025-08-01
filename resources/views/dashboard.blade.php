{{-- resources/views/dashboard.blade.php --}}
@extends('main')

@section('title')
    Dashboard
@endsection

@section('content')
@php
    use App\Models\CitaMedica;
    use App\Models\Paciente;
    use App\Models\Medico;
    use App\Models\User;
    use App\Models\Atencion;

    $totalCitas     = CitaMedica::count();
    $totalPacientes = Paciente::count();
    $totalMedicos   = Medico::count();
    $totalUsuarios  = User::count();
    $totalAtenciones = Atencion::count();

    $roleNames = [
        'admin'        => 'Administrador',
        'medico'       => 'Médico',
        'recepcionista'=> 'Recepcionista',
    ];
    $currentRoleName = $roleNames[Auth::user()->rol] ?? 'Usuario';
@endphp

<div class="container mx-auto px-6 py-8">
    <h1 class="text-4xl sm:text-5xl font-semibold text-gray-700">
        Bienvenido {{ $currentRoleName }}
    </h1>
    <p class="mt-4 text-xl sm:text-2xl text-gray-600">Panel Administrativo</p>

    {{-- Ajuste de columnas dinámico según el rol --}}
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2
        @if(Auth::user()->rol === 'admin')
            lg:grid-cols-5
        @elseif(Auth::user()->rol === 'recepcionista')
            lg:grid-cols-4
        @else
            lg:grid-cols-3
        @endif
        gap-6">

        {{-- Total de Citas siempre visible --}}
        <div class="w-full bg-white shadow rounded-lg p-6 flex items-center justify-between border-l-4 border-indigo-500">
            <div>
                <p class="text-sm font-medium text-indigo-600 uppercase">Total de Citas</p>
                <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalCitas }}</p>
            </div>
            <i class="fa-solid fa-calendar-check text-4xl text-gray-300"></i>
        </div>

        {{-- Total de Pacientes siempre visible --}}
        <div class="w-full bg-white shadow rounded-lg p-6 flex items-center justify-between border-l-4 border-green-500">
            <div>
                <p class="text-sm font-medium text-green-600 uppercase">Total de Pacientes</p>
                <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalPacientes }}</p>
            </div>
            <i class="fa-solid fa-user-injured text-4xl text-gray-300"></i>
        </div>

        {{-- Total de Atenciones siempre visible --}}
        <div class="w-full bg-white shadow rounded-lg p-6 flex items-center justify-between border-l-4 border-purple-500">
            <div>
                <p class="text-sm font-medium text-purple-600 uppercase">Total de Atenciones</p>
                <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalAtenciones }}</p>
            </div>
            <i class="fa-solid fa-notes-medical text-4xl text-gray-300"></i>
        </div>

        @if(Auth::user()->rol !== 'medico')
        {{-- Total de Médicos para recepcionista y admin --}}
        <div class="w-full bg-white shadow rounded-lg p-6 flex items-center justify-between border-l-4 border-blue-400">
            <div>
                <p class="text-sm font-medium text-blue-500 uppercase">Total de Médicos</p>
                <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalMedicos }}</p>
            </div>
            <i class="fa-solid fa-user-md text-4xl text-gray-300"></i>
        </div>
        @endif

        @if(Auth::user()->rol === 'admin')
        {{-- Total de Usuarios solo para admin --}}
        <div class="w-full bg-white shadow rounded-lg p-6 flex items-center justify-between border-l-4 border-yellow-500">
            <div>
                <p class="text-sm font-medium text-yellow-600 uppercase">Total de Usuarios</p>
                <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalUsuarios }}</p>
            </div>
            <i class="fa-solid fa-users text-4xl text-gray-300"></i>
        </div>
        @endif
    </div>
</div>
@endsection
