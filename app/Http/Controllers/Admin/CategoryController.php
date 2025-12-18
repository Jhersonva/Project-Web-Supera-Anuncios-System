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
        return view('admin.config.categories.index', [
            'categories' => AdCategory::with('subcategories.fields')->get(),

            'urgentPrice'     => Setting::get('urgent_publication_price', 7.00),
            'featuredPrice'   => Setting::get('featured_publication_price', 6.00),
            'premierePrice'   => Setting::get('premiere_publication_price',5.00),

            'semiNewPrice'    => Setting::get('semi_new_publication_price', 4.00),
            'newPrice'        => Setting::get('new_publication_price', 3.00),
            'availablePrice' => Setting::get('available_publication_price', 2.00),
            'topPrice'        => Setting::get('top_publication_price', 1.00),
        ]);
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

            'is_urgent'     => $request->boolean('is_urgent'),
            'is_premiere'   => $request->boolean('is_premiere'),
            'is_featured'   => $request->boolean('is_featured'),
            'is_semi_new'   => $request->boolean('is_semi_new'),
            'is_new'        => $request->boolean('is_new'),
            'is_available'  => $request->boolean('is_available'),
            'is_top'        => $request->boolean('is_top'),
        ]);

        return back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $category = AdCategory::findOrFail($id);

        $category->update([
            'name' => $request->name,
            'icon' => $request->icon,

            'is_urgent'     => $request->boolean('is_urgent'),
            'is_premiere'   => $request->boolean('is_premiere'),
            'is_featured'   => $request->boolean('is_featured'),
            'is_semi_new'   => $request->boolean('is_semi_new'),
            'is_new'        => $request->boolean('is_new'),
            'is_available'  => $request->boolean('is_available'),
            'is_top'        => $request->boolean('is_top'),
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
