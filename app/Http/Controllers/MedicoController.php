<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $medicos = Medico::latest()->paginate($perPage);
        return view('medicos.index', compact('medicos', 'perPage'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'nombre'            => 'required|string|max:255',
            'apellido_paterno'  => 'required|string|max:255',
            'apellido_materno'  => 'required|string|max:255',
            'especialidad'      => 'required|string|max:255',
            'telefono'          => ['required','digits:9','regex:/^9[0-9]{8}$/','unique:medicos,telefono'],
            'dni'               => ['required','digits:8','unique:medicos,dni'],
            'email'             => ['required','email','max:100','unique:medicos,email'],
            'disponibilidad'    => 'required|in:Disponible,No disponible',
        ];

        $messages = [
            'dni.digits'       => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique'       => 'El DNI ya está registrado. Por favor, verifica el número.',
            'telefono.digits'  => 'El teléfono debe contener exactamente 9 dígitos numéricos.',
            'telefono.regex'   => 'El teléfono debe comenzar con el número 9.',
            'telefono.unique'  => 'El número de teléfono ya está registrado. Usa uno diferente.',
            'email.email'      => 'El correo electrónico debe tener un formato válido.',
            'email.max'        => 'El correo electrónico no puede exceder 100 caracteres.',
            'email.unique'     => 'El correo electrónico ya está en uso. Elige otro.',
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

        Medico::create($validator->validated());

        if ($request->ajax()) {
            session()->flash('success', 'Médico creado correctamente.');
            return response()->json(['success' => true]);
        };
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medico $medico)
    {
        $rules = [
            'nombre'            => 'required|string|max:255',
            'apellido_paterno'  => 'required|string|max:255',
            'apellido_materno'  => 'required|string|max:255',
            'especialidad'      => 'required|string|max:255',
            'telefono'          => ['required','digits:9','regex:/^9[0-9]{8}$/','unique:medicos,telefono,'.$medico->id],
            'dni'               => ['required','digits:8','unique:medicos,dni,'.$medico->id],
            'email'             => ['required','email','max:100','unique:medicos,email,'.$medico->id],
            'disponibilidad'    => 'required|in:Disponible,No disponible',
        ];

        $messages = [
            'dni.digits'       => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique'       => 'El DNI ya está registrado. Por favor, verifica el número.',
            'telefono.digits'  => 'El teléfono debe contener exactamente 9 dígitos numéricos.',
            'telefono.regex'   => 'El teléfono debe comenzar con el número 9.',
            'telefono.unique'  => 'El número de teléfono ya está registrado. Usa uno diferente.',
            'email.email'      => 'El correo electrónico debe tener un formato válido.',
            'email.max'        => 'El correo electrónico no puede exceder 100 caracteres.',
            'email.unique'     => 'El correo electrónico ya está en uso. Elige otro.',
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

        $medico->update($validator->validated());

        if ($request->ajax()) {
            session()->flash('success', 'Médico actualizado correctamente.');
            return response()->json(['success' => true]);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medico $medico)
    {
        $medico->delete();
        return redirect()->route('medicos.index')
                         ->with('success', 'Médico eliminado correctamente.');
    }
}
