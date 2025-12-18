<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class LabelPriceController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'key'   => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);

        Setting::set($request->key, $request->price);

        return back()->with('success', 'Precio actualizado correctamente.');
    }
}
