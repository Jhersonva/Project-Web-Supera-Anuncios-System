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

    public function approve(Request $request, $id)
    {
        $recarga = Recharge::findOrFail($id);

        if ($recarga->status !== 'pendiente') {
            return back()->with('warning', 'La recarga ya fue procesada.');
        }

        // Validar que ingrese el número de operación
        $request->validate([
            'operation_number' => 'required|string|max:50',
        ]);

        // Guardar el número de operación
        $recarga->operation_number = $request->operation_number;

        // Sumar monto al usuario
        $usuario = $recarga->user;
        $usuario->virtual_wallet += $recarga->monto;
        $usuario->save();

        // Cambiar estado
        $recarga->status = 'aceptado';
        $recarga->save();

        return back()->with('success', 'Recarga aprobada correctamente.');
    }


    public function reject(Request $request, $id)
    {
        $recarga = Recharge::findOrFail($id);

        if ($recarga->status !== 'pendiente') {
            return back()->with('warning', 'Esta recarga ya fue procesada.');
        }

        // Validar que ingrese un motivo de rechazo
        $request->validate([
            'reject_message' => 'required|string|min:5',
        ]);

        // Guardar mensaje
        $recarga->reject_message = $request->reject_message;

        // Cambiar estado
        $recarga->status = 'rechazado';
        $recarga->save();

        return back()->with('info', 'La recarga ha sido rechazada.');
    }

}
