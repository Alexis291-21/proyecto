{{-- resources/views/citas/index.blade.php --}}
@extends('main')

@section('title', 'Citas Médicas')

@section('content')
<div class="w-full p-8 bg-gray-50">
  <div class="bg-white shadow-lg rounded-lg p-6">

    <header class="flex flex-col md:flex-row justify-between items-center mb-6 border-b pb-4">
      <h2 class="text-3xl font-extrabold text-gray-800">Lista de Citas Médicas</h2>

      <div class="mt-4 md:mt-0 flex items-center space-x-3">
        @if(Auth::user()->rol === 'medico')
          <div class="relative w-64">
            <select id="filtroMedico" class="w-full border rounded-lg p-2 h-10 select2" style="width:100%">
              <option value="">Todos</option>
              @foreach($medicos as $med)
                <option value="{{ $med->id }}" @if(request()->input('medico_id') == $med->id) selected @endif>
                  {{ $med->nombre }}
                </option>
              @endforeach
            </select>
          </div>
        @endif

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

        @if(in_array(Auth::user()->rol, ['recepcionista','admin']))
          <button id="btnExcel" class="flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
            <i class="fas fa-file-excel mr-2"></i>Excel
          </button>
          <button id="btnPdf" class="flex items-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg shadow transition">
            <i class="fas fa-file-pdf mr-2"></i>PDF
          </button>
          <button id="abrirModalCrear" class="flex items-center bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg shadow transition">
            <i class="fas fa-plus-circle mr-2"></i>Agregar Cita
          </button>
        @endif
      </div>
    </header>

    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-md bg-red-100 text-red-800 border border-red-300 shadow">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
      <table id="tablaCitas" class="w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Paciente</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Médico</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hora</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
            @if(in_array(Auth::user()->rol, ['recepcionista','admin']))
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
            @endif
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @foreach($citas as $cita)
            <tr class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $cita->paciente ? "{$cita->paciente->nombre} {$cita->paciente->apellido_paterno} {$cita->paciente->apellido_materno}": '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $cita->medico ? "{$cita->medico->nombre} {$cita->medico->apellido_paterno} {$cita->medico->apellido_materno}": '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ \Carbon\Carbon::parse($cita->fecha)->format('d-m-Y') }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $cita->hora }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium
                  {{ $cita->estado=='pendiente' ? 'bg-yellow-200 text-yellow-800' : ($cita->estado=='confirmada'? 'bg-green-200 text-green-800':'bg-red-200 text-red-800') }}">
                  {{ ucfirst($cita->estado) }}
                </span>
              </td>
              @if(in_array(Auth::user()->rol, ['recepcionista','admin']))
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                  <a href="javascript:void(0)" data-editar='@json($cita)' class="text-yellow-500 hover:text-yellow-700" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                  <form action="{{ route('citas.destroy', $cita) }}" method="POST" class="inline formEliminar">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                  </form>
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">
        {{ $citas->links() }}
      </div>
    </div>
  </div>
</div>

{{-- Modal Crear/Editar Cita --}}
<div id="modalCita" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-md shadow-xl w-full max-w-md">
    <div class="bg-purple-700 text-white px-6 py-4 rounded-t-md">
      <h3 id="tituloModal" class="text-lg font-semibold text-center">Agregar Cita</h3>
    </div>
    <form id="formCita" method="POST" action="{{ route('citas.store') }}" class="px-6 py-6">
      @csrf
      <input type="hidden" name="_method" id="inputMetodo" value="POST">

      <div class="space-y-4">
        <div>
          <label class="block text-sm text-gray-700 mb-1">Paciente</label>
          <select name="paciente_id" id="pacienteSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-300 select2" required>
            <option value="">Seleccionar paciente</option>
            @foreach($pacientes as $pac)
              <option value="{{ $pac->id }}">{{ "{$pac->nombre} {$pac->apellido_paterno} {$pac->apellido_materno}" }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">Médico</label>
          <select name="medico_id" id="medicoSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-300 select2" required>
            <option value="">Seleccionar médico</option>
            @foreach($medicos as $med)
              <option value="{{ $med->id }}">{{ "{$med->nombre} {$med->apellido_paterno} {$med->apellido_materno}" }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">Fecha</label>
          <input type="date" name="fecha" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-300" required>
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">Hora</label>
          <div class="relative">
            <input id="horaPicker" name="hora" type="text" readonly placeholder="Seleccionar hora" class="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:ring-2 focus:ring-blue-300" required>
            <i id="iconHora" class="far fa-clock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-600 cursor-pointer"></i>
          </div>
        </div>
        <div id="estadoContainer" class="hidden">
          <label class="block text-sm text-gray-700 mb-1">Estado</label>
          <select name="estado" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-300">
            <option value="pendiente">Pendiente</option>
            <option value="confirmada">Confirmada</option>
            <option value="cancelada">Cancelada</option>
          </select>
        </div>
      </div>

      <div class="flex justify-between mt-6">
        <button type="submit" id="btnGuardarCita" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow font-semibold">Guardar</button>
        <button type="button" id="cerrarModalCita" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md shadow font-semibold">Cancelar</button>
      </div>
    </form>
  </div>
</div>

{{-- Librerías y estilos --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet"/>

<style>
  /* Cuadros estilo Médicos */
  #tablaCitas td.dataTables_empty { text-align:center; font-size:1rem; color:#6b7280; }
  #tablaCitas th, #tablaCitas td { border:1.5px solid #9CA3AF; padding:8px; }
  #tablaCitas thead { background-color:#f3f4f6; }
  /* Select2 */
  .select2-container--default .select2-selection--single {
    height:2.5rem!important; padding:0.5rem 0.75rem!important; border-radius:0.375rem!important; border:1px solid #e5e7eb!important;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:1.5rem!important; }
  .select2-container--default .select2-selection--single .select2-selection__arrow { height:2.5rem!important; }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
$(document).ready(function() {
  // DataTable
  var table = $('#tablaCitas').DataTable({
    stateSave: true,
    dom: '<"flex justify-between mb-2"lf>rt<"flex justify-between mt-4"ip><"clear">',
    ordering: false,
    buttons: [
      {
        extend: 'excelHtml5',
        title: '',
        filename: 'Citas_Medicas',
        exportOptions: { columns: [0,1,2,3,4] },
        className: 'd-none',
        customizeData: d => d.header[0][0] = 'Citas Médicas'
      },
      {
        extend: 'pdfHtml5',
        title: 'Citas Médicas',
        filename: 'Citas_Medicas',
        exportOptions: { columns: [0,1,2,3,4] },
        className: 'd-none',
        customize: doc => doc.styles.title = { alignment:'center', fontSize:16, margin:[0,0,0,10] }
      }
    ],
    language: {
      emptyTable: "No hay citas registradas",
      zeroRecords: "No se encontraron citas",
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ citas",
      info: "Mostrando _START_ a _END_ de _TOTAL_ citas",
      infoEmpty: "Mostrando 0 citas",
      infoFiltered: "(filtrado de _MAX_ citas)",
      paginate: { first:"Primero", previous:"Anterior", next:"Siguiente", last:"Último" }
    }
  });
  var btnExcel = table.button(0), btnPdf = table.button(1);
  $('#btnExcel').click(() => table.data().any() && btnExcel.trigger());
  $('#btnPdf').click(() => table.data().any() && btnPdf.trigger());

  // Select2 & Flatpickr
  function initSelect2(){
    $('#pacienteSelect, #medicoSelect').select2({ width:'100%', placeholder:'Seleccione', allowClear:true });
    $('#filtroMedico').select2({ width:'100%', placeholder:'Filtrar médico', allowClear:true });
  }
  initSelect2();
  var horaFp = flatpickr('#horaPicker', {
    enableTime: true,
    noCalendar: true,
    dateFormat: 'H:i',
    time_24hr: true,
    minuteIncrement: 1,
    clickOpens: false
  });
  $('#horaPicker, #iconHora').on('click', () => horaFp.open());

  // filtro médico
  $('#filtroMedico').on('change', function(){
    var m = $(this).val();
    window.location = m
      ? `{{ route('citas.index') }}?medico_id=${m}`
      : `{{ route('citas.index') }}`;
  });

  // Abrir Crear
  $('#abrirModalCrear').click(function(){
    $('#formCita')[0].reset();
    $('#inputMetodo').val('POST');
    $('#formCita').attr('action', '{{ route('citas.store') }}');
    $('#estadoContainer').addClass('hidden');
    $('#tituloModal').text('Agregar Cita');
    $('#btnGuardarCita')
      .text('Guardar')
      .removeClass('bg-blue-600 hover:bg-blue-700')
      .addClass('bg-green-600 hover:bg-green-700');
    initSelect2();
    horaFp.clear();
    $('#modalCita').removeClass('hidden');
  });

  // Abrir Editar
  $('#tablaCitas').on('click', 'a[data-editar]', function(){
    var c = $(this).data('editar');

    // 1) Método y URL
    $('#inputMetodo').val('PUT');
    $('#formCita').attr('action', `/citas/${c.id}`);

    // 2) Selects de paciente y médico
    $('#pacienteSelect').val(c.paciente_id).trigger('change');
    $('#medicoSelect').val(c.medico_id).trigger('change');

    // 3) Conversión de fecha a ISO (YYYY-MM-DD)
    var fechaIso;
    if (/^\d{4}-\d{2}-\d{2}/.test(c.fecha)) {
      fechaIso = c.fecha.substr(0,10);
    } else if (c.fecha.indexOf('/') > -1) {
      var p = c.fecha.split('/');
      fechaIso = p[2] + '-' + p[1].padStart(2,'0') + '-' + p[0].padStart(2,'0');
    } else {
      fechaIso = c.fecha;
    }
    $('input[name="fecha"]').val(fechaIso);

    // 4) Mostrar el selector de estado y seleccionar su valor
    $('#estadoContainer').removeClass('hidden');
    $('select[name="estado"]').val(c.estado);

    // 5) Cambiar textos/colores del botón y título
    $('#tituloModal').text('Editar Cita');
    $('#btnGuardarCita')
      .text('Actualizar')
      .removeClass('bg-green-600')
      .addClass('bg-blue-600 hover:bg-blue-700');

    // 6) Hora y apertura de modal
    horaFp.setDate(c.hora, false, 'H:i');
    initSelect2();
    $('#modalCita').removeClass('hidden');
  });

  // Eliminar confirm
  $(document).on('submit', '.formEliminar', function(e){
    e.preventDefault();
    var f = this;
    Swal.fire({
      title: '¿Estás seguro?',
      text: '¡Esta acción no se puede deshacer!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e3342f',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(r => r.isConfirmed && f.submit());
  });

  // Cerrar modal Crear/Editar Cita al pulsar “Cancelar”
  $(document).on('click', '#cerrarModalCita', function(e) {
    e.preventDefault();
    $('#modalCita').addClass('hidden');
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
});
</script>
@endsection
