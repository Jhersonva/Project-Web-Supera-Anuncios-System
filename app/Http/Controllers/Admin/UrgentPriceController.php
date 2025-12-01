<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class UrgentPriceController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'urgent_price' => 'required|numeric|min:0'
        ]);

        Setting::set('urgent_publication_price', $request->urgent_price);

        return back()->with('success', 'Precio actualizado correctamente.');
    }
}
