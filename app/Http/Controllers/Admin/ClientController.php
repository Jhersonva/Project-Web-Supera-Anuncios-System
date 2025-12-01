<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class ClientController extends Controller
{
    public function index()
    {
        // Clientes = usuarios con rol "advertising_user" (role_id = 2)
        $clients = User::where('role_id', 2)->get();

        return view('admin.config.clients.index', compact('clients'));
    }
}
