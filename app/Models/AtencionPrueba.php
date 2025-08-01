<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtencionPrueba extends Model
{
    use HasFactory;

    protected $table = 'atenciones_pruebas';

    protected $fillable = [
        'atencion_id',
        'glucosa',
        'vih',
        'embarazo',
        'covid_19',
        'tipo_muestra',
        'estado',
    ];

    public function atencion()
    {
        return $this->belongsTo(Atencion::class);
    }
}
