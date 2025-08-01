<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    use HasFactory;

    protected $table = 'calendarios';

    protected $fillable = [
        'cita_medica_id',
        'titulo',
        'start',
        'end',
        'color',
        'tooltip',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end'   => 'datetime',
    ];

    public function citaMedica()
    {
        return $this->belongsTo(CitaMedica::class, 'cita_medica_id');
    }
}
