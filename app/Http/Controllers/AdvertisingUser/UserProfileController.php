<?php

namespace App\Http\Controllers\AdvertisingUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'password'  => 'nullable|min:6'
        ]);

        // Datos editables
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

        // Si cambia la contraseña
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Tu perfil ha sido actualizado correctamente.');
    }
}