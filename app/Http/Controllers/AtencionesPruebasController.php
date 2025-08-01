<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use App\Models\AtencionPrueba;
use Illuminate\Http\Request;

class AtencionesPruebasController extends Controller
{
    /**
     * Display a listing of las pruebas rápidas.
     */
    public function index()
    {
        // Cargamos las pruebas con la relación atencion → paciente
        $pruebas = AtencionPrueba::with('atencion.paciente')->get();

        // Cargamos las atenciones para el select del modal
        $atenciones = Atencion::with('paciente')->get();

        return view('atenciones.pruebas', compact('pruebas', 'atenciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de la nueva prueba
        $data = $request->validate([
            'atencion_id'  => 'required|exists:atenciones,id',
            'glucosa'      => 'nullable|string|max:100',
            'vih'          => 'nullable|in:positivo,negativo',
            'embarazo'     => 'nullable|in:positivo,negativo',
            'covid_19'     => 'nullable|in:positivo,negativo',
            'tipo_muestra' => 'required|in:sangre,hisopado,orina,otro',
            // 'estado' se asigna por defecto en la migración
        ]);

        AtencionPrueba::create($data);

        return redirect()
            ->route('atenciones-pruebas.index')
            ->with('success', 'Prueba rápida registrada correctamente.');
    }

    /**
     * Marca una prueba como 'Atendida'.
     */
    public function atender(AtencionPrueba $prueba)
    {
        $prueba->update(['estado' => 'Atendida']);

        return redirect()
            ->route('atenciones-pruebas.index')
            ->with('success', 'Prueba marcada como atendida.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AtencionPrueba $prueba)
    {
        $prueba->delete();

        return redirect()
            ->route('atenciones-pruebas.index')
            ->with('success', 'Prueba rápida eliminada exitosamente.');
    }
}
