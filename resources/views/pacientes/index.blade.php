{{-- resources/views/pacientes/index.blade.php --}}

@extends('main')

@section('title')
    Pacientes
@endsection

@section('content')
<div class="w-full p-8 bg-gray-50">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <header class="flex flex-col md:flex-row justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-3xl font-extrabold text-gray-800">Lista de Pacientes</h2>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <button id="btnExcel" class="flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-file-excel mr-2"></i>Excel
                </button>
                <button id="btnPdf" class="flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-file-pdf mr-2"></i>PDF
                </button>
                <button id="abrirModalCrear" class="flex items-center bg-yellow-400 hover:bg-yellow-500 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-plus-circle mr-2"></i>Agregar Paciente
                </button>
            </div>
        </header>

        @if(session('success'))
            <div class="fixed top-16 right-16 mb-4 px-6 py-4 rounded-md bg-white text-green-500 border border-green-500 shadow-lg z-50 transform transition-all duration-300 ease-in-out opacity-100 scale-100">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-2xl text-green-500"></i>
                    <div class="text-sm font-medium">
                        <p class="text-gray-800 text-base">{{ session('success') }}</p> <!-- Aumentamos el tamaño con text-base -->
                    </div>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table id="tablaPacientes" class="w-full divide-y divide-gray-200" style="visibility: hidden;">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre completo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Edad</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Género</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dirección</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">DNI</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">F. Nacimiento</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pacientes as $paciente)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $paciente->edad }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $paciente->genero }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $paciente->telefono }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $paciente->direccion }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $paciente->dni }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="javascript:void(0)" data-editar='@json($paciente)' class="text-yellow-500 hover:text-yellow-700 mx-1" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" class="formEliminar inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 mx-1" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Crear / Editar Paciente --}}
<div id="modalPaciente" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-md shadow-xl w-full max-w-md">
        <div class="bg-gray-800 text-white px-6 py-4 rounded-t-md">
            <h3 id="tituloModal" class="text-lg font-semibold text-center">Agregar Paciente</h3>
        </div>

        <form id="formPaciente" method="POST" action="{{ route('pacientes.store') }}" class="px-6 py-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="inputMetodo" value="POST">

            <div>
                <label class="block text-sm text-gray-700 mb-1">Nombre</label>
                <input type="text" name="nombre" id="inputNombre"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       required>
                <div class="error-msg text-red-600 text-sm mt-1">@error('nombre'){{ $message }}@enderror</div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Apellido Paterno</label>
                <input type="text" name="apellido_paterno" id="inputPaterno"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       required>
                <div class="error-msg text-red-600 text-sm mt-1">@error('apellido_paterno'){{ $message }}@enderror</div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Apellido Materno</label>
                <input type="text" name="apellido_materno" id="inputMaterno"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       required>
                <div class="error-msg text-red-600 text-sm mt-1">@error('apellido_materno'){{ $message }}@enderror</div>
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="block text-sm text-gray-700 mb-1">Edad</label>
                    <input type="number" name="edad" id="inputEdad"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                           required>
                    <div class="error-msg text-red-600 text-sm mt-1">@error('edad'){{ $message }}@enderror</div>
                </div>
                <div class="w-1/2">
                    <label class="block text-sm text-gray-700 mb-1">Género</label>
                    <select name="genero" id="inputGenero"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <div class="error-msg text-red-600 text-sm mt-1">@error('genero'){{ $message }}@enderror</div>
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono" id="inputTelefono"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       maxlength="9" pattern="\d{9}">
                <div class="error-msg text-red-600 text-sm mt-1">@error('telefono'){{ $message }}@enderror</div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Dirección</label>
                <input type="text" name="direccion" id="inputDireccion"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       required>
                <div class="error-msg text-red-600 text-sm mt-1">@error('dirección'){{ $message }}@enderror</div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">DNI</label>
                <input type="text" name="dni" id="inputDni"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       maxlength="8" pattern="\d{8}">
                <div class="error-msg text-red-600 text-sm mt-1">@error('dni'){{ $message }}@enderror</div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="inputFN"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       required>
                <div class="error-msg text-red-600 text-sm mt-1">@error('fecha_nacimiento'){{ $message }}@enderror</div>
            </div>

            <div class="flex justify-between mt-6">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow font-semibold">
                    Guardar
                </button>
                <button type="button" id="cerrarModal"
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
$(function(){
    var table = $('#tablaPacientes').DataTable({
        stateSave: true,
        dom: '<"flex justify-between items-center mb-2"lf>rt<"flex justify-between items-center mt-4"ip><"clear">',
        ordering: false,
        buttons: [
            { extend: 'excelHtml5', exportOptions: { columns: [0,1,2,3,4,5,6] }, className: 'd-none' },
            { extend: 'pdfHtml5',   exportOptions: { columns: [0,1,2,3,4,5,6] }, className: 'd-none' }
        ],
        language: {
            emptyTable: 'No hay pacientes registrados',
            zeroRecords: 'No se encontraron resultados',
            processing: 'Procesando...',
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_ entradas',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ pacientes',
            paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Último' }
        },
        initComplete: function() {
            $('#tablaPacientes').css('visibility', '');
        }
    });

    $('#btnExcel').click(() => table.data().any() && table.button(0).trigger());
    $('#btnPdf').click(() => table.data().any() && table.button(1).trigger());

    $('#tablaPacientes').on('click', '.formEliminar button', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(res => res.isConfirmed && form.submit());
    });

    $('#abrirModalCrear').click(function() {
        $('#formPaciente')[0].reset();
        $('.error-msg').empty();
        $('#tituloModal').text('Agregar Paciente');
        $('#formPaciente').attr('action', "{{ route('pacientes.store') }}");
        $('#inputMetodo').val('POST');
        $('#modalPaciente').removeClass('hidden');
    });

    // Handler para editar paciente
    $('#tablaPacientes').on('click', 'a[data-editar]', function() {
        let p = $(this).data('editar');

        // 1) Reset del formulario y mensajes de error
        $('#formPaciente')[0].reset();
        $('.error-msg').empty();

        // 2) Ajustes del modal: título, acción y método HTTP
        $('#tituloModal').text('Editar Paciente');
        $('#formPaciente')
            .attr('action', '/pacientes/' + p.id);
        $('#inputMetodo').val('PUT');

        // 3) Cambiar el estilo y texto del botón
        $('#formPaciente button[type="submit"]')
            .text('Actualizar')
            .removeClass('bg-green-600')
            .addClass('bg-blue-600 hover:bg-blue-700');

        // 4) Rellenar campos con los datos del paciente
        $('#inputNombre').val(p.nombre);
        $('#inputPaterno').val(p.apellido_paterno);
        $('#inputMaterno').val(p.apellido_materno);
        $('#inputEdad').val(p.edad);
        $('#inputGenero').val(p.genero);
        $('#inputTelefono').val(p.telefono);
        $('#inputDireccion').val(p.direccion);
        $('#inputDni').val(p.dni);

        // 5) Formatear fecha de nacimiento en YYYY‑MM‑DD
        let fechaIso = p.fecha_nacimiento.split('T')[0];
        $('#inputFN').val(fechaIso);

        // 6) Mostrar el modal
        $('#modalPaciente').removeClass('hidden');
    });

    $('#cerrarModal').click(function() {
        $('#modalPaciente').addClass('hidden');
    });

    $(document).ready(function() {
        @if(session('success'))
            var successMessage = $('.fixed.top-16.right-16.mb-4.px-6.py-4'); // Selecciona el div del mensaje de éxito
            setTimeout(function() {
                successMessage.fadeOut(500, function() {
                    $(this).remove(); // Elimina el mensaje después de desaparecer
                });
            },8000);
        @endif
    });

    $('#formPaciente').submit(function(e) {
        e.preventDefault();
        let $form = $(this), url = $form.attr('action'), data = $form.serialize();
        $form.find('.error-msg').text('');
        $.post({ url, data,
            success: resp => resp.success && location.reload(),
            error: xhr => {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, (field, msgs) => {
                        $form.find("[name='" + field + "']").siblings('.error-msg').text(msgs[0]);
                    });
                }
            }
        });
    });

    @if ($errors->any())
        $('#modalPaciente').removeClass('hidden');
        let metodo = "{{ old('_method', 'POST') }}";
        $('#tituloModal').text(metodo === 'PUT' ? 'Editar Paciente' : 'Agregar Paciente');
    @endif
});
</script>

<style>
#tablaPacientes td.dataTables_empty { text-align: center; font-size: 1rem; color: #6b7280; }
#tablaPacientes th, #tablaPacientes td { border: 1.5px solid #9CA3AF; padding: 8px; }
#tablaPacientes thead { background-color: #f3f4f6; }
</style>

@endsection
