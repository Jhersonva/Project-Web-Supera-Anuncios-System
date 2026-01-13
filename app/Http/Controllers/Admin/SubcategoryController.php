<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdCategory;
use App\Models\AdSubcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ad_categories_id' => 'required|exists:ad_categories,id',
            'name'             => 'required|string|max:255',
            'price'            => 'nullable|numeric',

            'is_urgent'     => 'sometimes|boolean',
            'is_premiere'   => 'sometimes|boolean',
            'is_featured'   => 'sometimes|boolean',
            'is_semi_new'   => 'sometimes|boolean',
            'is_new'        => 'sometimes|boolean',
            'is_available'  => 'sometimes|boolean',
            'is_top'        => 'sometimes|boolean',
        ]);

        AdSubcategory::create([
            'ad_categories_id' => $request->ad_categories_id,
            'name'             => $request->name,
            'price'            => $request->price,

            'is_urgent'     => $request->boolean('is_urgent'),
            'is_premiere'   => $request->boolean('is_premiere'),
            'is_featured'   => $request->boolean('is_featured'),
            'is_semi_new'   => $request->boolean('is_semi_new'),
            'is_new'        => $request->boolean('is_new'),
            'is_available'  => $request->boolean('is_available'),
            'is_top'        => $request->boolean('is_top'),
        ]);

        return back()->with('success', 'Subcategoría creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $sub = AdSubcategory::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'nullable|numeric',

            'is_urgent'     => 'sometimes|boolean',
            'is_premiere'   => 'sometimes|boolean',
            'is_featured'   => 'sometimes|boolean',
            'is_semi_new'   => 'sometimes|boolean',
            'is_new'        => 'sometimes|boolean',
            'is_available'  => 'sometimes|boolean',
            'is_top'        => 'sometimes|boolean',
        ]);

        $sub->update([
            'name'  => $request->name,
            'price' => $request->price,

            'is_urgent'     => $request->boolean('is_urgent'),
            'is_premiere'   => $request->boolean('is_premiere'),
            'is_featured'   => $request->boolean('is_featured'),
            'is_semi_new'   => $request->boolean('is_semi_new'),
            'is_new'        => $request->boolean('is_new'),
            'is_available'  => $request->boolean('is_available'),
            'is_top'        => $request->boolean('is_top'),
        ]);

        return back()->with('success', 'Subcategoría actualizada.');
    }

    public function destroy($id)
    {
        $sub = AdSubcategory::findOrFail($id);
        $sub->fields()->delete();
        $sub->delete();

        return back()->with('success', 'Subcategoría eliminada.');
    }

    public function subcategories($categoryId)
    {
        return AdSubcategory::where('ad_categories_id', $categoryId)
            ->select(
                'id',
                'name',
                'price',
                'is_urgent',
                'is_premiere',
                'is_featured',
                'is_semi_new',
                'is_new',
                'is_available',
                'is_top'
            )
            ->get();
    }

    public function subcategoriesWithCategory($id)
    {
        $category = AdCategory::findOrFail($id);

        $subcategories = AdSubcategory::where('ad_categories_id', $id)
            ->select(
                'id',
                'name',
                'price',
                'is_urgent',
                'is_premiere',
                'is_featured',
                'is_semi_new',
                'is_new',
                'is_available',
                'is_top'
            )
            ->get();

        return response()->json([
            'category' => $category,
            'subcategories' => $subcategories
        ]);
    }
}