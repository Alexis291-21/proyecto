{{-- resources/views/atenciones/laboratorio.blade.php --}}
@extends('main')

@section('title')
    Exámenes de Laboratorio
@endsection

@section('content')
<div class="w-full p-8 bg-gray-50">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <header class="flex flex-col md:flex-row justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-3xl font-extrabold text-gray-800">Exámenes de Laboratorio</h2>
            <div class="mt-4 md:mt-0 flex space-x-3">
                {{-- Mostrar “Exportar Excel”, “Exportar PDF” y “Nuevo Examen” solo si NO es médico --}}
                @if(Auth::user()->rol !== 'medico')
                    <button id="btnExcel" class="flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                        <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                    </button>
                    <button id="btnPdf" class="flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                        <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                    </button>
                    <button id="abrirModalCrear" class="flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                        <i class="fas fa-plus-circle mr-2"></i>Nuevo Examen
                    </button>
                @endif

                {{-- Enlace que siempre se muestra --}}
                <a href="{{ route('atenciones.index') }}" class="flex items-center bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-notes-medical mr-2"></i>Volver a Atenciones
                </a>
            </div>
        </header>

        <div class="overflow-x-auto">
            <table id="tablaLaboratorio" class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha Examen</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hemoglobina (g/dL)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Leucocitos (cél/μL)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Plaquetas (mil/μL)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Colesterol Total (mg/dL)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Triglicéridos (mg/dL)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Glucosa Ayunas (mg/dL)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo de Muestra</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>

                        {{-- Mostrar “Acciones” para admin y médico --}}
                        @if(in_array(Auth::user()->rol, ['admin', 'medico']))
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($laboratorios as $lab)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ $lab->atencion->paciente->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ \Carbon\Carbon::parse($lab->atencion->fecha_examen)->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $lab->hemoglobina ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $lab->leucocitos ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $lab->plaquetas ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $lab->colesterol_total ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $lab->trigliceridos ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $lab->glucosa_ayunas ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ ucfirst($lab->tipo_muestra) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $lab->estado === 'En curso'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-green-100 text-green-800' }}">
                                    {{ $lab->estado }}
                                </span>
                            </td>

                            {{-- Mostrar “Acciones” para admin y médico --}}
                            @if(in_array(Auth::user()->rol, ['admin', 'medico']))
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center space-x-2">
                                        {{-- Botón "Atendida" (para médico y admin, solo si está “En curso”) --}}
                                        @if($lab->estado === 'En curso')
                                            <form action="{{ route('atenciones-laboratorio.atender', $lab->id) }}" method="POST">
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

                                        {{-- Si es admin, también mostramos el botón "Eliminar" --}}
                                        @if(Auth::user()->rol === 'admin')
                                            <form action="{{ route('atenciones-laboratorio.destroy', $lab->id) }}" method="POST" class="inline-block formEliminarLaboratorio">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 mx-1" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
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

{{-- MODAL Nuevo Examen de Laboratorio --}}
<div id="modalLaboratorio" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <h3 id="tituloModalLaboratorio" class="text-xl font-bold mb-4 text-gray-800 text-center">Nuevo Examen de Laboratorio</h3>
        <form id="formLaboratorio" method="POST" action="{{ route('atenciones-laboratorio.store') }}">
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
                <label for="hemoglobina" class="block text-gray-700 text-sm font-bold mb-2">Hemoglobina (g/dL)</label>
                <input
                    type="number"
                    name="hemoglobina"
                    id="inputHemoglobina"
                    class="w-full border rounded p-2"
                    step="0.01"
                    min="0"
                >
                <div class="error-msg text-red-600 text-sm mt-1">@error('hemoglobina') {{ $message }} @enderror</div>
            </div>

            <div class="mb-4">
                <label for="leucocitos" class="block text-gray-700 text-sm font-bold mb-2">Leucocitos (células/μL)</label>
                <input
                    type="number"
                    name="leucocitos"
                    id="inputLeucocitos"
                    class="w-full border rounded p-2"
                    step="1"
                    min="0"
                >
                <div class="error-msg text-red-600 text-sm mt-1">@error('leucocitos') {{ $message }} @enderror</div>
            </div>

            <div class="mb-4">
                <label for="plaquetas" class="block text-gray-700 text-sm font-bold mb-2">Plaquetas (miles/μL)</label>
                <input
                    type="number"
                    name="plaquetas"
                    id="inputPlaquetas"
                    class="w-full border rounded p-2"
                    step="1"
                    min="0"
                >
                <div class="error-msg text-red-600 text-sm mt-1">@error('plaquetas') {{ $message }} @enderror</div>
            </div>

            <div class="mb-4">
                <label for="colesterol_total" class="block text-gray-700 text-sm font-bold mb-2">Colesterol Total (mg/dL)</label>
                <input
                    type="number"
                    name="colesterol_total"
                    id="inputColesterol"
                    class="w-full border rounded p-2"
                    step="0.01"
                    min="0"
                >
                <div class="error-msg text-red-600 text-sm mt-1">@error('colesterol_total') {{ $message }} @enderror</div>
            </div>

            <div class="mb-4">
                <label for="trigliceridos" class="block text-gray-700 text-sm font-bold mb-2">Triglicéridos (mg/dL)</label>
                <input
                    type="number"
                    name="trigliceridos"
                    id="inputTrigliceridos"
                    class="w-full border rounded p-2"
                    step="0.01"
                    min="0"
                >
                <div class="error-msg text-red-600 text-sm mt-1">@error('trigliceridos') {{ $message }} @enderror</div>
            </div>

            <div class="mb-4">
                <label for="glucosa_ayunas" class="block text-gray-700 text-sm font-bold mb-2">Glucosa Ayunas (mg/dL)</label>
                <input
                    type="number"
                    name="glucosa_ayunas"
                    id="inputGlucosaAyunas"
                    class="w-full border rounded p-2"
                    step="0.01"
                    min="0"
                >
                <div class="error-msg text-red-600 text-sm mt-1">@error('glucosa_ayunas') {{ $message }} @enderror</div>
            </div>

            <div class="mb-4">
                <label for="tipo_muestra" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Muestra</label>
                <select name="tipo_muestra" id="inputTipoMuestra" class="w-full border rounded p-2" required>
                    <option value="sangre">Sangre</option>
                    <option value="orina">Orina</option>
                    <option value="otro">Otro</option>
                </select>
                <div class="error-msg text-red-600 text-sm mt-1">@error('tipo_muestra') {{ $message }} @enderror</div>
            </div>

            <div class="flex justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Guardar</button>
                <button type="button" id="cerrarModalLaboratorio" class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- Librerías --}}
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
    var table = $('#tablaLaboratorio').DataTable({
        stateSave: true,
        dom: '<"flex justify-between items-center mb-2"lf>rt<"flex justify-between items-center mt-4"ip><"clear">',
        ordering: false,
        buttons: [
            { extend: 'excelHtml5', title: 'Exámenes de Laboratorio', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9] }, className: 'd-none' },
            { extend: 'pdfHtml5',   title: 'Exámenes de Laboratorio', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9] }, className: 'd-none' }
        ],
        language: {
            emptyTable: "No hay exámenes registrados",
            zeroRecords: "No se encontraron resultados",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ entradas",
            info: "Mostrando _START_ a _END_ de _TOTAL_ exámenes",
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
        $('#formLaboratorio')[0].reset();
        $('.error-msg').empty();
        $('#modalLaboratorio').removeClass('hidden');
    });
    $('#cerrarModalLaboratorio').click(function() {
        $('#formLaboratorio')[0].reset();
        $('.error-msg').empty();
        $('#modalLaboratorio').addClass('hidden');
    });

    // Mantener modal abierto si hay errores
    @if ($errors->any())
        $('#modalLaboratorio').removeClass('hidden');
    @endif

    // Confirmación de eliminación (sólo para admin)
    $('.formEliminarLaboratorio').on('submit', function(e) {
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
#tablaLaboratorio td.dataTables_empty { text-align: center; font-size: 1rem; color: #6b7280; }
#tablaLaboratorio th, #tablaLaboratorio td { border: 1px solid #e5e7eb; padding: 8px; }
#tablaLaboratorio thead { background-color: #f3f4f6; }
</style>
@endsection
