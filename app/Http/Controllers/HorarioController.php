<?php
// app/Http/Controllers/HorarioController.php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Medico;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    /**
     * Muestra la vista con el selector de médicos
     */
    public function index()
    {
        $medicos = Medico::all();
        return view('horarios.index', compact('medicos'));
    }

    /**
     * Devuelve por JSON todos los horarios de un médico
     */
    public function getHorarios($medico_id)
    {
        $horarios = Horario::where('medico_id', $medico_id)->get();
        return response()->json($horarios);
    }

    /**
     * Crea un nuevo horario
     * Verifica conflictos antes de guardar: mismo día y misma franja horaria (mañana o tarde).
     */
    public function store(Request $request)
    {
        $request->validate([
            'medico_id'            => 'required|exists:medicos,id',
            'dia'                  => 'required|string',
            'turno_manana_inicio'  => 'nullable|string',
            'turno_manana_fin'     => 'nullable|string',
            'turno_tarde_inicio'   => 'nullable|string',
            'turno_tarde_fin'      => 'nullable|string',
            'estado'               => 'required|boolean',
        ]);

        // Buscar conflictos: mismo día y misma franja (mañana o tarde)
        $dia        = $request->input('dia');
        $tm_inicio  = $request->input('turno_manana_inicio');
        $tm_fin     = $request->input('turno_manana_fin');
        $tt_inicio  = $request->input('turno_tarde_inicio');
        $tt_fin     = $request->input('turno_tarde_fin');

        // Conflicto en franja de mañana (si se enviaron ambos campos)
        if ($tm_inicio && $tm_fin) {
            $conflictoManana = Horario::where('dia', $dia)
                ->where('turno_manana_inicio', $tm_inicio)
                ->where('turno_manana_fin', $tm_fin)
                ->exists();

            if ($conflictoManana) {
                return response()->json([
                    'message' => 'No se permite guardar el turno de mañana: ya hay un médico asignado a esa misma hora y día.'
                ], 422);
            }
        }

        // Conflicto en franja de tarde (si se enviaron ambos campos)
        if ($tt_inicio && $tt_fin) {
            $conflictoTarde = Horario::where('dia', $dia)
                ->where('turno_tarde_inicio', $tt_inicio)
                ->where('turno_tarde_fin', $tt_fin)
                ->exists();

            if ($conflictoTarde) {
                return response()->json([
                    'message' => 'No se permite guardar el turno de tarde: ya hay un médico asignado a esa misma hora y día.'
                ], 422);
            }
        }

        // Si no hay conflictos, crear el horario
        $horario = Horario::create($request->only([
            'medico_id','dia',
            'turno_manana_inicio','turno_manana_fin',
            'turno_tarde_inicio','turno_tarde_fin',
            'estado'
        ]));

        return response()->json($horario);
    }

    /**
     * Actualiza un solo campo o varios de un horario existente
     */
    public function update(Request $request, Horario $horario)
    {
        $data = $request->only([
            'dia',
            'turno_manana_inicio',
            'turno_manana_fin',
            'turno_tarde_inicio',
            'turno_tarde_fin',
            'estado'
        ]);

        $horario->update(array_filter($data, fn($v) => !is_null($v)));

        return response()->json(['message' => 'Horario actualizado correctamente.']);
    }

    /**
     * Actualiza en bloque todos los horarios de un mismo médico
     */
    public function updateMasivo(Request $request)
    {
        $request->validate([
            'medico_id' => 'required|exists:medicos,id',
            'updates'   => 'required|array',
            'updates.*.id'                     => 'required|exists:horarios,id',
            'updates.*.turno_manana_inicio'    => 'nullable|string',
            'updates.*.turno_manana_fin'       => 'nullable|string',
            'updates.*.turno_tarde_inicio'     => 'nullable|string',
            'updates.*.turno_tarde_fin'        => 'nullable|string',
            'updates.*.estado'                 => 'required|boolean',
        ]);

        $medicoId = $request->medico_id;

        foreach ($request->updates as $h) {
            Horario::where('id', $h['id'])
                  ->where('medico_id', $medicoId)
                  ->update([
                      'turno_manana_inicio' => $h['turno_manana_inicio'],
                      'turno_manana_fin'    => $h['turno_manana_fin'],
                      'turno_tarde_inicio'  => $h['turno_tarde_inicio'],
                      'turno_tarde_fin'     => $h['turno_tarde_fin'],
                      'estado'              => $h['estado'],
                  ]);
        }

        return response()->json(['message' => 'Horarios actualizados correctamente.']);
    }
}
