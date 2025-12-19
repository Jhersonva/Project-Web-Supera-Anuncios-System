<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicySetting;
use Illuminate\Http\Request;

class PrivacyPolicySettingController extends Controller
{
    /**
     * ver politicas (admin / employee / advertising_user)
     */
    public function show()
    {
        $policy = PrivacyPolicySetting::first();

        return view('privacy-policy.show', compact('policy'));
    }

    /**
     * ver configuracion (solo admin / employee)
     */
    public function index()
    {
        $policy = PrivacyPolicySetting::first();

        return view('admin.config.privacy-policy.index', compact('policy'));
    }

    /**
     * actualizar politicas (solo admin / employee)
     */
    public function update(Request $request)
    {
        $request->validate([
            'privacy_text' => 'required|string',
            'contains_explicit_content' => 'boolean',
            'requires_adult' => 'boolean',
            'is_active' => 'boolean',
        ]);

        PrivacyPolicySetting::updateOrCreate(
            ['id' => 1], 
            [
                'privacy_text' => $request->privacy_text,
                'contains_explicit_content' => $request->boolean('contains_explicit_content'),
                'requires_adult' => $request->boolean('requires_adult'),
                'is_active' => $request->boolean('is_active'),
            ]
        );

        return redirect()->back()->with('success', 'Políticas de privacidad actualizadas correctamente');
    }
}