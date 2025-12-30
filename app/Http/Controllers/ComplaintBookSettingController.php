<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ComplaintBookSetting;
use Illuminate\Http\Request;

class ComplaintBookSettingController extends Controller
{
    /**
     * Vista admin de configuración
     */
    public function index()
    {
        $settings = ComplaintBookSetting::first();

        return view('admin.config.complaint_book_settings.index', compact('settings'));
    }

    public function publicView()
    {
        $settings = ComplaintBookSetting::firstOrFail();

        // Captcha matemático
        $a = rand(1, 9);
        $b = rand(1, 9);

        session([
            'captcha_result' => $a + $b
        ]);

        return view('public.complaint-book', compact('settings', 'a', 'b'));
    }


    /**
     * Actualizar desde panel admin (blade)
     */
    public function updateView(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:11',
            'address' => 'nullable|string|max:255',
            'legal_text' => 'required|string',
            'notification_email' => 'nullable|email',
        ]);

        $settings = ComplaintBookSetting::first();

        if (!$settings) {
            ComplaintBookSetting::create($request->all());
        } else {
            $settings->update($request->all());
        }

        return redirect()
            ->back()
            ->with('success', 'Libro de reclamaciones actualizado correctamente');
    }

    /**
     * Mostrar configuración del libro de reclamaciones
     * (advertising_user + admin)
     */
    public function show()
    {
        $setting = ComplaintBookSetting::first();

        if (!$setting) {
            return response()->json([
                'message' => 'Configuración no encontrada'
            ], 404);
        }

        return response()->json($setting);
    }

    /**
     * Actualizar texto y datos del libro
     * (SOLO admin)
     */
    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:11',
            'address' => 'nullable|string|max:255',
            'legal_text' => 'required|string',
            'notification_email' => 'nullable|email',
        ]);

        $setting = ComplaintBookSetting::first();

        if (!$setting) {
            $setting = ComplaintBookSetting::create($request->all());
        } else {
            $setting->update($request->all());
        }

        return response()->json([
            'message' => 'Libro de reclamaciones actualizado correctamente',
            'data' => $setting
        ]);
    }
}
