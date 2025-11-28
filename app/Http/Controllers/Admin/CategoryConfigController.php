<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdCategory;

class CategoryConfigController extends Controller
{
    public function index()
    {
        // Cargar categorías con subcategorías y sus campos
        $categories = AdCategory::with([
            'subcategories.fields'
        ])->get();

        return view('admin.config.categories.index', compact('categories'));
    }
}
