<?php

namespace App\Http\Controllers\AdvertisingUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;


class UserProfileController extends Controller
{
    /* Mostrar perfil */
    public function index()
    {
        $user = Auth::user();
        return view('advertising_user.profile.index', compact('user'));
    }

    /* Actualizar datos */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'dni'       => 'required|digits:8|unique:users,dni,' . $user->id,
            'phone'     => 'nullable|digits:9',
            'locality'  => 'nullable|max:150',
            'whatsapp'  => 'nullable|max:20',
            'call_phone'=> 'nullable|max:20',
            'contact_email' => 'nullable|email|max:150',
            'address'   => 'nullable|max:200',
            'password'  => 'nullable|min:6',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $data = $request->only([
            'full_name',
            'email',
            'dni',
            'phone',
            'locality',
            'whatsapp',
            'call_phone',
            'contact_email',
            'address'
        ]);

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        /* ========= SUBIR IMAGEN ========= */
        if ($request->hasFile('profile_image')) {

            $path = public_path('assets/img/profile-image');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Eliminar imagen anterior (si existe)
            if ($user->profile_image && File::exists(public_path($user->profile_image))) {
                File::delete(public_path($user->profile_image));
            }

            $filename = 'user_' . $user->id . '_' . time() . '.' . $request->profile_image->extension();
            $request->profile_image->move($path, $filename);

            $data['profile_image'] = 'assets/img/profile-image/' . $filename;
        }

        $user->update($data);

        return back()->with('success', 'Tu perfil ha sido actualizado correctamente.');
    }

}