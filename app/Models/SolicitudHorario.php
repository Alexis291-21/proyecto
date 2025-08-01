<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudHorario extends Model
{
    use HasFactory;

    protected $table = 'solicitud_horarios';

    protected $fillable = [
        'medico_id',
        'solicitante_id',
        'accion',
        'estado',
    ];

    // Relación con el médico
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    // Relación con el usuario que solicitó
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }
}
