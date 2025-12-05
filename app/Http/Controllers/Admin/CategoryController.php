<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdCategory;
use Illuminate\Http\Request;
use App\Models\Setting;

class CategoryController extends Controller
{
    public function index()
    {
        // Cargar categorías con subcategorías y sus campos
            $categories = AdCategory::with([
                'subcategories.fields'
            ])->get();

            $urgentPrice = \App\Models\Setting::get('urgent_publication_price', 5.00);

            return view('admin.config.categories.index', [
            'categories' => AdCategory::with('subcategories.fields')->get(),
            'urgentPrice' => Setting::get('urgent_publication_price', 5.00)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required'
        ]);

        AdCategory::create($request->only('name', 'icon'));

        return back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $category = AdCategory::findOrFail($id);

        $category->update($request->only('name','icon'));

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        $category = AdCategory::findOrFail($id);
        $category->subcategories()->delete();
        $category->delete();

        return back()->with('success', 'Categoría eliminada.');
    }
}
