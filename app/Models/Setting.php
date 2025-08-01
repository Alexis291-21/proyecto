<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'nombre_empresa',
        'codigo_postal',
        'telefono',
        'ruc',
        'responsable',
        'logo',
    ];

    protected $appends = ['logo_url'];

    /**
     * Accesor para obtener la URL pública DEL LOGO (o null si no existe).
        */
    public function getLogoUrlAttribute()
    {
        return $this->logo && Storage::disk('public')->exists($this->logo)
            ? Storage::url($this->logo)
            : null;
    }
}
