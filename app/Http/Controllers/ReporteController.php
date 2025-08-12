<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Mostrar el reporte diario de citas confirmadas y canceladas,
     * incluyendo el nombre completo del paciente por cada fila (paciente, fecha, count).
     */
    public function index()
    {
        // Confirmadas por paciente y día
        $citas_confirmadas = DB::table('cita_medicas')
            ->join('pacientes', 'cita_medicas.paciente_id', '=', 'pacientes.id')
            ->select(
                DB::raw("CONCAT(pacientes.nombre, ' ', pacientes.apellido_paterno, ' ', pacientes.apellido_materno) as paciente"),
                DB::raw('DATE(cita_medicas.fecha) as fecha'),
                DB::raw('COUNT(*) as count')
            )
            ->where('cita_medicas.estado', 'confirmada')
            ->groupBy(
                'pacientes.id',
                'pacientes.nombre',
                'pacientes.apellido_paterno',
                'pacientes.apellido_materno',
                DB::raw('DATE(cita_medicas.fecha)')
            )
            ->orderBy('fecha', 'desc')
            ->get();

        // Canceladas por paciente y día
        $citas_canceladas = DB::table('cita_medicas')
            ->join('pacientes', 'cita_medicas.paciente_id', '=', 'pacientes.id')
            ->select(
                DB::raw("CONCAT(pacientes.nombre, ' ', pacientes.apellido_paterno, ' ', pacientes.apellido_materno) as paciente"),
                DB::raw('DATE(cita_medicas.fecha) as fecha'),
                DB::raw('COUNT(*) as count')
            )
            ->where('cita_medicas.estado', 'cancelada')
            ->groupBy(
                'pacientes.id',
                'pacientes.nombre',
                'pacientes.apellido_paterno',
                'pacientes.apellido_materno',
                DB::raw('DATE(cita_medicas.fecha)')
            )
            ->orderBy('fecha', 'desc')
            ->get();

        // Totales y porcentajes
        $total_confirmadas = $citas_confirmadas->sum('count');
        $total_canceladas  = $citas_canceladas->sum('count');
        $total_citas       = $total_confirmadas + $total_canceladas;

        $porcentajeConfirmadas = $total_citas > 0
            ? round($total_confirmadas / $total_citas * 100, 2)
            : 0;

        $porcentajeCanceladas = $total_citas > 0
            ? round($total_canceladas / $total_citas * 100, 2)
            : 0;

        return view('reportes.index', [
            'citas_confirmadas'     => $citas_confirmadas,
            'citas_canceladas'      => $citas_canceladas,
            'porcentajeConfirmadas' => $porcentajeConfirmadas,
            'porcentajeCanceladas'  => $porcentajeCanceladas,
        ]);
    }
}
