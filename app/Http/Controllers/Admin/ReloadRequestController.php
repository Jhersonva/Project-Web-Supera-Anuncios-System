<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recharge;
use App\Models\User;
use Illuminate\Http\Request;

class ReloadRequestController extends Controller
{
    public function index()
    {
        $recharges = Recharge::orderBy('created_at', 'desc')->get();
        return view('admin.reload-request.index', compact('recharges'));
    }

    public function approve($id)
    {
        $recarga = Recharge::findOrFail($id);

        if ($recarga->status !== 'pendiente') {
            return back()->with('warning', 'La recarga ya fue procesada.');
        }

        // Sumar monto
        $usuario = $recarga->user;
        $usuario->virtual_wallet += $recarga->monto;
        $usuario->save();

        // Cambiar estado
        $recarga->status = 'aceptado';
        $recarga->save();

        return back()->with('success', 'Recarga aprobada y saldo añadido al usuario.');
    }

    public function reject($id)
    {
        $recarga = Recharge::findOrFail($id);

        if ($recarga->status !== 'pendiente') {
            return back()->with('warning', 'Esta recarga ya fue procesada.');
        }

        $recarga->status = 'rechazado';
        $recarga->save();

        return back()->with('info', 'Recarga rechazada.');
    }

}
