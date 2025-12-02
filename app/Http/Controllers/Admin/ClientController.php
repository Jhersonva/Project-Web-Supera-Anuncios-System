<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        // Clientes = usuarios con rol "advertising_user" (role_id = 2)
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
            'full_name' => 'required',
            'email'     => 'required|email|unique:users,email,' . $client->id,
            'phone'     => 'nullable',
            'locality'  => 'nullable',
        ]);

        // Actualizar datos
        $client->update($request->only([
            'full_name',
            'email',
            'phone',
            'locality'
        ]));

        return redirect()->route('admin.config.clients')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function toggleStatus(User $client)
    {
        $client->is_active = !$client->is_active;
        $client->save();

        return redirect()->route('admin.config.clients')
            ->with('success', 'Estado de la cuenta actualizado.');
    }
}
