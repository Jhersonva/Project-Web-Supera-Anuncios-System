<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class PremierePriceController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'premiere_price' => 'required|numeric|min:0'
        ]);

        Setting::set('premiere_publication_price', $request->premiere_price);

        return back()->with('success', 'Precio de publicación estreno actualizado correctamente.');
    }
}