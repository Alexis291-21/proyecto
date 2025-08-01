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
<div id="modalCrear" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
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
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div>
          <label for="NuevoApellidoTxt" class="block text-sm text-gray-700 mb-1">Apellido*</label>
          <input type="text" name="apellido" id="NuevoApellidoTxt" required
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div>
          <label for="NuevoEmailTxt" class="block text-sm text-gray-700 mb-1">Correo Electrónico*</label>
          <input type="email" name="email" required id="NuevoEmailTxt"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div>
          <label for="NuevoPasswordTxt" class="block text-sm text-gray-700 mb-1">Contraseña*</label>
          <input type="password" name="password" required id="NuevoPasswordTxt"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div>
          <label for="NuevoRolSelect" class="block text-sm text-gray-700 mb-1">Rol*</label>
          <select name="rol" id="NuevoRolSelect" required
                  class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="">Selecciona un rol</option>
            <option value="recepcionista">Recepcionista</option>
            <option value="medico">Médico</option>
            <option value="admin">Administrador</option>
          </select>
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
<div id="modalEditar" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-md shadow-xl w-full max-w-md">
    <div class="bg-purple-700 text-white px-6 py-4 rounded-t-md">
      <h3 class="text-lg font-semibold text-center">Editar Usuario</h3>
    </div>
    <form id="formEditar" method="POST" class="px-6 py-6">
      @csrf
      @method('PUT')
      <div class="space-y-4">
        <div>
          <label class="block text-sm text-gray-700 mb-1">Nombre*</label>
          <input type="text" name="nombre" id="editNombre" required
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">Apellido*</label>
          <input type="text" name="apellido" id="editApellido" required
                 class="w-full	border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">Correo Electrónico*</label>
          <input type="email" name="email" id="editEmail" required
                 class="w-full	border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">Rol*</label>
          <select name="rol" id="editRol" required
                  class="w-full	border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="">Selecciona un rol</option>
            <option value="recepcionista">Recepcionista</option>
            <option value="medico">Médico</option>
            <option value="admin">Administrador</option>
          </select>
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
            { extend: 'excelHtml5', title: 'Usuarios', exportOptions: { columns: [0,1,2] }, className: 'd-none' },
            { extend: 'pdfHtml5',   title: 'Usuarios', exportOptions: { columns: [0,1,2] }, className: 'd-none' }
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

    $('#btnExcel').click(() => table.data().any() && table.button(0).trigger());
    $('#btnPdf').click(() => table.data().any() && table.button(1).trigger());

    $('.formEliminar').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(res => res.isConfirmed && this.submit());
    });

    $('#btnNuevo').click(function() {
            $('#NuevoNombreTxt').val('');
            $('#NuevoApellidoTxt').val('');
            $('#NuevoEmailTxt').val('');
            $('#NuevoPasswordTxt').val('');
            $('#NuevoRolSelect').val('');
            $('#modalCrear').removeClass('hidden');
        }
    );
    $('#cerrarCrear').click(() => $('#modalCrear').addClass('hidden'));

    $('.btnEditar').click(function() {
        const u = $(this).data('usuario');
        $('#formEditar').attr('action', '/usuarios/' + u.id);
        $('#editNombre').val(u.name.split(' ')[0]);
        $('#editApellido').val(u.name.split(' ').slice(1).join(' '));
        $('#editEmail').val(u.email);
        $('#editRol').val(u.rol);
        $('#modalEditar').removeClass('hidden');
    });
    $('#cerrarEditar').click(() => $('#modalEditar').addClass('hidden'));
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
</style>

@endsection
