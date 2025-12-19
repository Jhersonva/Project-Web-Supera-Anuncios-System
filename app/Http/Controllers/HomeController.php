<?php 

namespace App\Http\Controllers;

use App\Models\PrivacyPolicySetting;

class HomeController extends Controller
{
    public function index()
    {
        $policy = PrivacyPolicySetting::where('is_active', true)->first();

        return view('public.home', compact('policy'));
    }
}
