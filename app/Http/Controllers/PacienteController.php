<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage   = $request->input('perPage', 10);
        $pacientes = Paciente::latest()->paginate($perPage);
        return view('pacientes.index', compact('pacientes', 'perPage'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'nombre'           => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'edad'             => 'required|integer|min:0',
            'genero'           => 'required|in:Masculino,Femenino,Otro',
            'telefono'         => ['required','digits:9','regex:/^9[0-9]{8}$/','unique:pacientes,telefono'],
            'direccion'        => 'required|string|max:255',
            'dni'              => ['required','digits:8','unique:pacientes,dni'],
            'fecha_nacimiento' => 'required|date',
        ];

        $messages = [
            'dni.digits'             => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique'             => 'El DNI ya está registrado. Verifica el número.',
            'telefono.digits'        => 'El teléfono debe tener exactamente 9 dígitos.',
            'telefono.regex'         => 'El teléfono debe comenzar con 9.',
            'telefono.unique'        => 'El teléfono ya está registrado. Usa otro.',
            'fecha_nacimiento.date'  => 'La fecha de nacimiento debe ser una fecha válida.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        Paciente::create($validator->validated());

        if ($request->ajax()) {
            session()->flash('success', 'Paciente creado correctamente.');
            return response()->json(['success' => true]);
        };
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paciente $paciente)
    {
        $rules = [
            'nombre'           => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'edad'             => 'required|integer|min:0',
            'genero'           => 'required|in:Masculino,Femenino,Otro',
            'telefono'         => ['required','digits:9','regex:/^9[0-9]{8}$/','unique:pacientes,telefono,'.$paciente->id],
            'direccion'        => 'required|string|max:255',
            'dni'              => ['required','digits:8','unique:pacientes,dni,'.$paciente->id],
            'fecha_nacimiento' => 'required|date',
        ];

        $messages = [
            'dni.digits'             => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique'             => 'El DNI ya está registrado. Verifica el número.',
            'telefono.digits'        => 'El teléfono debe tener exactamente 9 dígitos.',
            'telefono.regex'         => 'El teléfono debe comenzar con 9.',
            'telefono.unique'        => 'El teléfono ya está registrado. Usa otro.',
            'fecha_nacimiento.date'  => 'La fecha de nacimiento debe ser una fecha válida.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        $paciente->update($validator->validated());

        if ($request->ajax()) {
            session()->flash('success', 'Paciente actualizado correctamente.');
            return response()->json(['success' => true]);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paciente $paciente)
    {
        $paciente->delete();
        return redirect()->route('pacientes.index')
                         ->with('success', 'Paciente eliminado correctamente.');
    }
}
