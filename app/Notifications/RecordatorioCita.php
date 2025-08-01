<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\CitaMedica;

class RecordatorioCita extends Notification
{
    use Queueable;

    protected $cita;

    public function __construct(CitaMedica $cita)
    {
        $this->cita = $cita;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Recordatorio de Cita Médica')
            ->greeting('Hola ' . $this->cita->paciente->nombre)
            ->line('Este es un recordatorio de su cita médica.')
            ->line('📅 Fecha de la cita: ' . $this->cita->fecha_cita)
            ->line('¡Por favor llegue con anticipación!')
            ->salutation('Saludos cordiales.');
    }
}
