<?php

namespace App\Http\Controllers;

use App\Models\CitaMedica;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarioController extends Controller
{
    /**
     * Muestra la vista con el calendario.
     */
    public function index()
    {
        // Si el usuario NO es un médico, cargamos lista de médicos para filtrar
        $medicos = Auth::user()->rol !== 'medico'
            ? Medico::orderBy('nombre')->get()
            : collect();

        return view('calendario.index', compact('medicos'));
    }

    /**
     * Devuelve las citas como un array JSON para FullCalendar.
     */
    public function eventos(Request $request)
    {
        $user = Auth::user();
        $hoy  = Carbon::now()->toDateString();

        // Base de la consulta según rol
        if ($user->rol === 'medico') {
            $medId = $user->medico->id ?? 0;
            $query = CitaMedica::where('medico_id', $medId);
        } else {
            if ($request->filled('medico_id')) {
                $query = CitaMedica::where('medico_id', $request->medico_id);
            } else {
                $query = CitaMedica::query();
            }
        }

        // Solo futuras (>= hoy)
        $citas = $query
            ->whereDate('fecha', '>=', $hoy)
            ->with(['paciente', 'medico'])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        // Mapear a FullCalendar
        $events = $citas->map(function($c) {
            $colors = [
                'pendiente'  => '#FBBF24',
                'confirmada' => '#34D399',
                'cancelada'  => '#F87171',
            ];
            return [
                'id'    => $c->id,
                'title' => "{$c->hora} - ".optional($c->paciente)->nombre,
                'start' => "{$c->fecha}T{$c->hora}",
                'color' => $colors[$c->estado] ?? '#3B82F6',
                'extendedProps' => [
                    'medico' => optional($c->medico)->nombre,
                    'estado' => $c->estado,
                ],
            ];
        });

        return response()->json($events);
    }
}
