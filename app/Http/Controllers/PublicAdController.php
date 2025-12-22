<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Models\AdCategory;
use App\Models\AdSubcategory;

class PublicAdController extends Controller
{
    public function index()
    {
        $categories = AdCategory::with('subcategories')->get();
        $subcategories = AdSubcategory::all();

        return view('public.home', compact('categories', 'subcategories'));
    }
}
