<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use App\Models\AtencionLaboratorio;
use Illuminate\Http\Request;

class AtencionesLaboratorioController extends Controller
{
    /**
     * Display a listing of los exámenes de laboratorio.
     */
    public function index()
    {
        // Cargamos los exámenes con la relación atención → paciente
        $laboratorios = AtencionLaboratorio::with('atencion.paciente')->get();

        // Cargamos las atenciones para el select del modal
        $atenciones = Atencion::with('paciente')->get();

        return view('atenciones.laboratorio', compact('laboratorios', 'atenciones'));
    }

    /**
     * Store a newly created examen de laboratorio in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'atencion_id'      => 'required|exists:atenciones,id',
            'hemoglobina'      => 'nullable|numeric',
            'leucocitos'       => 'nullable|numeric',
            'plaquetas'        => 'nullable|numeric',
            'colesterol_total' => 'nullable|numeric',
            'trigliceridos'    => 'nullable|numeric',
            'glucosa_ayunas'   => 'nullable|numeric',
            'tipo_muestra'     => 'required|in:sangre,orina,otro',
            // 'estado' se asigna por defecto en la migración
        ]);

        AtencionLaboratorio::create($data);

        return redirect()
            ->route('atenciones-laboratorio.index')
            ->with('success', 'Examen de laboratorio registrado correctamente.');
    }

    /**
     * Marca un examen de laboratorio como 'Atendido'.
     */
    public function atender(AtencionLaboratorio $laboratorio)
    {
        $laboratorio->update(['estado' => 'Atendida']);

        return redirect()
            ->route('atenciones-laboratorio.index')
            ->with('success', 'Examen de laboratorio marcado como atendido.');
    }

    /**
     * Remove the specified examen de laboratorio from storage.
     */
    public function destroy(AtencionLaboratorio $laboratorio)
    {
        $laboratorio->delete();

        return redirect()
            ->route('atenciones-laboratorio.index')
            ->with('success', 'Examen de laboratorio eliminado exitosamente.');
    }
}

