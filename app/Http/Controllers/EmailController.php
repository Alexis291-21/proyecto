<?php

namespace App\Http\Controllers;

use App\Mail\RecordatorioCita;
use App\Mail\RecordatorioPaciente;
use App\Models\Cita;
use App\Models\CitaMedica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;    // <-- Importa Log

class EmailController extends Controller
{
    public function enviar(Request $request)
    {
        try {
            $data = $request->validate([
                'cita_id' => 'required|exists:citas,id',
                'mensaje' => 'required|string',
            ]);

            $cita = CitaMedica::with('paciente')->findOrFail($data['cita_id']);
            $email = optional($cita->paciente)->email;

            if (! $email) {
                return response()->json([
                    'message' => 'El paciente no tiene un correo registrado.'
                ], 422);
            }

            Mail::to($email)
                ->send(new RecordatorioCita($cita, $data['mensaje']));

            return response()->json([
                'message' => 'Recordatorio enviado al correo del paciente.'
            ], 200);

        } catch (\Exception $e) {
            // Ahora Log::error apunta al facade correctamente
            Log::error("Error enviando recordatorio (cita_id={$request->cita_id}): {$e->getMessage()}");

            return response()->json([
                'message' => 'Ocurrió un error al intentar enviar el correo.'
            ], 500);
        }
    }
}
