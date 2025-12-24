<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\SystemSocialLink;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    /**
     * Mostrar formulario de configuración del sistema
     */
    public function edit()
    {
        $settings = SystemSetting::firstOrCreate([]);
        $socials  = SystemSocialLink::orderBy('order')->get();

        return view('admin.config.system.index', compact('settings', 'socials'));
    }

    /**
     * Actualizar configuración del sistema
     */
    public function update(Request $request)
    {
        $settings = SystemSetting::firstOrFail();

        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'whatsapp_number' => 'nullable|string|max:9',
        ]);

        $data = $request->only([
            'company_name',
            'company_description',
            'whatsapp_number',
        ]);

        // LOGO (misma lógica que anuncios)
        if ($request->hasFile('logo')) {

            $path = public_path('images/system/logo');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // eliminar logo anterior
            if ($settings->logo && file_exists(public_path($settings->logo))) {
                unlink(public_path($settings->logo));
            }

            $filename = 'logo_' . time() . '_' . uniqid() . '.' . 
                        $request->file('logo')->getClientOriginalExtension();

            $request->file('logo')->move($path, $filename);

            $data['logo'] = 'images/system/logo/' . $filename;
        }

        $settings->update($data);

        return redirect()
            ->route('admin.config.system')
            ->with('success', 'Configuración del sistema actualizada correctamente');
    }
}
