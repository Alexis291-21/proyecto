<?php

namespace App\Mail;

use App\Models\Cita;
use App\Models\CitaMedica;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecordatorioCita extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;
    public $mensaje;

    public function __construct(CitaMedica $cita, string $mensaje)
    {
        $this->cita    = $cita;
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        return $this
            ->subject("Recordatorio de cita médica: {$this->cita->fecha} {$this->cita->hora}")
            ->markdown('emails.recordatorio')
            ->with([
                'paciente' => $this->cita->paciente,
                'cita'     => $this->cita,
                'mensaje'  => $this->mensaje,
            ]);
    }
}
