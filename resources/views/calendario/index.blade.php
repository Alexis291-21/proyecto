@extends('main')

@section('title', 'Calendario de Citas')

@section('stylesheet')
    @parent
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
@endsection

@section('javascripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://unpkg.com/tippy.js@6/dist/tippy.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const modal     = document.getElementById('modalDetalle');

        const campos = {
            paciente: document.getElementById('d-paciente'),
            medico:   document.getElementById('d-medico'),
            fecha:    document.getElementById('d-fecha'),
            hora:     document.getElementById('d-hora'),
            estado:   document.getElementById('d-estado'),
        };

        // Inicializa FullCalendar
        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            firstDay: 1,
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día'
            },
            editable: true,
            selectable: true,
            navLinks: true,
            dayMaxEvents: true,

            // Carga eventos desde el backend
            events: {
                url: '{{ route("calendario.eventos") }}',
                method: 'GET'
            },

            eventDidMount(info) {
                tippy(info.el, {
                    content: `
                        <strong>Paciente:</strong> ${info.event.extendedProps.paciente}<br>
                        <strong>Estado:</strong> ${info.event.extendedProps.estado}
                    `,
                    allowHTML: true
                });
            },

            eventClick(info) {
                const p = info.event.extendedProps;
                campos.paciente.textContent = p.paciente;
                campos.medico.textContent   = p.medico;
                campos.fecha.textContent    = p.fecha;
                campos.hora.textContent     = p.hora;
                campos.estado.textContent   = p.estado;
                modal.classList.remove('hidden');
            },

            eventDrop(info) {
                fetch(`/calendario/reprogramar/${info.event.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ start: info.event.start.toISOString() })
                })
                .then(response => {
                    if (!response.ok) info.revert();
                })
                .catch(() => info.revert());
            },
        });

        calendar.render();

        // Cerrar modal
        document.getElementById('cerrarDetalle').onclick =
        document.getElementById('cerrarDetalleFooter').onclick = () => {
            modal.classList.add('hidden');
        };

        // Refresca eventos tras crear nueva cita
        const formCrear = document.getElementById('formCrearCita');
        if (formCrear) {
            formCrear.addEventListener('submit', function(e) {
                e.preventDefault();
                const data = new FormData(formCrear);
                fetch(formCrear.action, {
                    method: formCrear.method,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: data
                })
                .then(resp => resp.json())
                .then(json => {
                    if (json.status === 'ok') {
                        formCrear.reset();
                        calendar.refetchEvents();
                    }
                });
            });
        }
    });
    </script>
@endsection

@section('content')
<div class="container mx-auto p-4">
    <div id="calendar" class="bg-white p-4 rounded-lg shadow"></div>
</div>

{{-- Modal Detalle de Cita --}}
<div id="modalDetalle" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Detalle de la Cita</h3>
            <button id="cerrarDetalle" class="text-gray-600 hover:text-gray-800 text-2xl">&times;</button>
        </div>
        <ul class="space-y-2 text-gray-700">
            <li><strong>Paciente:</strong> <span id="d-paciente">—</span></li>
            <li><strong>Médico:</strong>   <span id="d-medico">—</span></li>
            <li><strong>Fecha:</strong>    <span id="d-fecha">—</span></li>
            <li><strong>Hora:</strong>     <span id="d-hora">—</span></li>
            <li><strong>Estado:</strong>   <span id="d-estado">—</span></li>
        </ul>
        <div class="mt-6 text-right">
            <button id="cerrarDetalleFooter" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Cerrar
            </button>
        </div>
    </div>
</div>
@endsection
