{{-- resources/views/atenciones/index.blade.php --}}

@extends('main')

@section('title')
    Atenciones
@endsection

@section('content')
<div class="w-full p-8 bg-gray-50">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <header class="flex flex-col md:flex-row justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-3xl font-extrabold text-gray-800">Listado de Atenciones</h2>
            @if(in_array(Auth::user()->rol, ['admin','recepcionista']))
            <div class="mt-4 md:mt-0 flex space-x-3">
                <button id="btnExcel" class="flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-file-excel mr-2"></i>Excel
                </button>
                <button id="btnPdf" class="flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-file-pdf mr-2"></i>PDF
                </button>
                <button id="abrirModalCrear" class="flex items-center bg-yellow-500 hover:bg-yellow-400 text-white font-medium py-2 px-4 rounded-lg shadow transition">
                    <i class="fas fa-plus-circle mr-2"></i>Nueva Atención
                </button>
            </div>
            @endif
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
            <table id="tablaAtenciones" class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Médico</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha de Atención</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Diagnóstico</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tratamiento</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Observaciones</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Presión</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Frecuencia</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Temperatura</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                        @if(in_array(Auth::user()->rol, ['admin','recepcionista']))
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($atenciones as $atencion)
                        <tr class="hover:bg-gray-50 transition" data-atencion='@json($atencion)'>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $atencion->paciente->nombre }} {{ $atencion->paciente->apellido_paterno }} {{ $atencion->paciente->apellido_materno }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $atencion->medico->nombre }} {{ $atencion->medico->apellido_paterno }} {{ $atencion->medico->apellido_materno }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $atencion->fecha_atencion->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $atencion->diagnostico }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $atencion->tratamiento }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $atencion->observaciones }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $atencion->presion_arterial }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $atencion->frecuencia_cardiaca }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $atencion->temperatura }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $atencion->estado === 'En curso' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $atencion->estado }}
                                </span>
                            </td>
                            @if(in_array(Auth::user()->rol, ['admin','recepcionista']))
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2 flex justify-center items-center">
                                    @if($atencion->estado === 'En curso')
                                        <form action="{{ route('atenciones.atendida', $atencion->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-semibold mr-2">
                                                Atendida
                                            </button>
                                        </form>
                                    @endif

                                    <button class="btnEditarAtencion text-yellow-500 hover:text-yellow-700" title="Editar">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    <form action="{{ route('atenciones.destroy', $atencion) }}" method="POST" class="formEliminar inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 mx-1" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Crear/Editar Atención --}}
<div id="modalAtencion" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-md shadow-xl w-full max-w-lg max-h-[95vh] overflow-y-auto">
    <div class="bg-gray-800 text-white px-4 py-3 rounded-t-md">
      <h3 id="tituloModalAtencion" class="text-lg font-semibold text-center">Nueva Atención</h3>
    </div>
    <form id="formAtencion" method="POST" action="{{ route('atenciones.store') }}" class="px-4 py-4 space-y-4">
      @csrf
      <input type="hidden" name="_method" id="inputMetodoAtencion" value="POST">

      <div>
        <label class="block text-sm text-gray-700 mb-1">Paciente</label>
        <select name="paciente_id" id="pacienteSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 select2" required>
          <option value="">Seleccionar paciente</option>
          @foreach($pacientes as $pac)
            <option value="{{ $pac->id }}">{{ $pac->nombre }} {{ $pac->apellido_paterno }} {{ $pac->apellido_materno }}</option>
          @endforeach
        </select>
        <div class="error-msg text-red-600 text-sm mt-1"></div>
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Médico</label>
        <select name="medico_id" id="medicoSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 select2" required>
          <option value="">Seleccionar médico</option>
          @foreach($medicos as $med)
            <option value="{{ $med->id }}">{{ $med->nombre }} {{ $med->apellido_paterno }} {{ $med->apellido_materno }}</option>
          @endforeach
        </select>
        <div class="error-msg text-red-600 text-sm mt-1"></div>
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Fecha de Atención</label>
        <input type="date" name="fecha_atencion" id="inputFechaAtencion" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
        <div class="error-msg text-red-600 text-sm mt-1"></div>
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Diagnóstico</label>
        <textarea name="diagnostico" id="inputDiagnostico" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
        <div class="error-msg text-red-600 text-sm mt-1"></div>
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Tratamiento</label>
        <textarea name="tratamiento" id="inputTratamiento" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
        <div class="error-msg text-red-600 text-sm mt-1"></div>
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Observaciones</label>
        <textarea name="observaciones" id="inputObservaciones" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Presión Arterial</label>
        <input type="text" name="presion_arterial" id="inputPresion" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="120/80">
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Frecuencia Cardíaca</label>
        <input type="number" name="frecuencia_cardiaca" id="inputFrecuencia" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="80">
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Temperatura (°C)</label>
        <input type="number" step="0.1" name="temperatura" id="inputTemperatura" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="36.5">
      </div>

      <div class="flex justify-between mt-4">
        <button type="submit" id="btnSubmitAtencion" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md shadow font-semibold">
          Guardar
        </button>
        <button type="button" id="btnCancelarAtencion" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md shadow font-semibold">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

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
$(function(){
  var table = $('#tablaAtenciones').DataTable({
    stateSave: true,
    dom: '<"flex justify-between items-center mb-2"lf>rt<"flex justify-between items-center mt-4"ip><"clear">',
    ordering: false,
    buttons: [
      @if(in_array(Auth::user()->rol, ['admin','recepcionista']))
      { extend: 'excelHtml5', title: null, exportOptions: { columns: [0,1,2,3,4,5,6,7,8] }, className: 'd-none' },
      { extend: 'pdfHtml5', title: 'Atenciones', exportOptions: { columns: [0,1,2,3,4,5,6,7,8] }, className: 'd-none' }
      @endif
    ].filter(Boolean),
    language: {
      emptyTable: 'No hay atenciones registradas',
      zeroRecords: 'No se encontraron resultados',
      processing: 'Procesando...',
      search: 'Buscar:',
      lengthMenu: 'Mostrar _MENU_ entradas',
      info: 'Mostrando _START_ a _END_ de _TOTAL_ atenciones',
      infoEmpty: 'Mostrando 0 atenciones',
      infoFiltered: '(filtrado de _MAX_ atenciones)',
      paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Último' }
    }
  });

  @if(in_array(Auth::user()->rol, ['admin','recepcionista']))
  $('#btnExcel').click(() => table.data().any() && table.button(0).trigger());
  $('#btnPdf').click(() => table.data().any() && table.button(1).trigger());
  @endif

  function initSelect2() {
    $('#pacienteSelect, #medicoSelect').select2({ width: '100%', placeholder: 'Seleccionar', allowClear: true });
  }
  initSelect2();

  $('#abrirModalCrear').click(function() {
    $('#formAtencion')[0].reset();
    $('.error-msg').empty();
    $('#tituloModalAtencion').text('Nueva Atención');
    $('#formAtencion').attr('action','{{ route("atenciones.store") }}');
    $('#inputMetodoAtencion').val('POST');
    $('#btnSubmitAtencion').text('Guardar').removeClass('bg-blue-600 hover:bg-blue-700').addClass('bg-green-600 hover:bg-green-700');
    initSelect2();
    $('#modalAtencion').removeClass('hidden');
  });

  // Handler completo para editar una Atención
  $('.btnEditarAtencion').click(function() {
    // 1) Obtener datos de la fila
    let data = $(this).closest('tr').data('atencion');

    // 2) Resetear formulario y limpiar errores
    $('#formAtencion')[0].reset();
    $('.error-msg').empty();

    // 3) Ajustar método y URL del form
    $('#inputMetodoAtencion').val('PUT');
    $('#formAtencion').attr('action', '/atenciones/' + data.id);

    // 4) Formatear fecha de atención a YYYY‑MM‑DD
    //    data.fecha_atencion puede venir "2025-07-01T00:00:00.000Z" o "2025-07-01"
    let fechaIso = data.fecha_atencion.split('T')[0];
    $('#inputFechaAtencion').val(fechaIso);

    // 5) Rellenar el resto de campos
    $('#inputDiagnostico').val(data.diagnostico);
    $('#inputTratamiento').val(data.tratamiento);
    $('#inputObservaciones').val(data.observaciones);
    $('#inputPresion').val(data.presion_arterial);
    $('#inputFrecuencia').val(data.frecuencia_cardiaca);
    $('#inputTemperatura').val(data.temperatura);

    // 6) Inicializar Select2 y preseleccionar paciente/medico
    $('#pacienteSelect').val(data.paciente_id).trigger('change');
    $('#medicoSelect').val(data.medico_id).trigger('change');

    // 7) Ajustar texto y estilo del botón de submit
    $('#btnSubmitAtencion')
      .text('Actualizar')
      .removeClass('bg-green-600 hover:bg-green-700')
      .addClass('bg-blue-600 hover:bg-blue-700');

    // 8) Ajustar título del modal y mostrarlo
    $('#tituloModalAtencion').text('Editar Atención');
    $('#modalAtencion').removeClass('hidden');
  });

  $('#tablaAtenciones').on('click', '.formEliminar button', function(e) {
    e.preventDefault();
    let f = $(this).closest('form');
    Swal.fire({
      title: '¿Estás seguro?',
      text: '¡Esta acción no se puede deshacer!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e3342f',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(res => res.isConfirmed && f.submit());
  });

  $('#btnCancelarAtencion').click(function() {
    $('#modalAtencion').addClass('hidden');
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

  @if($errors->any())
    $('#modalAtencion').removeClass('hidden');
  @endif
});
</script>

<style>
#tablaAtenciones td.dataTables_empty { text-align: center; font-size: 1rem; color: #6b7280; }
#tablaAtenciones th, #tablaAtenciones td { border: 1.5px solid #9CA3AF; padding: 8px; }
#tablaAtenciones thead { background-color: #f3f4f6; }
</style>
@endsection
