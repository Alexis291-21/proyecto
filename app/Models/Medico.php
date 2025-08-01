<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    // Definir los campos que pueden ser asignados en masa (fillable)
    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'especialidad',
        'telefono',
        'dni',
        'email',
        'disponibilidad',
    ];

    /**
     * Relación: Un médico puede tener muchas citas médicas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function citas()
    {
        return $this->hasMany(CitaMedica::class);
    }
}
