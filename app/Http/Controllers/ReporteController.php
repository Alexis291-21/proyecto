<?php

namespace App\Http\Controllers;

use App\Models\CitaMedica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Mostrar el reporte diario de citas confirmadas y canceladas.
     */
    public function index()
    {
        // 1. Obtener citas confirmadas, agrupadas por día
        $citas_confirmadas = CitaMedica::selectRaw('DATE(fecha) as periodo, COUNT(*) as count')
            ->where('estado', 'confirmada')
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();

        // 2. Obtener citas canceladas, agrupadas por día
        $citas_canceladas = CitaMedica::selectRaw('DATE(fecha) as periodo, COUNT(*) as count')
            ->where('estado', 'cancelada')
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();

        // 3. Cálculo de totales y porcentajes
        $total_confirmadas = $citas_confirmadas->sum('count');
        $total_canceladas  = $citas_canceladas->sum('count');
        $total_citas       = $total_confirmadas + $total_canceladas;

        $porcentajeConfirmadas = $total_citas > 0
            ? round($total_confirmadas / $total_citas * 100, 2)
            : 0;

        $porcentajeCanceladas = $total_citas > 0
            ? round($total_canceladas / $total_citas * 100, 2)
            : 0;

        // 4. Devolver la vista con los datos
        return view('reportes.index', [
            'citas_confirmadas'    => $citas_confirmadas,
            'citas_canceladas'     => $citas_canceladas,
            'porcentajeConfirmadas'=> $porcentajeConfirmadas,
            'porcentajeCanceladas' => $porcentajeCanceladas,
        ]);
    }
}
