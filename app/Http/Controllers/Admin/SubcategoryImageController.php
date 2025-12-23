<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdSubcategory;
use App\Models\AdSubcategoryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubcategoryImageController extends Controller
{
    public function index($id)
    {
        return AdSubcategoryImage::where('ad_subcategory_id', $id)
            ->orderBy('order')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'ad_subcategory_id' => 'required|exists:ad_subcategories,id',
            'images.*' => 'nullable|image|max:2048'
        ]);

        if (!$request->hasFile('images')) {
            return back();
        }

        $sub = AdSubcategory::findOrFail($request->ad_subcategory_id);

        foreach ($request->file('images') as $index => $image) {

            $filename = uniqid().'_'.$image->getClientOriginalName();

            $image->move(
                public_path('assets/img/subcategories'),
                $filename
            );

            $sub->images()->create([
                'image' => 'assets/img/subcategories/'.$filename,
                'order' => $index
            ]);
        }

        return back()->with('success', 'Imágenes agregadas correctamente.');
    }

    public function bySubcategory($id)
    {
        return AdSubcategoryImage::where('ad_subcategory_id', $id)
            ->select('id', 'image')
            ->get();
    }

    public function destroy($id)
    {
        $img = AdSubcategoryImage::findOrFail($id);

        if (file_exists(public_path($img->image))) {
            unlink(public_path($img->image));
        }

        $img->delete();

        return response()->json([
            'success' => true
        ], 200);
    }

}
