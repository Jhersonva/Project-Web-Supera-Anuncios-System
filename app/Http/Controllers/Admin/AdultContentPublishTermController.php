<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdultContentPublishTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdultContentPublishTermController extends Controller
{
    public function index()
    {
        $terms = AdultContentPublishTerm::all();
        return view('admin.adult.publish_terms.index', compact('terms'));
    }

    public function publicTermsAdult()
    {
        $terms = AdultContentPublishTerm::orderBy('id')->get();

        return view('advertising_user.terms_and_conditions.publish',compact('terms')
        );
    }

    public function update(Request $request, AdultContentPublishTerm $adultContentPublishTerm)
    {
        $request->validate([
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        /* ICONO*/
        if ($request->hasFile('icon')) {

            // Eliminar imagen anterior
            if ($adultContentPublishTerm->icon && File::exists(public_path($adultContentPublishTerm->icon))) {
                File::delete(public_path($adultContentPublishTerm->icon));
            }

            // Crear nombre único
            $file = $request->file('icon');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Ruta destino
            $destination = public_path('assets/img/publish-terms');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // Mover archivo
            $file->move($destination, $filename);

            // Guardar ruta
            $adultContentPublishTerm->icon = 'assets/img/publish-terms/' . $filename;
        }

        /* TEXTO */
        $adultContentPublishTerm->title = $request->title;
        $adultContentPublishTerm->description = $request->description;
        $adultContentPublishTerm->save();

        return redirect()->back()->with('success', 'Término actualizado correctamente');
    }
}
