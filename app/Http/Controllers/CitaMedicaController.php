<?php

namespace App\Http\Controllers;

use App\Models\CitaMedica;
use App\Models\Paciente;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CitaMedicaController extends Controller
{
    /**
     * Muestra la lista de citas, permitiendo filtrar por médico (medico_id).
     * - Administrador y Recepcionista pueden ver todas las citas o filtrar por un médico concreto.
     * - Médico ve todas sus citas (o las del “medico_id” si se provee), ordenando primero las confirmadas.
     */
    public function index(Request $request)
    {
        $perPage        = $request->input('perPage', 10);
        $hoy            = Carbon::now()->toDateString();
        $pacientes      = Paciente::orderBy('nombre')->get();
        $medicos        = Medico::orderBy('nombre')->get();
        $user           = Auth::user();
        $filtroMedicoId = $request->input('medico_id', null);

        // Construir la query base de CitaMedica según rol y/o filtro GET:
        if ($user->rol === 'medico') {
            $medicoVinculado = $user->medico;
            $filtrarId = $filtroMedicoId ?: ($medicoVinculado->id ?? null);

            if ($filtrarId) {
                $citasQuery = CitaMedica::where('medico_id', $filtrarId)
                    ->with(['paciente', 'medico']);
            } else {
                $citasQuery = CitaMedica::whereRaw('0 = 1');
            }
        } else {
            // Admin o Recepcionista
            if ($filtroMedicoId) {
                $citasQuery = CitaMedica::where('medico_id', $filtroMedicoId)
                    ->with(['paciente', 'medico']);
            } else {
                $citasQuery = CitaMedica::with(['paciente', 'medico']);
            }
        }

        // Paginación de citas
        if ($user->rol === 'medico') {
            $citas = $citasQuery
                ->orderByRaw("estado = 'confirmada' desc")
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'desc')
                ->paginate($perPage)
                ->appends([
                    'perPage'   => $perPage,
                    'medico_id' => $filtroMedicoId
                ]);
        } else {
            $citas = $citasQuery
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'desc')
                ->paginate($perPage)
                ->appends([
                    'perPage'   => $perPage,
                    'medico_id' => $filtroMedicoId
                ]);
        }

        // Obtener próximas citas
        if ($user->rol === 'medico') {
            $filtrarIdProximas = $filtroMedicoId ?: ($user->medico->id ?? null);
            if ($filtrarIdProximas) {
                $proximasCitas = CitaMedica::with(['paciente', 'medico'])
                    ->where('medico_id', $filtrarIdProximas)
                    ->whereDate('fecha', '>=', $hoy)
                    ->orderByRaw("estado = 'confirmada' desc")
                    ->orderBy('fecha', 'asc')
                    ->orderBy('hora', 'asc')
                    ->get();
            } else {
                $proximasCitas = collect();
            }
        } else {
            if ($filtroMedicoId) {
                $proximasCitas = CitaMedica::with(['paciente', 'medico'])
                    ->where('medico_id', $filtroMedicoId)
                    ->whereDate('fecha', '>=', $hoy)
                    ->orderBy('fecha', 'asc')
                    ->orderBy('hora', 'asc')
                    ->get();
            } else {
                $proximasCitas = CitaMedica::with(['paciente', 'medico'])
                    ->whereDate('fecha', '>=', $hoy)
                    ->orderBy('fecha', 'asc')
                    ->orderBy('hora', 'asc')
                    ->get();
            }
        }

        return view('citas.index', compact(
            'citas',
            'perPage',
            'pacientes',
            'medicos',
            'proximasCitas',
            'filtroMedicoId'
        ));
    }

    public function create()
    {
        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Medico::orderBy('nombre')->get();
        return view('citas.index', compact('pacientes', 'medicos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha'       => 'required|date',
            'hora'        => 'required|date_format:H:i',
            'medico_id'   => 'required|exists:medicos,id',
        ]);

        CitaMedica::create([
            'paciente_id' => $validated['paciente_id'],
            'fecha'       => $validated['fecha'],
            'hora'        => $validated['hora'],
            'medico_id'   => $validated['medico_id'],
            'estado'      => 'pendiente',
        ]);

        return redirect()
            ->route('citas.index')
            ->with('success', 'Cita médica creada correctamente.');
    }

    public function edit($id)
    {
        $cita      = CitaMedica::findOrFail($id);
        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Medico::orderBy('nombre')->get();
        return view('citas.index', compact('cita', 'pacientes', 'medicos'));
    }

    public function update(Request $request, $id)
    {
        $cita = CitaMedica::findOrFail($id);

        $validated = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha'       => 'required|date',
            'hora'        => 'required|date_format:H:i',
            'medico_id'   => 'required|exists:medicos,id',
            'estado'      => 'required|in:pendiente,confirmada,cancelada',
        ]);

        $cita->update([
            'paciente_id' => $validated['paciente_id'],
            'fecha'       => $validated['fecha'],
            'hora'        => $validated['hora'],
            'medico_id'   => $validated['medico_id'],
            'estado'      => $validated['estado'],
        ]);

        return redirect()
            ->route('citas.index')
            ->with('success', 'Cita médica actualizada correctamente.');
    }

    public function destroy($id)
    {
        $cita = CitaMedica::findOrFail($id);
        $cita->delete();

        return redirect()
            ->route('citas.index')
            ->with('success', 'Cita médica eliminada correctamente.');
    }
}
