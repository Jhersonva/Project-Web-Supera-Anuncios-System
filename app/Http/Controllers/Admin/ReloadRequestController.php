<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recharge;
use App\Models\User;
use App\Models\CashBox;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReloadRequestController extends Controller
{
    public function index()
    {
        // Recargas pendientes
        $rechargesPendientes = Recharge::where('status', 'pendiente')
            ->with('user')
            ->latest()
            ->get();

        // Historial (aceptadas o rechazadas)
        $rechargesHistorial = Recharge::whereIn('status', ['aceptado', 'rechazado'])
            ->with('user')
            ->latest()
            ->get();

        return view('admin.reload-request.index', compact('rechargesPendientes', 'rechargesHistorial'));
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

        // REGISTRAR MOVIMIENTO EN LA CAJA
        $empleado = Auth::user();

        // Encontrar caja abierta del admin/empleado que aprueba
        $caja = CashBox::where('user_id', $empleado->id)
                        ->where('status', 'open')
                        ->first();

        if ($caja) {

            // Actualizar saldo
            $caja->current_balance += $recarga->monto;
            $caja->save();

            // Registrar movimiento de recarga
            CashMovement::create([
                'cash_box_id' => $caja->id,
                'employee_id' => $empleado->id, 
                'type' => 'income',
                'amount' => $recarga->monto,
                'description' => 'Recarga aprobada para el usuario: ' . $usuario->full_name
            ]);
        }


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
