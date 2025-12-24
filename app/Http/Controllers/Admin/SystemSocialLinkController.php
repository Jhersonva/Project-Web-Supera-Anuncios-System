<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\SystemSocialLink;
use Illuminate\Http\Request;

class SystemSocialLinkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'url'  => 'required|url',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'url']);

        if ($request->hasFile('icon')) {
            $path = public_path('images/system/social');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $filename = 'social_' . time() . '_' . uniqid() . '.' .
                        $request->file('icon')->getClientOriginalExtension();

            $request->file('icon')->move($path, $filename);

            $data['icon'] = 'images/system/social/' . $filename;
        }

        SystemSocialLink::create($data);

        return back()->with('success', 'Red social agregada correctamente');
    }

    public function edit()
    {
        $settings = SystemSetting::firstOrCreate([]);
        $socials  = SystemSocialLink::orderBy('order')->get();

        return view('admin.config.system.index', compact('settings', 'socials'));
    }

    public function destroy(SystemSocialLink $social)
    {
        if ($social->icon && file_exists(public_path($social->icon))) {
            unlink(public_path($social->icon));
        }

        $social->delete();

        return back()->with('success', 'Red social eliminada');
    }
}