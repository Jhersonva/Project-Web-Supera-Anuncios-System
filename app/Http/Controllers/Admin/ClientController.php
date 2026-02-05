<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class ClientController extends Controller
{
    public function index(Request $request)
    {
        $accountType = $request->get('account_type'); 

        $clients = User::where('role_id', 2)
            ->when($accountType, function ($query) use ($accountType) {
                $query->where('account_type', $accountType);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'admin.config.clients.index',
            compact('clients', 'accountType')
        );
    }

    public function edit(User $client)
    {
        return view('admin.config.clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        $rules = [
            'email'          => 'required|email|unique:users,email,' . $client->id,
            'locality'       => 'nullable|string|max:150',
            'whatsapp'       => 'nullable|string|max:9',
            'call_phone'     => 'nullable|string|max:9',
            'address'        => 'nullable|string|max:200',
            'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        if ($client->account_type === 'person') {
            $rules['full_name'] = 'required|string|max:255';
            $rules['dni']       = 'required|digits:8|unique:users,dni,' . $client->id;
            $rules['birthdate']  = 'nullable|date|before_or_equal:today';
        }

        if ($client->account_type === 'business') {
            $rules['company_reason'] = 'required|string|max:255';
            $rules['ruc']            = 'required|digits:11|unique:users,ruc,' . $client->id;
        }

        $request->validate($rules);

        $data = $request->only([
            'email',
            'locality',
            'whatsapp',
            'call_phone',
            'address',
        ]);

        if ($client->account_type === 'person') {

            $data['full_name'] = $request->full_name;
            $data['dni'] = $request->dni;
            $data['birthdate']  = $request->birthdate;

            // Limpiar campos de empresa
            $data['company_reason'] = null;
            $data['ruc'] = null;
        }

        if ($client->account_type === 'business') {

            $data['company_reason'] = $request->company_reason;
            $data['ruc'] = $request->ruc;

            // Limpiar campos de persona
            $data['full_name'] = null;
            $data['dni'] = null;
        }

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

    public function toggleVerification(User $client)
    {
        if ($client->is_verified) {
            $client->is_verified = false;
            $client->verified_at = null;
        } else {
            $client->is_verified = true;
            $client->verified_at = now();
        }

        $client->save();

        return redirect()
            ->route('admin.config.clients')
            ->with('success', 'Estado de verificación actualizado.');
    }

    public function toggleStatus(User $client)
    {
        $client->is_active = !$client->is_active;
        $client->save();

        return redirect()->route('admin.config.clients')
            ->with('success', 'Estado de la cuenta actualizado.');
    }
}
