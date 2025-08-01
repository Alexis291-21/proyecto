@extends('layouts.app')

@section('title')
    @yield('title')
@endsection

@section('stylesheet')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Evitar FOUC antes de Alpine.js */
        [x-cloak] { display: none !important; }

        .sidebar-bg {
            /* Fondo sólido igual al de la imagen: rgb(62, 105, 218) */
            background-color: #3E69DA;
        }

        .sidebar-hidden .sidebar-bg { display: none; }
        .sidebar-collapsed .sidebar-bg { width: 5rem; }

        /* Transiciones suaves para el sidebar */
        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
    @yield('stylesheet')
@endsection

@section('javascripts')
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('javascripts')
@endsection

@section('body')
<div x-data="{
        sidebarOpen: JSON.parse(localStorage.getItem('sidebar-open') || 'true'),
        sidebarCollapsed: JSON.parse(localStorage.getItem('sidebar-collapsed') || 'false'),
        settingsModal: false,
        logoutModal: false
    }"
    x-init="
        $watch('sidebarOpen', v => localStorage.setItem('sidebar-open', v));
        $watch('sidebarCollapsed', v => localStorage.setItem('sidebar-collapsed', v));
    "
    x-cloak
    class="flex min-h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside
        x-cloak
        x-show="sidebarOpen"
        class="fixed inset-y-0 left-0 sidebar-bg text-white p-5 flex flex-col overflow-y-auto transition-all duration-300"
        :class="sidebarCollapsed ? 'w-20' : 'w-64'">
        <div class="mb-8" :class="sidebarCollapsed ? 'flex justify-center' : 'pl-4'">
            <h2 class="text-2xl font-bold" x-show="!sidebarCollapsed">Sistema Médico</h2>
            <i class="fa-solid fa-hospital text-xl" x-show="sidebarCollapsed"></i>
        </div>
        <ul class="space-y-2 flex-1">
            @php $r = Auth::user()->rol; @endphp

            @if(in_array($r, ['recepcionista','medico','admin']))
            <li>
                <a href="{{ url('/public') }}"
                   class="flex items-center px-4 py-2 rounded-r {{ Request::is('public') ? 'bg-[#4E2C8A] border-l-4 border-white font-semibold' : 'hover:bg-[#4E2C8A]/80' }}">
                    <i class="fa-solid fa-house mr-3"></i>
                    <span x-show="!sidebarCollapsed">Home</span>
                </a>
            </li>
            @endif

            @if(in_array($r, ['recepcionista','admin']))
            <li>
                <a href="{{ url('/medicos') }}"
                   class="flex items-center px-4 py-2 rounded-r {{ Request::is('medicos*') ? 'bg-[#4E2C8A] border-l-4 border-white font-semibold' : 'hover:bg-[#4E2C8A]/80' }}">
                    <i class="fa-solid fa-user-md mr-3"></i>
                    <span x-show="!sidebarCollapsed">Médicos</span>
                </a>
            </li>
            @endif

            @if(in_array($r, ['recepcionista','medico','admin']))
            <li>
                <a href="{{ url('/pacientes') }}"
                   class="flex items-center px-4 py-2 rounded-r {{ Request::is('pacientes*') ? 'bg-[#4E2C8A] border-l-4 border-white font-semibold' : 'hover:bg-[#4E2C8A]/80' }}">
                    <i class="fa-solid fa-user-injured mr-3"></i>
                    <span x-show="!sidebarCollapsed">Pacientes</span>
                </a>
            </li>
            @endif

            @if(in_array($r, ['recepcionista','medico','admin']))
            <li>
                <a href="{{ url('/citas') }}"
                   class="flex items-center px-4 py-2 rounded-r {{ Request::is('citas*') ? 'bg-[#4E2C8A] border-l-4 border-white font-semibold' : 'hover:bg-[#4E2C8A]/80' }}">
                    <i class="fa-solid fa-calendar-check mr-3"></i>
                    <span x-show="!sidebarCollapsed">Citas</span>
                </a>
            </li>
            @endif

            @if(in_array($r, ['recepcionista','medico','admin']))
            <li>
                <a href="{{ url('/calendario') }}"
                   class="flex items-center px-4 py-2 rounded-r {{ Request::is('calendario') ? 'bg-[#4E2C8A] border-l-4 border-white font-semibold' : 'hover:bg-[#4E2C8A]/80' }}">
                    <i class="fa-solid fa-calendar-days mr-3"></i>
                    <span x-show="!sidebarCollapsed">Calendario</span>
                </a>
            </li>
            @endif

            @if(in_array($r, ['recepcionista','medico','admin']))
            <li>
                <a href="{{ url('/atenciones') }}"
                   class="flex items-center px-4 py-2 rounded-r {{ Request::is('atenciones*') ? 'bg-[#4E2C8A] border-l-4 border-white font-semibold' : 'hover:bg-[#4E2C8A]/80' }}">
                    <i class="fa-solid fa-notes-medical text-lg mr-3"></i>
                    <span x-show="!sidebarCollapsed">Atenciones</span>
                </a>
            </li>
            @endif

            @if(in_array($r, ['recepcionista','admin']))
            <li>
                <a href="{{ url('/reportes') }}"
                   class="flex items-center px-4 py-2 rounded-r {{ Request::is('reportes*') ? 'bg-[#4E2C8A] border-l-4 border-white font-semibold' : 'hover:bg-[#4E2C8A]/80' }}">
                    <i class="fa-solid fa-chart-bar mr-3"></i>
                    <span x-show="!sidebarCollapsed">Reportes</span>
                </a>
            </li>
            @endif

            @if(in_array($r, ['recepcionista','medico','admin']))
            <li>
                <a href="{{ url('/horarios') }}"
                   class="flex items-center px-4 py-2 rounded-r {{ Request::is('horarios*') ? 'bg-[#4E2C8A] border-l-4 border-white font-semibold' : 'hover:bg-[#4E2C8A]/80' }}">
                    <i class="fa-solid fa-clock mr-3"></i>
                    <span x-show="!sidebarCollapsed">Horarios</span>
                </a>
            </li>
            @endif

            @if($r === 'admin')
            <li>
                <a href="{{ url('/usuarios') }}"
                   class="flex items-center px-4 py-2 rounded-r {{ Request::is('usuarios*') ? 'bg-[#4E2C8A] border-l-4 border-white font-semibold' : 'hover:bg-[#4E2C8A]/80' }}">
                    <i class="fa-solid fa-users mr-3"></i>
                    <span x-show="!sidebarCollapsed">Usuarios</span>
                </a>
            </li>
            @endif

            {{-- Botón colapsar/expandir justo debajo de Usuarios --}}
            <li class="flex-shrink-0 flex justify-center mt-auto">
                <button
                    @click="sidebarCollapsed = !sidebarCollapsed"
                    class="w-10 h-10 rounded-full bg-white bg-opacity-20 hover:bg-opacity-30 flex items-center justify-center focus:outline-none"
                >
                    <span class="text-2xl font-bold select-none"
                          x-text="sidebarCollapsed ? '>' : '<'"></span>
                </button>
            </li>
        </ul>
    </aside>

    {{-- Contenedor principal --}}
    <div class="flex-1 flex flex-col pt-5"
         :class="sidebarOpen
             ? (sidebarCollapsed ? 'ml-20' : 'ml-64')
             : 'ml-0'">
        {{-- HEADER --}}
        <header
        class="fixed inset-x-0 top-0 z-30 bg-white border-b shadow-sm grid grid-cols-3 items-center px-6 py-3"
        :class="sidebarOpen
            ? (sidebarCollapsed ? 'left-20' : 'left-64')
            : 'left-0'">
        {{-- 1. Columna izquierda: hamburguesa --}}
        <div class="flex items-center">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="text-gray-600 hover:text-gray-800 focus:outline-none">
            <i class="fa-solid fa-bars fa-lg"></i>
            </button>
        </div>

        {{-- 2. Columna central: fecha y hora --}}
        <div
            x-data="{ date: '', time: '' }"
            x-init="(() => {
                const upd = () => {
                    const now = new Date();
                    date = now.toLocaleDateString('es-PE', {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    });
                    time = now.toLocaleTimeString('es-PE', {
                        hour: '2-digit', minute: '2-digit', second: '2-digit'
                    });
                };
                upd(); setInterval(upd, 1000);
            })()"
            class="flex flex-col items-center justify-center text-gray-700 font-semibold text-base capitalize">
            <span x-text="date"></span>
            <span x-text="time"></span>
        </div>

        {{-- 3. Columna derecha: usuario --}}
        <div class="flex justify-end items-center space-x-4">
            <span class="text-gray-700 font-medium capitalize">
            @switch(Auth::user()->rol)
                @case('admin') Administrador @break
                @case('medico') Médico @break
                @case('recepcionista') Recepcionista @break
            @endswitch
            </span>

            <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" class="flex items-center space-x-1 focus:outline-none">
                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar"
                    class="w-8 h-8 rounded-full border">
                <i class="fa-solid fa-caret-down text-gray-600"></i>
            </button>
            <div x-show="open" x-cloak
                class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-lg overflow-hidden z-40">
                <a href="{{ route('profile.show') }}"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                <i class="fa-regular fa-user mr-2"></i> Mi Perfil
                </a>

                @if(Auth::user()->rol === 'admin')
                <button @click="settingsModal = true; open = false"
                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                <i class="fa-solid fa-cog mr-2"></i> Configuración
                </button>
                @endif

                <form @submit.prevent="logoutModal = true" class="border-t">
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid fa-right-from-bracket fa-rotate-180 mr-2"></i> Cerrar sesión
                </button>
                </form>
            </div>
            </div>
        </div>
        </header>

        <!-- Modal Confirmación Cerrar Sesión -->
        <div
          x-show="logoutModal" x-cloak
          class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
        >
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
              <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                <h2 class="text-white text-xl font-semibold">Confirmar Cierre de Sesión</h2>
                <button @click="logoutModal = false" class="text-blue-200 hover:text-white">
                  <i class="fa-solid fa-xmark fa-lg"></i>
                </button>
              </div>
              <div class="p-6 text-center space-y-4">
                <i class="fa-solid fa-triangle-exclamation text-4xl text-yellow-500"></i>
                <p class="text-gray-700">
                  ¿Estás seguro de que deseas cerrar tu sesión?
                  Selecciona <strong>"Logout"</strong> para continuar.
                </p>
              </div>
              <div class="px-6 pb-6 grid grid-cols-2 gap-4">
                <button
                  @click="logoutModal = false"
                  class="w-full py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition"
                >
                  Cancelar
                </button>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button
                  type="submit"
                  class="w-full py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition"
                  >
                  Logout
                  </button>
                </form>
              </div>
          </div>
        </div>

        {{-- Modal de Configuración (solo admin) --}}
        @php $s = \App\Models\Setting::first() ?? new \App\Models\Setting; @endphp
        @if(Auth::user()->rol === 'admin')
        <div
            x-show="settingsModal"
            x-cloak
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50"
        >
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl p-8 relative">
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-blue-600 flex items-center">
                        <i class="fa-solid fa-cogs mr-2"></i> Configuración
                    </h2>
                    <button
                        @click="settingsModal = false"
                        class="text-gray-400 hover:text-gray-600 transition"
                    >
                        <i class="fa-solid fa-xmark fa-lg"></i>
                    </button>
                </div>

                <p class="text-gray-500 mb-6 text-sm">
                    Ajusta aquí los datos principales de tu empresa.
                </p>

                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @php
                        $fields = [
                            ['name'=>'nombre_empresa','label'=>'Nombre de la empresa','icon'=>'fa-building','type'=>'text','placeholder'=>'Mi Clínica S.A.'],
                            ['name'=>'codigo_postal','label'=>'Código Postal','icon'=>'fa-envelope','type'=>'text','placeholder'=>'12345','attrs'=>'maxlength="5" pattern="\d{5}" oninput="this.value=this.value.replace(/[^0-9]/g,\'\').slice(0,5)"'],
                            ['name'=>'telefono','label'=>'Teléfono','icon'=>'fa-phone','type'=>'text','placeholder'=>'912345678','attrs'=>'maxlength="9" pattern="9\d{8}" oninput="let v=this.value.replace(/[^0-9]/g,\'\'); if(v.length&&v[0]!=="9")v=""; this.value=v.slice(0,9);"'],
                            ['name'=>'ruc','label'=>'RUC','icon'=>'fa-id-card','type'=>'text','placeholder'=>'12345678901','attrs'=>'maxlength="11" pattern="\d{11}" oninput="this.value=this.value.replace(/[^0-9]/g,\'\').slice(0,11)"'],
                            ['name'=>'responsable','label'=>'Responsable / Director','icon'=>'fa-user-tie','type'=>'text','placeholder'=>'Dr. Juan Pérez']
                        ];
                    @endphp

                    @foreach($fields as $f)
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $f['label'] }}</label>
                        <div class="flex items-center bg-gray-50 border border-gray-300 rounded-xl focus-within:ring-2 focus-within:ring-blue-500 transition">
                            <span class="px-3 text-gray-400">
                                <i class="fa-solid {{ $f['icon'] }}"></i>
                            </span>
                            <input
                                name="{{ $f['name'] }}"
                                type="{{ $f['type'] }}"
                                {!! $f['attrs'] ?? '' !!}
                                class="flex-1 px-3 py-2 bg-transparent focus:outline-none text-gray-800 placeholder-gray-400"
                                placeholder="{{ $f['placeholder'] }}"
                                value="{{ old($f['name'], $s->{$f['name']} ?? '') }}"
                            />
                        </div>
                    </div>
                    @endforeach

                    {{-- Logo de la empresa (PNG, JPG) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Logo de la empresa (PNG, JPG)
                        </label>

                        {{-- Contenedor de previsualización --}}
                        <div class="w-24 h-24 rounded-xl overflow-hidden mb-2 relative">
                            <img
                                id="logoPreview"
                                src="{{ $s->logo_url ?? '' }}"
                                class="object-contain w-full h-full"
                                style="{{ $s->logo_url ? 'display:block;' : 'display:none;' }}"
                            />
                            <div
                                id="logoPlaceholder"
                                class="absolute inset-0 flex items-center justify-center bg-gray-100 text-gray-400"
                                style="{{ $s->logo_url ? 'display:none;' : 'display:flex;' }}"
                            >
                                Sin logo
                            </div>
                        </div>

                        {{-- Input de archivo --}}
                        <input
                            id="logoInput"
                            name="logo"
                            type="file"
                            accept="image/png,image/jpeg"
                            class="block mt-2 text-sm text-gray-500
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100 transition"
                        />
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-between mt-6">
                        <button
                            type="button"
                            @click="settingsModal = false"
                            class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition font-medium"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition"
                        >
                            Guardar cambios
                        </button>
                    </div>
                </form>

                <script>
                // --- Helpers para cookies ---
                function setCookie(name, value, days = 365) {
                    const expires = new Date(Date.now() + days * 864e5).toUTCString();
                    document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; expires=' + expires;
                }

                function getCookie(name) {
                    return document.cookie.split('; ').reduce((r, v) => {
                        const [key, val] = v.split('=');
                        return key === name ? decodeURIComponent(val) : r;
                    }, '');
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const openCookie      = getCookie('sidebar_open');
                    const collapsedCookie = getCookie('sidebar_collapsed');

                    if (openCookie !== '') {
                        localStorage.setItem('sidebar-open', openCookie === 'true');
                    }
                    if (collapsedCookie !== '') {
                        localStorage.setItem('sidebar-collapsed', collapsedCookie === 'true');
                    }
                });

                document.getElementById('logoInput').addEventListener('change', function(e){
                    const file = e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        const img   = document.getElementById('logoPreview');
                        const place = document.getElementById('logoPlaceholder');

                        img.src = ev.target.result;
                        img.style.display = 'block';
                        place.style.display = 'none';
                    }
                    reader.readAsDataURL(file);
                });

                function setSidebarState(open, collapsed) {
                    localStorage.setItem('sidebar-open',  open);
                    localStorage.setItem('sidebar-collapsed', collapsed);
                    setCookie('sidebar_open',      open,      365);
                    setCookie('sidebar_collapsed', collapsed, 365);
                }

                document.addEventListener('alpine:init', () => {
                    Alpine.data('sidebarState', () => ({
                        sidebarOpen:      JSON.parse(localStorage.getItem('sidebar-open')     || 'true'),
                        sidebarCollapsed: JSON.parse(localStorage.getItem('sidebar-collapsed')|| 'false'),
                        toggleSidebar() {
                            this.sidebarOpen = !this.sidebarOpen;
                            setSidebarState(this.sidebarOpen, this.sidebarCollapsed);
                        },
                        toggleCollapse() {
                            this.sidebarCollapsed = !this.sidebarCollapsed;
                            setSidebarState(this.sidebarOpen, this.sidebarCollapsed);
                        }
                    }));
                });
                </script>
            </div>
        </div>
        @endif

        <main class="pt-20 p-6 flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>
@endsection
