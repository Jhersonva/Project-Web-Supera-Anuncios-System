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
            'ad_categories_id' => 'required',
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        try {
            $sub = AdSubcategory::create($request->all());

            return back()->with('success', 'Subcategoría creada correctamente.');

        } catch (\Exception $e) {

            return back()->with('error', 'Error al crear la subcategoría.');
        }
    }

    public function update(Request $request, $id)
    {
        $sub = AdSubcategory::findOrFail($id);

        $sub->update($request->only('name','price'));

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
            ->select('id', 'name', 'price')
            ->get();
    }

    public function subcategoriesWithCategory($id)
    {
        $category = AdCategory::findOrFail($id);

        $subcategories = AdSubcategory::where('ad_categories_id', $id)
            ->select('id','name','price')
            ->get();

        return response()->json([
            'category' => $category,
            'subcategories' => $subcategories
        ]);
    }

}
