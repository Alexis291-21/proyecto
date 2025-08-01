<?php

// app/Models/Horario.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'medico_id', 'dia',
        'turno_manana_inicio', 'turno_manana_fin',
        'turno_tarde_inicio', 'turno_tarde_fin',
        'estado'
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
