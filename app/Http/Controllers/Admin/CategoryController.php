<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdCategory;
use App\Models\Setting;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = AdCategory::with('subcategories.fields')->get();

        $urgentPrice    = Setting::get('urgent_publication_price', 7.00);
        $featuredPrice  = Setting::get('featured_publication_price', 6.00);
        $premierePrice  = Setting::get('premiere_publication_price', 5.00);
        $semiNewPrice   = Setting::get('semi_new_publication_price', 4.00);
        $newPrice       = Setting::get('new_publication_price', 3.00);
        $availablePrice = Setting::get('available_publication_price', 2.00);
        $topPrice       = Setting::get('top_publication_price', 1.00);

        return view('admin.config.categories.index', compact(
            'categories',
            'urgentPrice',
            'featuredPrice',
            'premierePrice',
            'semiNewPrice',
            'newPrice',
            'availablePrice',
            'topPrice'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
        ]);

        AdCategory::create([
            'name' => $request->name,
            'icon' => $request->icon,
        ]);

        return back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $category = AdCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
            'icon' => $request->icon,
        ]);

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
