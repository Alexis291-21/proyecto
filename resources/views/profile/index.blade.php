@extends('main')

@section('title', 'Mi Perfil')

@section('content')
<div
  x-data="{
    showImage: false,
    showAvatarModal: false,
    showNameModal: false,
    showEmailModal: false,
    showPhoneModal: false,
    showCurrent: false,
    showNew: false,
    showConfirm: false,
    showDeleteConfirm: false,
    showAvatarButtons: false
  }"
  class="bg-gray-100 min-h-screen"
>

  {{-- COVER MEJORADO --}}
  <div class="relative h-60 md:h-72 lg:h-96 overflow-hidden rounded-b-2xl shadow-inner">
    <div
      class="absolute inset-0 bg-cover bg-center"
      style="background-image: url('{{ asset('images/cover-default.jpg') }}');"
    ></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black opacity-60"></div>
  </div>

  <div class="max-w-5xl mx-auto px-4 -mt-20">

    {{-- AVATAR CARD --}}
    <div class="flex justify-center">
      <div class="relative w-40 h-40 rounded-full bg-white p-1 shadow-lg group">

        {{-- Botones sobre el avatar SOLO cuando selecciona archivo --}}
        <div
          x-show="showAvatarButtons"
          x-cloak
          class="absolute -top-24 left-1/2 -translate-x-1/2 flex flex-col items-center z-10"
        >
          <div class="relative">
            <div
              class="flex items-center space-x-4 bg-white rounded-2xl p-4 shadow-lg border border-gray-200
                     transform transition duration-200 hover:shadow-2xl"
            >
              <button
                type="button"
                @click="showAvatarButtons = false"
                class="flex items-center px-6 py-2 bg-gray-100 text-gray-700 text-base font-medium
                       rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-1
                       focus:ring-gray-300 transition duration-150"
              >
                <i class="fas fa-times mr-2"></i>
                Cancelar
              </button>
              <button
                type="submit"
                form="avatarForm"
                class="flex items-center px-6 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600
                       text-white text-base font-semibold rounded-lg shadow-md hover:from-indigo-600
                       hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-1
                       focus:ring-indigo-400 transition duration-150 whitespace-nowrap"
              >
                <i class="fas fa-save mr-2"></i>
                Guardar como foto de perfil
              </button>
            </div>

            {{-- Flecha debajo del contenedor --}}
            <div
              class="absolute w-4 h-4 bg-white border-l border-t border-gray-200
                     transform rotate-45 shadow-full -bottom-2 left-1/2 -translate-x-1/2"
            ></div>
          </div>
        </div>

        @if (Auth::user()->avatar)
          <img
            @click="showImage = true"
            src="{{ Auth::user()->avatar_url }}"
            alt="Avatar"
            class="w-full h-full rounded-full object-cover cursor-pointer"
          >
        @else
          <img
            src="{{ Auth::user()->avatar_url }}"
            alt="Avatar"
            class="w-full h-full rounded-full object-cover"
          >
        @endif

        {{-- botón editar --}}
        <button
          type="button"
          @click="showAvatarModal = true"
          class="absolute bottom-1 inset-x-1 h-1/2
                 flex flex-col items-center justify-center
                 rounded-b-full
                 bg-black bg-opacity-25
                 shadow-inner
                 opacity-0 group-hover:opacity-100
                 hover:bg-opacity-35 hover:shadow-lg
                 transition-all duration-200
                 cursor-pointer"
          title="Cambiar foto de perfil"
        >
          <svg xmlns="http://www.w3.org/2000/svg"
               class="w-8 h-8 text-white mb-1"
               fill="currentColor"
               viewBox="0 0 24 24">
            <path d="M20 5h-3.586l-1.707-1.707A.996.996 0 0014.414 3H9.586a.996.996 0 00-.707.293L7.172 5H4
                     c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2h16
                     c1.103 0 2-.897 2-2V7c0-1.103-.897-2-2-2zm-8 13
                     c-2.761 0-5-2.239-5-5s2.239-5 5-5
                     5 2.239 5 5-2.239 5-5 5zm0-8
                     c-1.654 0-3 1.346-3 3s1.346 3 3 3
                     3-1.346 3-3-1.346-3-3-3z"/>
          </svg>
          <span class="text-xs text-white font-medium">Próximamente</span>
        </button>

      </div>
    </div>

    {{-- NOMBRE + EMAIL + TELÉFONO --}}
    <div class="text-center mt-4 mb-8">
      <h1 class="text-3xl font-bold text-gray-900">{{ Auth::user()->name }}</h1>
      <p class="inline-block mt-2 mb-1 px-3 py-1 bg-indigo-600 text-white rounded-full text-sm">
        {{ Auth::user()->email }}
      </p>
      <p class="text-gray-600 mt-1">
        {{ Auth::user()->phone }}
      </p>
    </div>

    {{-- Sección INFO + CONTRASEÑA --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">

      {{-- Tarjeta: Datos del perfil --}}
      <div class="bg-white rounded-2xl shadow-2xl transition-shadow hover:shadow-3xl overflow-hidden border border-gray-200">
        <div class="flex items-center px-6 py-4 bg-gradient-to-r from-indigo-600 to-blue-500">
          <i class="fas fa-user-circle text-white text-2xl mr-3"></i>
          <h2 class="text-white text-lg font-semibold">Datos del perfil</h2>
        </div>
        <div class="p-6">
          <ul class="space-y-4">
            <li class="flex justify-between items-center">
              <span class="font-medium text-gray-700">Nombre completo</span>
              <button
                @click="showNameModal = true"
                class="text-indigo-500 hover:text-indigo-700 transition"
                title="Editar nombre"
              >
                <i class="fas fa-edit"></i>
              </button>
            </li>
            <li class="flex justify-between items-center">
              <span class="font-medium text-gray-700">Correo electrónico</span>
              <button
                @click="showEmailModal = true"
                class="text-green-500 hover:text-green-700 transition"
                title="Editar correo"
              >
                <i class="fas fa-edit"></i>
              </button>
            </li>
            <li class="flex justify-between items-center">
              <span class="font-medium text-gray-700">Teléfono de contacto</span>
              <button
                @click="showPhoneModal = true"
                class="text-indigo-500 hover:text-indigo-700 transition"
                title="Editar teléfono"
              >
                <i class="fas fa-edit"></i>
              </button>
            </li>
          </ul>
        </div>
      </div>

      {{-- Tarjeta: Cambiar contraseña --}}
      <div class="bg-white rounded-2xl shadow-2xl transition-shadow hover:shadow-3xl overflow-hidden border border-gray-200">
        <div class="flex items-center px-6 py-4 bg-gradient-to-r from-indigo-600 to-blue-500">
          <i class="fas fa-lock text-white text-2xl mr-3"></i>
          <h2 class="text-white text-lg font-semibold">Cambiar contraseña</h2>
        </div>
        <div class="px-6 py-6">
          <form action="{{ route('perfil.password') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Contraseña actual --}}
            <div>
              <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                Contraseña actual
              </label>
              <div class="relative">
                <input
                  :type="showCurrent ? 'text' : 'password'"
                  name="current_password"
                  id="current_password"
                  value="{{ old('current_password') }}"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                  required
                >
                <button
                  type="button"
                  @click="showCurrent = !showCurrent"
                  class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-700 transition-opacity opacity-0 focus:opacity-100"
                >
                  <i :class="showCurrent ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              @error('current_password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Nueva contraseña --}}
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Nueva contraseña
              </label>
              <div class="relative">
                <input
                  :type="showNew ? 'text' : 'password'"
                  name="password"
                  id="password"
                  value="{{ old('password') }}"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                  required
                >
                <button
                  type="button"
                  @click="showNew = !showNew"
                  class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-700 transition-opacity opacity-0 focus:opacity-100"
                >
                  <i :class="showNew ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Confirmar contraseña --}}
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirmar contraseña
              </label>
              <div class="relative">
                <input
                  :type="showConfirm ? 'text' : 'password'"
                  name="password_confirmation"
                  id="password_confirmation"
                  value="{{ old('password_confirmation') }}"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                  required
                >
                <button
                  type="button"
                  @click="showConfirm = !showConfirm"
                  class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-700 transition-opacity opacity-0 focus:opacity-100"
                >
                  <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                class="flex items-center px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
              >
                <i class="fas fa-check mr-2"></i>Actualizar
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>

    {{-- Modal de imagen completa --}}
    <div
      x-show="showImage"
      x-transition.opacity
      x-cloak
      @click.away="showImage = false"
      class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
    >
      <button
        @click="showImage = false"
        class="absolute top-4 right-4 text-white text-2xl"
      >×</button>
      <img
        src="{{ Auth::user()->avatar_url }}"
        alt="Avatar de {{ Auth::user()->name }}"
        class="w-[500px] h-[500px] object-cover"
      />
    </div>

    {{-- Modal Cambiar Avatar – Desktop (pantallas ≥ md) --}}
    <div
      x-show="showAvatarModal"
      x-transition.opacity
      x-cloak
      @click.away="showAvatarModal = false"
      class="hidden md:flex fixed inset-0 bg-black bg-opacity-80 items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

        {{-- Header --}}
        <div class="relative px-6 py-4 bg-gradient-to-r from-indigo-600 to-blue-500">
          <h3 class="text-lg font-semibold text-white">Actualizar foto de perfil</h3>
          <button
            @click="showAvatarModal = false"
            class="absolute right-4 top-1/2 -translate-y-[40%]
                  w-8 h-8 flex items-center justify-center
                  rounded-full text-white text-xl
                  leading-tight
                  hover:bg-white hover:bg-opacity-20
                  focus:outline-none focus:ring-2 focus:ring-white
                  transition-colors"
            aria-label="Cerrar"
          >
            <span class="-translate-y-[1px] block">×</span>
          </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-6 text-center">
          <div class="mx-auto w-32 h-32 rounded-full overflow-hidden border-4 border-indigo-200 shadow-inner">
            <img
              src="{{ Auth::user()->avatar_url ?? asset('images/default-avatar.png') }}"
              alt="Avatar actual"
              class="w-full h-full object-cover"
            >
          </div>
          <p class="text-sm text-gray-600">
            Elige una nueva imagen para que tus amigos te reconozcan fácilmente.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            @if(Auth::user()->avatar)
              <label
                for="avatar"
                class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg cursor-pointer transition-colors"
              >
                <i class="fas fa-upload mr-2"></i>Cambiar foto
              </label>
              <button
                @click.prevent="showDeleteConfirm = true"
                class="flex-1 w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors"
              >
                <i class="fas fa-trash-alt mr-2"></i>Eliminar foto
              </button>
            @else
              <label
                for="avatar"
                class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg cursor-pointer transition-colors"
              >
                <i class="fas fa-camera mr-2"></i>Añadir foto
              </label>
            @endif
          </div>
        </div>

      </div>

      {{-- Formulario oculto --}}
      <form
        id="avatarForm"
        action="{{ route('perfil.avatar') }}"
        method="POST"
        enctype="multipart/form-data"
        class="hidden"
      >
        @csrf
        @method('PUT')
        <input
          type="file"
          name="avatar"
          id="avatar"
          @change="showAvatarModal = false; showAvatarButtons = true"
        >
      </form>

      {{-- Modal Confirmación Eliminación --}}
      <div
        x-show="showDeleteConfirm"
        x-transition.opacity
        x-cloak
        @click.away="showDeleteConfirm = false"
        class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center z-60 p-4"
      >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xs p-6 text-center">
          <h3 class="text-lg font-semibold mb-4">¿Desea eliminar la imagen?</h3>
          <p class="text-sm text-gray-600 mb-6">
            ¿Confirma que desea eliminar de forma permanente esta imagen de su perfil?
          </p>
          <div class="flex items-center justify-between">
            <button
              @click="showDeleteConfirm = false"
              class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition"
            >
              Cancelar
            </button>
            <form action="{{ route('perfil.avatar.remove') }}" method="POST">
              @csrf
              @method('PUT')
              <button
                type="submit"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition"
              >
                Eliminar
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal Cambiar Avatar – Mobile/Tablet (pantallas < md) --}}
    <div
      x-show="showAvatarModal"
      x-transition.opacity
      x-cloak
      @click.away="showAvatarModal = false"
      class="md:hidden fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-2xl shadow-lg w-full max-w-xs overflow-hidden">

        {{-- Header simplificado --}}
        <div class="flex items-center justify-between px-4 py-3 border-b">
          <h3 class="text-lg font-semibold text-gray-800">Cambiar imagen</h3>
          <button
            @click="showAvatarModal = false"
            class="text-gray-600 hover:text-gray-800"
          >
            Cerrar
          </button>
        </div>

        {{-- Body con opciones de cámara y galería --}}
        <div class="px-4 py-6 space-y-4 text-center">
          {{-- Tomar fotografía --}}
          <label
            for="avatar_camera"
            class="block w-full py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition"
          >
            Tomar fotografía
          </label>
          <input
            id="avatar_camera"
            type="file"
            accept="image/*"
            capture="environment"
            class="hidden"
            @change="showAvatarModal = false; showAvatarButtons = true"
          />

          {{-- Abrir galería --}}
          <label
            for="avatar_gallery"
            class="block w-full py-2 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition"
          >
            Abrir galería
          </label>
          <input
            id="avatar_gallery"
            type="file"
            accept="image/*"
            class="hidden"
            @change="showAvatarModal = false; showAvatarButtons = true"
          />
        </div>
      </div>
    </div>

    {{-- Modal Editar Nombre --}}
    <div
      x-show="showNameModal"
      x-transition
      x-cloak
      @click.away="showNameModal = false"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex items-center space-x-3 px-6 py-4 border-b">
          <div class="text-blue-600 text-2xl"><i class="fas fa-user-edit"></i></div>
          <h2 class="text-lg font-semibold text-gray-800">Editar Nombre Completo</h2>
        </div>
        <form
          action="{{ route('perfil') }}"
          method="POST"
          @submit="showNameModal = false"
          class="px-6 py-5 space-y-4"
        >
          @csrf
          @method('PUT')
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
              Nombre completo
            </label>
            <input
              type="text"
              name="name"
              id="name"
              value="{{ old('name', Auth::user()->name) }}"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              required
            >
          </div>
          <div class="flex justify-end items-center space-x-3 pt-4 border-t">
            <button type="button" @click="showNameModal = false"
              class="flex items-center px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition"
            >
              <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <button type="submit"
              class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
              <i class="fas fa-check mr-2"></i>Guardar
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Modal Editar Correo --}}
    <div
      x-show="showEmailModal"
      x-transition
      x-cloak
      @click.away="showEmailModal = false"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex items-center space-x-3 px-6 py-4 border-b">
          <div class="text-green-600 text-2xl"><i class="fas fa-envelope-open-text"></i></div>
          <h2 class="text-lg font-semibold text-gray-800">Editar Correo Electrónico</h2>
        </div>
        <form
          action="{{ route('perfil') }}"
          method="POST"
          @submit="showEmailModal = false"
          class="px-6 py-5 space-y-4"
        >
          @csrf
          @method('PUT')
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
              Correo electrónico
            </label>
            <input
              type="email"
              name="email"
              id="email"
              value="{{ old('email', Auth::user()->email) }}"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 transition"
              required
            >
          </div>
          <div class="flex justify-end items-center space-x-3 pt-4 border-t">
            <button type="button" @click="showEmailModal = false"
              class="flex items-center px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition"
            >
              <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <button type="submit"
              class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
            >
              <i class="fas fa-check mr-2"></i>Guardar
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Modal Editar Teléfono --}}
    <div
      x-show="showPhoneModal"
      x-transition
      x-cloak
      @click.away="showPhoneModal = false"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex items-center space-x-3 px-6 py-4 border-b">
          <div class="text-indigo-600 text-2xl"><i class="fas fa-phone"></i></div>
          <h2 class="text-lg font-semibold text-gray-800">Editar Teléfono</h2>
        </div>
        <form
          action="{{ route('perfil') }}"
          method="POST"
          @submit="showPhoneModal = false"
          class="px-6 py-5 space-y-4"
        >
          @csrf
          @method('PUT')
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
              Teléfono de contacto
            </label>
            <input
              type="tel"
              name="phone"
              id="phone"
              value="{{ old('phone', Auth::user()->phone) }}"
              required minlength="9" maxlength="9"
              pattern="^9[0-9]{8}$"
              title="Debe empezar con 9 y tener 9 dígitos numéricos"
              onkeypress="return event.charCode >= 48 && event.charCode <= 57"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
            >
            @error('phone')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div class="flex justify-end items-center space-x-3 pt-4 border-t">
            <button type="button" @click="showPhoneModal = false"
              class="flex items-center px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition"
            >
              <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <button type="submit"
              class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
            >
              <i class="fas fa-check mr-2"></i>Guardar
            </button>
          </div>
        </form>
      </div>
    </div>

  </div><!-- fin max-w-5xl -->
</div><!-- fin container principal -->
@endsection
