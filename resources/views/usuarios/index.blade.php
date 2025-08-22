{{-- resources/views/usuarios/index.blade.php --}}

@extends('main')

@section('title')
    Usuarios
@endsection

@section('content')
<div class="w-full p-8 bg-gray-50">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <header class="flex flex-col md:flex-row justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-3xl font-extrabold text-gray-800">Lista de Usuarios</h2>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <button id="btnExcel" class="flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-file-excel mr-2"></i>Excel
                </button>
                <button id="btnPdf" class="flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-file-pdf mr-2"></i>PDF
                </button>
                <button id="btnNuevo" class="flex items-center bg-yellow-400 hover:bg-yellow-500 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-user-plus mr-2"></i>Nuevo Usuario
                </button>
            </div>
        </header>

        <div class="overflow-x-auto">
            <table id="tablaUsuarios" class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Correo Electrónico</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $usuario->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $usuario->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 capitalize">{{ $usuario->rol }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button data-usuario='@json($usuario)' class="btnEditar text-yellow-500 hover:text-yellow-700 mx-1" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="formEliminar inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 mx-1" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-600">No hay usuarios registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Crear Usuario --}}
<div id="modalCrear" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 {{ ($errors->any() && old('_method') !== 'PUT') ? '' : 'hidden' }}">
  <div class="bg-white rounded-md shadow-xl w-full max-w-md">
    <div class="bg-gray-800 text-white px-6 py-4 rounded-t-md">
      <h3 class="text-lg font-semibold text-center">Registrar Nuevo Usuario</h3>
    </div>
    <form method="POST" action="{{ route('usuarios.store') }}" class="px-6 py-6">
      @csrf
      <div class="space-y-4">
        <div>
          <label for="NuevoNombreTxt" class="block text-sm text-gray-700 mb-1">Nombre*</label>
          <input type="text" name="nombre" id="NuevoNombreTxt" required
                 class="w-full border {{ $errors->has('nombre') ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300 focus:ring-2 focus:ring-blue-300' }} rounded-md px-3 py-2 focus:outline-none"
                 value="{{ old('nombre') }}">
          <div class="error-msg text-gray-600 text-sm mt-1">@error('nombre'){{ $message }}@enderror</div>
        </div>

        <div>
          <label for="NuevoApellidoTxt" class="block text-sm text-gray-700 mb-1">Apellido*</label>
          <input type="text" name="apellido" id="NuevoApellidoTxt" required
                 class="w-full border {{ $errors->has('apellido') ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300 focus:ring-2 focus:ring-blue-300' }} rounded-md px-3 py-2 focus:outline-none"
                 value="{{ old('apellido') }}">
          <div class="error-msg text-gray-600 text-sm mt-1">@error('apellido'){{ $message }}@enderror</div>
        </div>

        <div>
          <label for="NuevoEmailTxt" class="block text-sm text-gray-700 mb-1">Correo Electrónico*</label>
          <!-- INPUT actualizado: ya no cambia el borde a rojo cuando hay error -->
          <input type="email" name="email" required id="NuevoEmailTxt"
                 class="w-full border border-gray-300 focus:ring-2 focus:ring-blue-300 rounded-md px-3 py-2 focus:outline-none"
                 value="{{ old('email') }}" maxlength="100">
          <div class="error-msg text-red-600 text-sm mt-1">@error('email'){{ $message }}@enderror</div>
        </div>

        <div>
          <label for="NuevoPasswordTxt" class="block text-sm text-gray-700 mb-1">Contraseña*</label>

          {{-- Contenedor relativo para colocar el ojo --}}
          <div class="relative">
            <input type="password" name="password" required id="NuevoPasswordTxt"
                   value="{{ old('password') }}"
                   class="w-full pr-10 border border-gray-300 focus:ring-2 focus:ring-blue-300 rounded-md px-3 py-2 focus:outline-none"
                   autocomplete="new-password">
            {{-- ojo: inicialmente oculto con clase .eye-hidden --}}
            <button type="button" id="togglePassword" class="absolute inset-y-0 right-2 flex items-center px-2 text-gray-500 focus:outline-none eye-hidden"
                    aria-label="Mostrar contraseña" aria-hidden="true" tabindex="-1">
                <i class="fas fa-eye" id="iconPassword" aria-hidden="true"></i>
            </button>
          </div>

          {{-- Solo el mensaje de error de contraseña será rojo --}}
          <div class="error-msg text-red-600 text-sm mt-1">@error('password'){{ $message }}@enderror</div>
        </div>

        <div>
          <label for="NuevoRolSelect" class="block text-sm text-gray-700 mb-1">Rol*</label>
          <select name="rol" id="NuevoRolSelect" required
                  class="w-full border {{ $errors->has('rol') ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300 focus:ring-2 focus:ring-blue-300' }} rounded-md px-3 py-2 bg-white focus:outline-none">
            <option value="">Selecciona un rol</option>
            <option value="recepcionista" {{ old('rol') == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
            <option value="medico" {{ old('rol') == 'medico' ? 'selected' : '' }}>Médico</option>
            <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
          </select>
          <div class="error-msg text-gray-600 text-sm mt-1">@error('rol'){{ $message }}@enderror</div>
        </div>
      </div>

      <div class="flex justify-between mt-6">
        <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow font-semibold">
          Registrar
        </button>
        <button type="button" id="cerrarCrear"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md shadow font-semibold">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Editar Usuario --}}
<div id="modalEditar" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 {{ ($errors->any() && old('_method') === 'PUT') ? '' : 'hidden' }}">
  <div class="bg-white rounded-md shadow-xl w-full max-w-md">
    <div class="bg-purple-700 text-white px-6 py-4 rounded-t-md">
      <h3 class="text-lg font-semibold text-center">Editar Usuario</h3>
    </div>
    <form id="formEditar" method="POST" action="{{ old('_method') === 'PUT' ? '#' : '' }}" class="px-6 py-6">
      @csrf
      @method('PUT')
      <div class="space-y-4">
        <div>
          <label class="block text-sm text-gray-700 mb-1">Nombre*</label>
          <input type="text" name="nombre" id="editNombre" required
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                 value="{{ old('nombre') }}">
          <div class="error-msg text-gray-600 text-sm mt-1">@error('nombre'){{ $message }}@enderror</div>
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">Apellido*</label>
          <input type="text" name="apellido" id="editApellido" required
                 class="w-full	border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                 value="{{ old('apellido') }}">
          <div class="error-msg text-gray-600 text-sm mt-1">@error('apellido'){{ $message }}@enderror</div>
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">Correo Electrónico*</label>
          <!-- INPUT actualizado: ya no cambia el borde a rojo cuando hay error -->
          <input type="email" name="email" id="editEmail" required
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                 maxlength="100" value="{{ old('email') }}">
          <div class="error-msg text-red-600 text-sm mt-1">@error('email'){{ $message }}@enderror</div>
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">Rol*</label>
          <select name="rol" id="editRol" required
                  class="w-full	border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="">Selecciona un rol</option>
            <option value="recepcionista" {{ old('rol') == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
            <option value="medico" {{ old('rol') == 'medico' ? 'selected' : '' }}>Médico</option>
            <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
          </select>
          <div class="error-msg text-gray-600 text-sm mt-1">@error('rol'){{ $message }}@enderror</div>
        </div>
      </div>
      <div class="flex justify-between mt-6">
        <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow font-semibold">
          Actualizar
        </button>
        <button type="button" id="cerrarEditar"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md shadow font-semibold">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    var table = $('#tablaUsuarios').DataTable({
        stateSave: true,
        dom: '<"flex justify-between items-center mb-2"lf>rt<"flex justify-between items-center mt-4"ip><"clear">',
        ordering: false,
        buttons: [
            {
              extend: 'excelHtml5',
              title: '',
              filename: 'Usuarios',
              exportOptions: { columns: [0,1,2] },
              className: 'd-none',
              customizeData: function(data) {
                if (data.header && data.header[0]) {
                  data.header[0][0] = 'Usuarios';
                }
              }
            },
            {
              extend: 'pdfHtml5',
              title: 'Usuarios',
              filename: 'Usuarios',
              exportOptions: { columns: [0,1,2] },
              className: 'd-none',
              customize: function(doc) {
                if (!doc.styles) doc.styles = {};
                doc.styles.title = { alignment: 'center', fontSize: 16, margin: [0,0,0,10] };
              }
            }
        ],
        language: {
            emptyTable: "No hay usuarios registrados",
            zeroRecords: "No se encontraron resultados",
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ entradas",
            info: "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
            infoEmpty: "Mostrando 0 usuarios",
            infoFiltered: "(filtrado de _MAX_ usuarios)",
            paginate: { first: "Primero", previous: "Anterior", next: "Siguiente", last: "Último" }
        }
    });

    var btnExcel = table.button(0), btnPdf = table.button(1);
    $('#btnExcel').click(function() { if (table.data().any()) btnExcel.trigger(); });
    $('#btnPdf').click(function() { if (table.data().any()) btnPdf.trigger(); });

    $('.formEliminar').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(res) {
            if (res.isConfirmed) form.submit();
        });
    });

    // Helpers: ojo oculto por defecto
    function hideEye() {
        $('#togglePassword').addClass('eye-hidden').attr('aria-hidden','true');
    }
    function showEye() {
        $('#togglePassword').removeClass('eye-hidden').removeAttr('aria-hidden');
    }

    // Inicial: ocultar el ojo
    hideEye();

    // Nuevo usuario modal
    $('#btnNuevo').click(function() {
        $('#NuevoNombreTxt').val('');
        $('#NuevoApellidoTxt').val('');
        $('#NuevoEmailTxt').val('');
        $('#NuevoPasswordTxt').val('');
        $('#NuevoRolSelect').val('');
        $('.error-msg').empty();
        // Asegurarnos que el input esté en tipo password y el icono en estado inicial
        $('#NuevoPasswordTxt').attr('type','password');
        $('#iconPassword').removeClass('fa-eye-slash').addClass('fa-eye');
        $('#togglePassword').attr('aria-label','Mostrar contraseña');
        hideEye(); // ocultar ojo al abrir modal (hasta que escriban)
        $('#modalCrear').removeClass('hidden');
    });
    $('#cerrarCrear').click(() => {
        $('#modalCrear').addClass('hidden');
        // limpiar y ocultar por seguridad
        $('#NuevoPasswordTxt').val('').attr('type','password');
        $('#iconPassword').removeClass('fa-eye-slash').addClass('fa-eye');
        $('#togglePassword').attr('aria-label','Mostrar contraseña');
        hideEye();
    });

    // Mostrar/ocultar ojo según se escriba en el input (aparece solo si hay contenido)
    $('#NuevoPasswordTxt').on('input', function() {
        var val = $(this).val() || '';
        if (val.length > 0) {
            showEye();
        } else {
            // si está vacío, ocultamos y volvemos a tipo password
            hideEye();
            $(this).attr('type','password');
            $('#iconPassword').removeClass('fa-eye-slash').addClass('fa-eye');
            $('#togglePassword').attr('aria-label','Mostrar contraseña');
        }
    });

    // Evitar que el botón del ojo reciba foco al hacer clic (previene anillo azul)
    $('#togglePassword').on('mousedown', function(e) {
        e.preventDefault();
    });

    // Toggle mostrar/ocultar contraseña (ojo)
    $('#togglePassword').on('click', function() {
        var input = $('#NuevoPasswordTxt');
        var icon = $('#iconPassword');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
            $(this).attr('aria-label','Ocultar contraseña');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
            $(this).attr('aria-label','Mostrar contraseña');
        }

        // Asegurarnos de que ninguno tenga foco para evitar el anillo azul
        input.blur();
        $(this).blur();
    });

    // Editar usuario
    $('.btnEditar').click(function() {
        const u = $(this).data('usuario');
        $('#formEditar').attr('action', '/usuarios/' + u.id);
        // dividir nombre en nombre y apellidos (aprox.)
        const parts = (u.name || '').split(' ');
        $('#editNombre').val(parts[0] || '');
        $('#editApellido').val(parts.slice(1).join(' ') || '');
        $('#editEmail').val(u.email || '');
        $('#editRol').val(u.rol || '');
        $('.error-msg').empty();
        $('#modalEditar').removeClass('hidden');
    });
    $('#cerrarEditar').click(() => $('#modalEditar').addClass('hidden'));

    // Si el servidor devolvió errores y uno de ellos es password, mostramos el modal pero NO enfocamos el input
    @if ($errors->has('password'))
        $('#modalCrear').removeClass('hidden'); // mostramos modal, pero no llamamos a focus() para evitar el anillo azul
    @endif

});
</script>

<style>
#tablaUsuarios td.dataTables_empty {
  text-align: center;
  font-size: 1rem;
  color: #6b7280;
}
#tablaUsuarios th,
#tablaUsuarios td {
  border: 1.5px solid #9CA3AF;
  padding: 8px;
}
#tablaUsuarios thead {
  background-color: #f3f4f6;
}

/* Ajustes para el botón ojo */
#togglePassword {
  background: transparent;
  border: none;
  padding: 0.25rem;
  border-radius: 0.25rem;
  transition: opacity .12s ease, visibility .12s ease;
}

/* clase usada para ocultar el ojo (no ocupa click ni foco) */
.eye-hidden {
  visibility: hidden;
  opacity: 0;
  pointer-events: none;
}

/* Si por alguna razón queda foco, anulamos outline/box-shadow visibles */
#togglePassword:focus {
  outline: none !important;
  box-shadow: none !important;
}
</style>

@endsection
