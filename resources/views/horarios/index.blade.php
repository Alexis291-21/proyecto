{{-- resources/views/horarios/index.blade.php --}}

@extends('main')

@section('title')
    Horarios
@endsection

@section('content')
<div class="w-full p-8 bg-gray-50">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <header class="flex flex-wrap md:flex-nowrap justify-between md:justify-start items-center gap-2 md:gap-4 mb-6 border-b pb-4">
            <div class="flex items-center gap-2 w-full">
                <h1 class="text-2xl font-extrabold text-gray-700 whitespace-nowrap">Módulo de Horarios</h1>

                {{-- Nuevo horario solo si NO es médico --}}
                @if(Auth::user()->rol !== 'medico')
                    <button
                        class="flex items-center bg-yellow-400 hover:bg-yellow-400 text-white font-semibold py-2 px-4 rounded-md shadow-lg transition-colors whitespace-nowrap"
                        onclick="abrirModal()"
                    >
                        <i class="fas fa-plus-circle mr-2"></i>Nuevo horario
                    </button>
                @endif

                <select
                    id="medico_id"
                    class="select2-custom border border-gray-100 rounded-lg text-xs w-24"
                >
                    <option value="">Seleccione un médico</option>
                    @foreach ($medicos as $medico)
                        <option value="{{ $medico->id }}">{{ $medico->nombre }} {{ $medico->apellido_paterno }} {{ $medico->apellido_materno }}</option>
                    @endforeach
                </select>

                {{-- Guardar Cambios solo si NO es médico --}}
                @if(Auth::user()->rol !== 'medico')
                    <button
                        type="button"
                        class="flex items-center bg-green-500 hover:bg-green-400 text-white font-semibold py-2 px-4 rounded-md shadow-lg transition-colors whitespace-nowrap ml-auto"
                        onclick="guardarCambios()"
                    >
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                @endif
            </div>
        </header>

        <div class="overflow-x-auto">
            <table class="w-full divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Día</th>
                        <th class="px-12 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                        <th class="px-12 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Turno Mañana</th>
                        <th class="px-12 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Turno Tarde</th>
                    </tr>
                </thead>
                <tbody id="tabla-horarios" class="bg-white divide-y divide-gray-200">
                    {{-- Se cargarán dinámicamente --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Agregar/Editar Horario (estilo modal médicos) --}}
<div id="modal-horario" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-md shadow-xl w-full max-w-md">
        {{-- Encabezado oscuro sin X --}}
        <div class="bg-gray-800 text-white px-6 py-4 rounded-t-md">
            <h3 class="text-lg font-semibold text-center">Detalle de la hora</h3>
        </div>
        {{-- Formulario --}}
        <form id="form-horario" class="px-6 py-6">
            <div class="mb-4">
                <label for="dia" class="block text-sm text-gray-700 mb-1">Día</label>
                <select id="dia" class="w-full p-2 border border-gray-300 rounded-lg">
                    <option value="">Seleccionar</option>
                    <option value="Domingo">Domingo</option>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                    <option value="Sábado">Sábado</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-700 mb-1">Turno Mañana</label>
                <div class="flex flex-col gap-2">
                    <input
                        id="turno_manana_inicio"
                        name="turno_manana_inicio"
                        type="text"
                        placeholder="Inicio..."
                        class="horario-time border rounded p-2 h-10 pr-4 cursor-pointer w-full"
                        readonly
                    />
                    <input
                        id="turno_manana_fin"
                        name="turno_manana_fin"
                        type="text"
                        placeholder="Fin..."
                        class="horario-time border rounded p-2 h-10 pr-4 cursor-pointer w-full"
                        readonly
                    />
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-700 mb-1">Turno Tarde</label>
                <div class="flex flex-col gap-2">
                    <input
                        id="turno_tarde_inicio"
                        name="turno_tarde_inicio"
                        type="text"
                        placeholder="Inicio..."
                        class="horario-time border rounded p-2 h-10 pr-4 cursor-pointer w-full"
                        readonly
                    />
                    <input
                        id="turno_tarde_fin"
                        name="turno_tarde_fin"
                        type="text"
                        placeholder="Fin..."
                        class="horario-time border rounded p-2 h-10 pr-4 cursor-pointer w-full"
                        readonly
                    />
                </div>
            </div>
            <div class="flex items-center mb-4">
                <label for="estado_default" class="mr-2 text-gray-700">Estado</label>
                <input type="checkbox" id="estado_default" class="h-5 w-5">
            </div>
            <div class="flex justify-between">
                <button
                    type="button"
                    onclick="registrarHorario()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow font-semibold"
                >
                    Registrar
                </button>
                <button
                    type="button"
                    onclick="cerrarModal()"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md shadow font-semibold"
                >
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
$(function() {
    $('#medico_id').select2({ placeholder: "Seleccione un médico", width: '100%' });

    const esMedico = "{{ Auth::user()->rol }}" === "medico";
    const fpOpts = { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true };

    if (!esMedico) {
        flatpickr('#turno_manana_inicio', fpOpts);
        flatpickr('#turno_manana_fin',    fpOpts);
        flatpickr('#turno_tarde_inicio',  fpOpts);
        flatpickr('#turno_tarde_fin',     fpOpts);
    } else {
        $('.horario-time').attr('disabled', true);
    }

    // Cargar último médico seleccionado
    const saved = localStorage.getItem('medicoSeleccionado');
    if (saved) {
        $('#medico_id').val(saved).trigger('change');
        cargarHorarios();
    }

    $('#medico_id').change(function() {
        localStorage.setItem('medicoSeleccionado', this.value);
        cargarHorarios();
    });
});

function abrirModal() { $('#modal-horario').removeClass('hidden'); }
function cerrarModal() {
    $('#modal-horario').addClass('hidden');
    $('#form-horario')[0].reset();
}

function registrarHorario() {
    const dia = $('#dia').val(), medId = $('#medico_id').val();
    if (!medId || !dia) return alert('Debe seleccionar un médico y un día.');

    fetch('{{ route('horarios.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({
            medico_id: medId,
            dia,
            turno_manana_inicio: $('#turno_manana_inicio').val(),
            turno_manana_fin:    $('#turno_manana_fin').val(),
            turno_tarde_inicio:  $('#turno_tarde_inicio').val(),
            turno_tarde_fin:     $('#turno_tarde_fin').val(),
            estado: $('#estado_default').is(':checked') ? 1 : 0
        })
    })
    .then(async response => {
        if (!response.ok) {
            const errorData = await response.json();
            return alert(errorData.message || 'Error al registrar el horario.');
        }
        cerrarModal();
        response.json().then(h => appendFila(h));
    })
    .catch(() => {
        alert('Error en la comunicación con el servidor.');
    });
}

function cargarHorarios() {
    const medId = $('#medico_id').val();
    if (!medId) return;
    fetch(`/horarios/${medId}`)
        .then(r => r.json())
        .then(data => {
            const $tb = $('#tabla-horarios').empty();
            data.forEach(h => appendFila(h));
        });
}

function appendFila(h) {
    const esMedico = "{{ Auth::user()->rol }}" === "medico";
    const toggle = `
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer" ${h.estado ? 'checked' : ''} onchange="cambiarEstado(${h.id}, this.checked); toggleColor(this);">
            <div class="w-11 h-6 ${h.estado ? 'bg-purple-600' : 'bg-gray-300'} rounded-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
        </label>`;
    const $row = $(`
        <tr data-id="${h.id}">
            <td class="px-6 py-4">${h.dia}</td>
            <td class="px-12 py-4">${toggle}</td>
            <td class="px-12 py-4">
                <input value="${h.turno_manana_inicio||''}" class="horario-time border rounded p-2 h-10 pr-4 cursor-pointer w-48" readonly/>
                -
                <input value="${h.turno_manana_fin||''}"    class="horario-time border rounded p-2 h-10 pr-4 cursor-pointer w-48" readonly/>
            </td>
            <td class="px-12 py-4">
                <input value="${h.turno_tarde_inicio||''}" class="horario-time border rounded p-2 h-10 pr-4 cursor-pointer w-48" readonly/>
                -
                <input value="${h.turno_tarde_fin||''}"    class="horario-time border rounded p-2 h-10 pr-4 cursor-pointer w-48" readonly/>
            </td>
        </tr>
    `);
    $('#tabla-horarios').append($row);

    // Inicializar flatpickr en inputs nuevos si no es médico
    if (!esMedico) {
        $row.find('input.horario-time').each((_, inp) => {
            flatpickr(inp, {
                enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true,
                onChange: (_, val) => {
                    const tr = inp.closest('tr'),
                          id = tr.dataset.id,
                          campos = ['turno_manana_inicio','turno_manana_fin','turno_tarde_inicio','turno_tarde_fin'],
                          idx = Array.from(tr.querySelectorAll('input.horario-time')).indexOf(inp);
                    actualizarHorario(id, campos[idx], val);
                }
            });
        });
    }
}

function actualizarHorario(id, campo, val) {
    fetch(`/horarios/${id}/actualizar`, {
        method: 'PATCH',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({ [campo]: val })
    }).catch(console.error);
}

function cambiarEstado(id, est) {
    actualizarHorario(id, 'estado', est ? 1 : 0);
}

function toggleColor(chk) {
    const track = chk.nextElementSibling;
    track.classList.toggle('bg-purple-600', chk.checked);
    track.classList.toggle('bg-gray-300', !chk.checked);
}

function guardarCambios() {
    const medId = $('#medico_id').val();
    if (!medId) return alert('Seleccione antes un médico.');

    const updates = [];
    $('#tabla-horarios tr[data-id]').each((_, tr) => {
        const id = tr.dataset.id;
        const inputs = tr.querySelectorAll('input.horario-time');
        const vals = Array.from(inputs).map(i => i.value || null);
        const estado = tr.querySelector('input[type=checkbox]').checked ? 1 : 0;
        updates.push({ id, turno_manana_inicio: vals[0], turno_manana_fin: vals[1], turno_tarde_inicio: vals[2], turno_tarde_fin: vals[3], estado });
    });

    if (!updates.length) return alert('No hay horarios para actualizar.');

    fetch('{{ route('horarios.updateMasivo') }}', {
        method: 'PUT',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({ medico_id: medId, updates })
    })
    .then(res => {
        if (!res.ok) throw new Error();
        alert('Cambios guardados correctamente.');
    })
    .catch(() => alert('Error al guardar los cambios.'));
}
</script>

<style>
.select2-container--default .select2-selection--single {
    height: 38px!important;
    padding: 6px 205px;
    font-size: .875rem;
}
.select2-container--default .select2-selection__rendered {
    text-align: center;
}
.select2-container--default .select2-results__option {
    text-align: center !important;
}
</style>
@endsection
