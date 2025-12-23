<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class ClientController extends Controller
{
    public function index()
    {
        $clients = User::where('role_id', 2)->get();

        return view('admin.config.clients.index', compact('clients'));
    }

     public function edit(User $client)
    {
        return view('admin.config.clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $client->id,
            'dni'            => 'nullable',
            'phone'          => 'nullable',
            'locality'       => 'nullable',
            'whatsapp'       => 'nullable',
            'call_phone'     => 'nullable',
            'contact_email'  => 'nullable|email',
            'address'        => 'nullable',
            'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
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
            'address',
        ]);

        if ($request->hasFile('profile_image')) {

            $path = public_path('assets/img/profile-image');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Eliminar imagen anterior
            if ($client->profile_image && File::exists(public_path($client->profile_image))) {
                File::delete(public_path($client->profile_image));
            }

            $filename = 'user_' . $client->id . '_' . time() . '.' . $request->profile_image->extension();
            $request->profile_image->move($path, $filename);

            $data['profile_image'] = 'assets/img/profile-image/' . $filename;
        }

        $client->update($data);

        return redirect()
            ->route('admin.config.clients')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function verify(User $client)
    {
        $client->is_verified = true;
        $client->verified_at = now();
        $client->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario verificado correctamente.'
        ]);
    }

    public function toggleStatus(User $client)
    {
        $client->is_active = !$client->is_active;
        $client->save();

        return redirect()->route('admin.config.clients')
            ->with('success', 'Estado de la cuenta actualizado.');
    }
}
