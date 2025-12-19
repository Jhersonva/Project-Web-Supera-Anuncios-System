<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PrivacyPolicyAcceptanceController extends Controller
{

    public function accept()
    {
        $user = Auth::user();

        $user->update([
            'privacy_policy_accepted' => true,
            'privacy_policy_accepted_at' => now(),
        ]);

        return redirect()->route('home');
    }

    public function reject()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')
            ->with('error', 'Debes aceptar las políticas de privacidad para usar el sistema.');
    }

}