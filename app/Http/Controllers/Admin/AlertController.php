<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::orderByDesc('created_at')->get();
        return view('admin.adult.alert.index', compact('alerts'));
    }
    

    public function update(Request $request, Alert $alert)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        /* LOGO */
        if ($request->hasFile('logo')) {

            if ($alert->logo && File::exists(public_path($alert->logo))) {
                File::delete(public_path($alert->logo));
            }

            $file = $request->file('logo');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('assets/img/alerts');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $alert->logo = 'assets/img/alerts/' . $filename;
        }

        $alert->title = $request->title;
        $alert->description = $request->description;
        $alert->is_active = $request->is_active;
        $alert->save();

        return redirect()->back()->with('success', 'Alerta actualizada correctamente');
    }
}
