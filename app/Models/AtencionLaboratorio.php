<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AtencionLaboratorio extends Model
{
    use HasFactory;

    protected $table = 'atenciones_laboratorio';

    // Campos que permitimos asignar masivamente
    protected $fillable = [
        'atencion_id',
        'hemoglobina',         // Hemoglobina (g/dL)
        'leucocitos',          // Leucocitos (células/μL)
        'plaquetas',           // Plaquetas (miles/μL)
        'colesterol_total',    // Colesterol Total (mg/dL)
        'trigliceridos',       // Triglicéridos (mg/dL)
        'glucosa_ayunas',      // Glucosa Ayunas (mg/dL)
        'tipo_muestra',        // Tipo Muestra (sangre, orina, otro)
        'estado',              // Estado (por ejemplo: 'En curso', 'Atendida', etc.)
    ];

    /**
     * Relación con la atención médica a la que pertenecen estos resultados.
     */
    public function atencion()
    {
        return $this->belongsTo(Atencion::class, 'atencion_id');
    }
}
