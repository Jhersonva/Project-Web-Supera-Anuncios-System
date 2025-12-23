<?php 

namespace App\Http\Controllers;

use App\Models\PrivacyPolicySetting;
use App\Models\AdCategory;
use App\Models\AdSubcategory;

class HomeController extends Controller
{
    public function index()
    {
        $categories = AdCategory::with('subcategories')->get();
        $subcategories = AdSubcategory::all();

        $policy = PrivacyPolicySetting::where('is_active', true)->first();

        return view('public.home', compact(
            'categories',
            'subcategories',
            'policy'
        ));
    }
}
