<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'edad',
        'genero',
        'telefono',
        'direccion',
        'dni',
        'fecha_nacimiento',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Un paciente puede tener muchas citas médicas.
     */
    public function citas()
    {
        return $this->hasMany(CitaMedica::class);
    }
}
