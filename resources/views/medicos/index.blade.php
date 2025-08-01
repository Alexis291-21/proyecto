{{-- resources/views/medicos/index.blade.php --}}

@extends('main')

@section('title')
    Médicos
@endsection

@section('content')
<div class="w-full p-8 bg-gray-50">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <header class="flex flex-col md:flex-row justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-3xl font-extrabold text-gray-800">Lista de Médicos</h2>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <button id="btnExcel" class="flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-file-excel mr-2"></i>Excel
                </button>
                <button id="btnPdf" class="flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-file-pdf mr-2"></i>PDF
                </button>
                <button id="abrirModalCrear" class="flex items-center bg-yellow-400 hover:bg-yellow-500 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-plus-circle mr-2"></i>Agregar Médico
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
            <table id="tablaMedicos" class="w-full divide-y divide-gray-200" style="visibility: hidden;">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre completo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Especialidad</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">DNI</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Correo electrónico</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Disponibilidad</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($medicos as $medico)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ $medico->nombre }} {{ $medico->apellido_paterno }} {{ $medico->apellido_materno }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $medico->especialidad }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $medico->telefono }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $medico->dni }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $medico->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium {{ $medico->disponibilidad == 'Disponible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $medico->disponibilidad }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="javascript:void(0)" data-editar='@json($medico)' class="text-yellow-500 hover:text-yellow-700 mx-1" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('medicos.destroy', $medico) }}" method="POST" class="formEliminar inline-block">
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

{{-- Modal Crear / Editar Médico --}}
<div id="modalMedico" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-md shadow-xl w-full max-w-md">
        <div class="bg-gray-800 text-white px-6 py-4 rounded-t-md">
            <h3 id="tituloModal" class="text-lg font-semibold text-center">Agregar Médico</h3>
        </div>

        <form id="formMedico" method="POST" action="{{ route('medicos.store') }}" class="px-6 py-6 space-y-4">
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

            <div>
                <label class="block text-sm text-gray-700 mb-1">Especialidad</label>
                <input type="text" name="especialidad" id="inputEspecialidad"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       required>
                <div class="error-msg text-red-600 text-sm mt-1">@error('especialidad'){{ $message }}@enderror</div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono" id="inputTelefono"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       maxlength="9" pattern="\d{9}" inputmode="numeric">
                <div class="error-msg text-red-600 text-sm mt-1">@error('telefono'){{ $message }}@enderror</div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">DNI</label>
                <input type="text" name="dni" id="inputDni"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       maxlength="8" pattern="\d{8}">
                <div class="error-msg text-red-600 text-sm mt-1">@error('dni'){{ $message }}@enderror</div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="email" id="inputEmail"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       maxlength="100">
                <div class="error-msg text-red-600 text-sm mt-1">@error('email'){{ $message }}@enderror</div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Disponibilidad</label>
                <select name="disponibilidad" id="inputDisponibilidad"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="Disponible">Disponible</option>
                    <option value="No disponible">No disponible</option>
                </select>
                <div class="error-msg text-red-600 text-sm mt-1">@error('disponibilidad'){{ $message }}@enderror</div>
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
    var table = $('#tablaMedicos').DataTable({
        stateSave: true,
        dom: '<"flex justify-between items-center mb-2"lf>rt<"flex justify-between items-center mt-4"ip><"clear">',
        ordering: false,
        buttons: [
            { extend: 'excelHtml5', exportOptions: { columns: [0,1,2,3,4] }, className: 'd-none' },
            { extend: 'pdfHtml5',   exportOptions: { columns: [0,1,2,3,4] }, className: 'd-none' }
        ],
        language: {
            emptyTable: 'No hay medicos registrados',
            zeroRecords: 'No se encontraron resultados',
            processing: 'Procesando...',
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_ entradas',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ médicos',
            infoEmpty: 'Mostrando 0 médicos',
            infoFiltered: '(filtrado de _MAX_ médicos)',
            paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Último' }
        },
        initComplete: function() {
            $('#tablaMedicos').css('visibility', '');
        }
    });

    $('#btnExcel').click(() => table.data().any() && table.button(0).trigger());
    $('#btnPdf').click(() => table.data().any() && table.button(1).trigger());

    $('#tablaMedicos tbody').on('click', '.formEliminar button', function(e) {
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
        $('#formMedico')[0].reset();
        $('.error-msg').empty();
        $('#tituloModal').text('Agregar Médico');
        $('#formMedico').attr('action', "{{ route('medicos.store') }}");
        $('#inputMetodo').val('POST');
        $('#modalMedico').removeClass('hidden');
    });

    $('#tablaMedicos tbody').on('click', 'a[data-editar]', function() {
        let m = $(this).data('editar');
        $('#formMedico')[0].reset();
        $('.error-msg').empty();
        $('#tituloModal').text('Editar Médico');
        $('#formMedico').attr('action', '/medicos/' + m.id);
        $('#inputMetodo').val('PUT');
        $('#inputNombre').val(m.nombre);
        $('#inputPaterno').val(m.apellido_paterno);
        $('#inputMaterno').val(m.apellido_materno);
        $('#inputEspecialidad').val(m.especialidad);
        $('#inputTelefono').val(m.telefono);
        $('#inputDni').val(m.dni);
        $('#inputEmail').val(m.email);
        $('#inputDisponibilidad').val(m.disponibilidad);

        // Cambiar el texto y color del botón a "Actualizar" cuando se está editando
        $('#formMedico button[type="submit"]').text('Actualizar').removeClass('bg-green-600').addClass('bg-blue-600 hover:bg-blue-700');

        $('#modalMedico').removeClass('hidden');
    });

    $('#cerrarModal').on('click', function() {
        $('#formMedico')[0].reset();
        $('.error-msg').empty();
        $('#modalMedico').addClass('hidden');
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

    $('#formMedico').submit(function(e) {
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
        $('#modalMedico').removeClass('hidden');
        let metodo = "{{ old('_method', 'POST') }}";
        $('#tituloModal').text(metodo === 'PUT' ? 'Editar Médico' : 'Agregar Médico');
    @endif
});
</script>

<style>
#tablaMedicos td.dataTables_empty { text-align: center; font-size: 1rem; color: #6b7280; }
#tablaMedicos th, #tablaMedicos td { border: 1.5px solid #9CA3AF; padding: 8px; }
#tablaMedicos thead { background-color: #f3f4f6; }
</style>
@endsection
