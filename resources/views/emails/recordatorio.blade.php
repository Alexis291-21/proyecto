{{-- resources/views/emails/recordatorio.blade.php --}}

@component('mail::message')
# Hola {{ $paciente->nombre }}

{!! nl2br(e($mensaje)) !!}

**Detalles de tu cita**
- **Fecha:** {{ $cita->fecha }}
- **Hora:**  {{ $cita->hora }}
- **Médico:** {{ optional($cita->medico)->nombre ?? '—' }}

@component('mail::button', ['url' => url('/mis-citas')])
Ver mis próximas citas
@endcomponent

Gracias por confiar en nosotros,
{{ config('app.name') }}
@endcomponent
