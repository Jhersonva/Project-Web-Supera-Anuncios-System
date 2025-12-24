<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdultContentViewTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdultContentViewTermController extends Controller
{
    public function index()
    {
        $terms = AdultContentViewTerm::all();
        return view('admin.adult.view_terms.index', compact('terms'));
    }

    public function publicTerms()
    {
        $terms = AdultContentViewTerm::orderBy('id')->get();

        return view('advertising_user.terms_and_conditions.index',compact('terms')
        );
    }

    public function update(Request $request, AdultContentViewTerm $adultContentViewTerm)
    {
        $request->validate([
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        /* ICONO */
        if ($request->hasFile('icon')) {

            // Eliminar imagen anterior
            if ($adultContentViewTerm->icon && File::exists(public_path($adultContentViewTerm->icon))) {
                File::delete(public_path($adultContentViewTerm->icon));
            }

            // Crear nombre único
            $file = $request->file('icon');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Ruta destino
            $destination = public_path('assets/img/view-terms');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // Mover archivo
            $file->move($destination, $filename);

            // Guardar ruta
            $adultContentViewTerm->icon = 'assets/img/view-terms/' . $filename;
        }

        /* TEXTO */
        $adultContentViewTerm->title = $request->title;
        $adultContentViewTerm->description = $request->description;
        $adultContentViewTerm->save();

        return redirect()->back()->with('success', 'Término actualizado correctamente');
    }
}
