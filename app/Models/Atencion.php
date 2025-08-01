<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atencion extends Model
{
    use HasFactory;

    protected $table = 'atenciones';

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha_atencion',
        'diagnostico',
        'tratamiento',
        'observaciones',
        'presion_arterial',
        'frecuencia_cardiaca',
        'temperatura',
        'estado',
    ];

    protected $casts = [
        'fecha_atencion' => 'date',
        'frecuencia_cardiaca' => 'integer',
        'temperatura' => 'decimal:1',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
