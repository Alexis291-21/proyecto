<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Solo accesible para usuarios autenticados.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el formulario de configuración (opcional).
     */
    public function edit()
    {
        $setting = Setting::first();
        return view('settings.edit', compact('setting'));
    }

    /**
     * Actualiza los valores de configuración, incluyendo el logo.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'nombre_empresa' => ['nullable', 'string', 'max:255'],
            'codigo_postal'  => ['nullable', 'digits:5'],
            'telefono'       => ['nullable', 'digits:9', 'starts_with:9'],
            'ruc'            => ['nullable', 'digits:11'],
            'responsable'    => ['nullable', 'string', 'max:255'],
            'logo'           => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        // Obtener o crear la instancia de Setting
        $setting = $setting = Setting::firstOrNew([]);
;

        // Si subieron un nuevo logo
        if ($request->hasFile('logo')) {
            // Borrar logo anterior si existía
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }
            // Almacenar el nuevo logo en storage/app/public/settings/logo
            $path = $request->file('logo')->store('settings/logo', 'public');
            $data['logo'] = $path;
        }

        // Rellenar y guardar
        $setting->fill($data)->save();

        return back()->with('success', '¡Configuración actualizada correctamente!');
    }
}
