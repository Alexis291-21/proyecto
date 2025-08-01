<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitaMedica extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención)
    protected $table = 'cita_medicas';

    // Atributos que pueden asignarse masivamente
    protected $fillable = [
        'paciente_id',
        'fecha',    // fecha de la cita (solo día)
        'hora',     // hora de la cita (HH:MM)
        'medico_id',
        'estado',
    ];

    // Casteos automáticos de atributos
    protected $casts = [
        'fecha' => 'date',               // Devuelve un Carbon solo con la parte de fecha
    ];

    /**
     * Relación: cada cita pertenece a un paciente.
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Relación: cada cita pertenece a un médico.
     */
    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
