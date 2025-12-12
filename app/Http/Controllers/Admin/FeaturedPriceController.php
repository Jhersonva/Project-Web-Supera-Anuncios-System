<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class FeaturedPriceController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'featured_price' => 'required|numeric|min:0'
        ]);

        Setting::set('featured_publication_price', $request->featured_price);

        return back()->with('success', 'Precio destacado actualizado correctamente.');
    }
}
