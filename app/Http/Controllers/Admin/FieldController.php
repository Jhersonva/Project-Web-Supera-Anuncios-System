<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FieldSubcategoryAd;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class FieldController extends Controller
{
   public function store(Request $request)
    {

        $request->validate([
            'ad_subcategories_id' => 'required',
            'name' => 'required',
            'type' => 'text'
        ]);

        FieldSubcategoryAd::create($request->all());

        return back()->with('success', 'Campo creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $field = FieldSubcategoryAd::findOrFail($id);

        $field->update($request->only('name'));

        return back()->with('success', 'Campo actualizado.');
    }

    public function destroy($id)
    {
        FieldSubcategoryAd::findOrFail($id)->delete();

        return back()->with('success', 'Campo eliminado.');
    }
}
