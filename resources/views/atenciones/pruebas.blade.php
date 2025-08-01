{{-- resources/views/atenciones/pruebas.blade.php --}}
@extends('main')

@section('title')
    Pruebas Rápidas
@endsection

@section('content')
<div class="w-full p-8 bg-gray-50">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <header class="flex flex-col md:flex-row justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-3xl font-extrabold text-gray-800">Pruebas Rápidas</h2>
            <div class="mt-4 md:mt-0 flex space-x-3">
                {{-- Mostrar “Exportar Excel”, “Exportar PDF” y “Nueva Prueba” solo si NO es médico --}}
                @if(Auth::user()->rol !== 'medico')
                    <button id="btnExcel" class="flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                        <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                    </button>
                    <button id="btnPdf" class="flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                        <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                    </button>
                    <button id="abrirModalCrear" class="flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                        <i class="fas fa-plus-circle mr-2"></i>Nueva Prueba
                    </button>
                @endif

                {{-- Este enlace siempre se muestra --}}
                <a href="{{ route('atenciones.index') }}" class="flex items-center bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-flask mr-2"></i>Volver a Atenciones
                </a>
            </div>
        </header>

        <div class="overflow-x-auto">
            <table id="tablaPruebas" class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha Atención</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Glucosa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">VIH</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Embarazo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">COVID-19</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo de Muestra</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>

                        {{-- Mostrar “Acciones” si el rol es admin o médico --}}
                        @if(in_array(Auth::user()->rol, ['admin', 'medico']))
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pruebas as $prueba)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ $prueba->atencion->paciente->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ \Carbon\Carbon::parse($prueba->atencion->fecha_examen)->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $prueba->glucosa }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ ucfirst($prueba->vih) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ ucfirst($prueba->embarazo) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ ucfirst($prueba->covid_19) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ ucfirst($prueba->tipo_muestra) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $prueba->estado === 'En curso'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-green-100 text-green-800' }}">
                                    {{ $prueba->estado }}
                                </span>
                            </td>

                            {{-- Renderizar “Acciones” si el rol es admin o médico --}}
                            @if(in_array(Auth::user()->rol, ['admin', 'medico']))
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center space-x-2">
                                        {{-- Si es ADMIN, mostrar botón "Atendida" y botón "Eliminar" --}}
                                        @if(Auth::user()->rol === 'admin')
                                            @if($prueba->estado === 'En curso')
                                                <form action="{{ route('pruebas.atendida', $prueba->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                                        Atendida
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('pruebas.destroy', $prueba->id) }}" method="POST" class="inline-block formEliminarPrueba">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 mx-1" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>

                                        {{-- Si es MÉDICO, mostrar solo botón "Atendida" --}}
                                        @elseif(Auth::user()->rol === 'medico')
                                            @if($prueba->estado === 'En curso')
                                                <form action="{{ route('pruebas.atendida', $prueba->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                                        Atendida
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Si ya no está "En curso", mostrar guión --}}
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL Nueva Prueba -->
<div id="modalPrueba" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <h3 id="tituloModalPrueba" class="text-xl font-bold mb-4 text-gray-800 text-center">Nueva Prueba Rápida</h3>
        <form id="formPrueba" method="POST" action="{{ route('pruebas.store') }}">
            @csrf

            <div class="mb-4">
                <label for="atencion_id" class="block text-gray-700 text-sm font-bold mb-2">Atención</label>
                <select name="atencion_id" id="inputAtencion" class="w-full border rounded p-2" required>
                    <option value="" disabled selected>Seleccione atención</option>
                    @foreach($atenciones as $a)
                        <option value="{{ $a->id }}">{{ $a->paciente->nombre ?? 'Sin nombre' }} – {{ $a->fecha_examen }}</option>
                    @endforeach
                </select>
                <div class="error-msg text-red-600 text-sm mt-1">@error('atencion_id') {{ $message }} @enderror</div>
            </div>

            <div class="mb-4">
                <label for="glucosa" class="block text-gray-700 text-sm font-bold mb-2">Glucosa</label>
                <input type="text" name="glucosa" id="inputGlucosa" class="w-full border rounded p-2">
                <div class="error-msg text-red-600 text-sm mt-1">@error('glucosa') {{ $message }} @enderror</div>
            </div>

            <!-- VIH -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">VIH</label>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="vih" value="positivo" class="form-radio text-blue-600">
                        <span class="ml-2 text-gray-800">Positivo</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="vih" value="negativo" class="form-radio text-blue-600">
                        <span class="ml-2 text-gray-800">Negativo</span>
                    </label>
                </div>
                <div class="error-msg text-red-600 text-sm mt-1">@error('vih') {{ $message }} @enderror</div>
            </div>

            <!-- Embarazo -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Embarazo</label>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="embarazo" value="positivo" class="form-radio text-blue-600">
                        <span class="ml-2 text-gray-800">Positivo</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="embarazo" value="negativo" class="form-radio text-blue-600">
                        <span class="ml-2 text-gray-800">Negativo</span>
                    </label>
                </div>
                <div class="error-msg text-red-600 text-sm mt-1">@error('embarazo') {{ $message }} @enderror</div>
            </div>

            <!-- COVID-19 -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">COVID-19</label>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="covid_19" value="positivo" class="form-radio text-blue-600">
                        <span class="ml-2 text-gray-800">Positivo</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="covid_19" value="negativo" class="form-radio text-blue-600">
                        <span class="ml-2 text-gray-800">Negativo</span>
                    </label>
                </div>
                <div class="error-msg text-red-600 text-sm mt-1">@error('covid_19') {{ $message }} @enderror</div>
            </div>

            <div class="mb-4">
                <label for="tipo_muestra" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Muestra</label>
                <select name="tipo_muestra" id="inputTipo" class="w-full border rounded p-2" required>
                    <option value="sangre">Sangre</option>
                    <option value="hisopado">Hisopado</option>
                    <option value="orina">Orina</option>
                    <option value="otro">Otro</option>
                </select>
                <div class="error-msg text-red-600 text-sm mt-1">@error('tipo_muestra') {{ $message }} @enderror</div>
            </div>

            <div class="flex justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Guardar</button>
                <button type="button" id="cerrarModalPrueba" class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Librerías -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    var table = $('#tablaPruebas').DataTable({
        stateSave: true,
        dom: '<"flex justify-between items-center mb-2"lf>rt<"flex justify-between items-center mt-4"ip><"clear">',
        ordering: false,
        buttons: [
            { extend: 'excelHtml5', title: 'Pruebas Rápidas', exportOptions: { columns: [0,1,2,3,4,5,6,7] }, className: 'd-none' },
            { extend: 'pdfHtml5',   title: 'Pruebas Rápidas', exportOptions: { columns: [0,1,2,3,4,5,6,7] }, className: 'd-none' }
        ],
        language: {
            emptyTable: "No hay pruebas registradas",
            zeroRecords: "No se encontraron resultados",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ entradas",
            info: "Mostrando _START_ a _END_ de _TOTAL_ pruebas",
            paginate: { first: "Primero", previous: "Anterior", next: "Siguiente", last: "Último" }
        }
    });

    var excelBtn = table.button(0), pdfBtn = table.button(1);
    $('#btnExcel').click(() => table.data().any() && excelBtn.trigger());
    $('#btnPdf').click(() => table.data().any() && pdfBtn.trigger());

    $('#inputAtencion').select2({
        width: '100%',
        placeholder: 'Seleccionar atención (Paciente - Fecha)',
        allowClear: true
    });

    // Modal Crear
    $('#abrirModalCrear').click(function() {
        $('#formPrueba')[0].reset();
        $('.error-msg').empty();
        $('#modalPrueba').removeClass('hidden');
    });
    $('#cerrarModalPrueba').click(function() {
        $('#formPrueba')[0].reset();
        $('.error-msg').empty();
        $('#modalPrueba').addClass('hidden');
    });

    // Mantener modal abierto si hay errores
    @if ($errors->any())
        $('#modalPrueba').removeClass('hidden');
    @endif

    // Confirmación de eliminación para ADMIN
    $('.formEliminarPrueba').on('submit', function(e) {
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
        }).then(res => {
            if (res.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>

<style>
#tablaPruebas td.dataTables_empty { text-align: center; font-size: 1rem; color: #6b7280; }
#tablaPruebas th, #tablaPruebas td { border: 1px solid #e5e7eb; padding: 8px; }
#tablaPruebas thead { background-color: #f3f4f6; }
</style>
@endsection
