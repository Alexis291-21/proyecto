<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use App\Models\Paciente;
use App\Models\Medico;
use Illuminate\Http\Request;

class AtencionesController extends Controller
{
    /**
     * Aplica middleware de autenticación a todas las rutas.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar la lista de atenciones (y datos para el modal).
     */
    public function index()
    {
        $atenciones = Atencion::with(['paciente', 'medico'])
                              ->orderBy('fecha_atencion', 'desc')
                              ->get();

        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Medico::orderBy('nombre')->get();

        return view('atenciones.index', compact('atenciones', 'pacientes', 'medicos'));
    }

    /**
     * (Opcional) Mostrar formulario de creación en vista separada.
     */
    public function create()
    {
        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Medico::orderBy('nombre')->get();
        return view('atenciones.create', compact('pacientes', 'medicos'));
    }

    /**
     * Almacenar nueva atención (desde modal o formulario).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id'        => 'required|exists:pacientes,id',
            'medico_id'          => 'required|exists:medicos,id',
            'fecha_atencion'     => 'required|date',
            'diagnostico'        => 'required|string|max:1000',
            'tratamiento'        => 'nullable|string|max:1000',
            'observaciones'      => 'nullable|string|max:1000',
            'presion_arterial'   => 'nullable|string|max:20',
            'frecuencia_cardiaca'=> 'nullable|integer|min:0',
            'temperatura'        => 'nullable|numeric|min:0',
        ]);

        // Asignar estado inicial 'En curso'
        Atencion::create(array_merge($data, ['estado' => 'En curso']));

        return redirect()
            ->route('atenciones.index')
            ->with('success', 'Atención creada correctamente.');
    }

    /**
     * Mostrar formulario de edición en vista separada.
     */
    public function edit(Atencion $atencion)
    {
        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Medico::orderBy('nombre')->get();
        return view('atenciones.edit', compact('atencion', 'pacientes', 'medicos'));
    }

    /**
     * Actualizar datos de una atención.
     */
    public function update(Request $request, Atencion $atencion)
    {
        $data = $request->validate([
            'paciente_id'        => 'required|exists:pacientes,id',
            'medico_id'          => 'required|exists:medicos,id',
            'fecha_atencion'     => 'required|date',
            'diagnostico'        => 'required|string|max:1000',
            'tratamiento'        => 'nullable|string|max:1000',
            'observaciones'      => 'nullable|string|max:1000',
            'presion_arterial'   => 'nullable|string|max:20',
            'frecuencia_cardiaca'=> 'nullable|integer|min:0',
            'temperatura'        => 'nullable|numeric|min:0',
        ]);

        $atencion->update($data);

        return redirect()
            ->route('atenciones.index')
            ->with('success', 'Atención actualizada correctamente.');
    }

    /**
     * Eliminar una atención.
     */
    public function destroy(Atencion $atencion)
    {
        $atencion->delete();

        return redirect()
            ->route('atenciones.index')
            ->with('success', 'Atención eliminada correctamente.');
    }

    /**
     * Marcar una atención como atendida.
     */
    public function marcarAtendida(Atencion $atencion)
    {
        $atencion->update(['estado' => 'Atendida']);
        return back()->with('success', 'Atención marcada como atendida.');
    }
}
