<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recharge;
use App\Models\User;
use App\Models\CashBox;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ReloadRequestController extends Controller
{
    public function index()
    {
        // Recargas pendientes
        $rechargesPendientes = Recharge::where('status', 'pendiente')
        ->with(['user', 'paymentMethod'])
        ->latest()
        ->get();

        // Historial (aceptadas o rechazadas)
        $rechargesHistorial = Recharge::whereIn('status', ['aceptado', 'rechazado'])
        ->with(['user', 'paymentMethod'])
        ->latest()
        ->get();

        return view('admin.reload-request.index', compact('rechargesPendientes', 'rechargesHistorial'));
    }

    public function pendingCount()
    {
        $count = Recharge::where('status', 'pendiente')->count();
        return response()->json(['count' => $count]);
    }

    public function approve(Request $request, $id)
    {
        $request->validate(
            [
                'operation_number' => 'required|string|max:50|unique:recharges,operation_number',
                'admin_message'    => 'required|string|min:5',
            ],
            [
                'operation_number.required' => 'Debes ingresar el número de operación.',
                'operation_number.unique'   => 'Este número de operación ya fue registrado.',
                'operation_number.max'      => 'El número de operación no debe superar los 50 caracteres.',
                'admin_message.required'    => 'Debes escribir un mensaje para el usuario.',
                'admin_message.min'         => 'El mensaje debe tener al menos 5 caracteres.',
            ]
        );

        $recarga = Recharge::with('user')->findOrFail($id);

        if ($recarga->status !== 'pendiente') {
            return back()->with('warning', 'La recarga ya fue procesada.');
        }

        $empleado = Auth::user();

        // Caja abierta
        $caja = CashBox::where('user_id', $empleado->id)
            ->where('status', 'open')
            ->first();

        if (!$caja) {
            return back()->with('error', 'No tienes una caja abierta.');
        }

        $usuario = $recarga->user;

        // Sumar saldo
        $usuario->virtual_wallet += $recarga->monto;
        $usuario->save();

        // Actualizar recarga
        $recarga->update([
            'status'           => 'aceptado',
            'operation_number' => $request->operation_number,
            'reject_message'   => $request->admin_message,
            'notified_at'      => now(),
        ]);

        // Caja
        $caja->increment('current_balance', $recarga->monto);

        CashMovement::create([
            'cash_box_id' => $caja->id,
            'employee_id' => $empleado->id,
            'type'        => 'income',
            'amount'      => $recarga->monto,
            'description' => 'Recarga aprobada - Usuario ID ' . $usuario->id
        ]);

        // WhatsApp
        if ($request->has('send_whatsapp') && $usuario->whatsapp) {

            $phone = preg_replace('/[^0-9]/', '', $usuario->whatsapp);

            $mensaje = urlencode(
                "*Recarga aprobada*\n\n" .
                "Monto: S/. {$recarga->monto}\n" .
                "Operación: {$request->operation_number}\n\n" .
                "{$request->admin_message}"
            );

            return back()
                ->with('success', 'Recarga aprobada correctamente.')
                ->with('whatsapp_url', "https://wa.me/{$phone}?text={$mensaje}");
        }

        return back()->with('success', 'Recarga aprobada correctamente.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_message' => 'required|string|min:5',
        ]);

        $recarga = Recharge::with('user')->findOrFail($id);

        if ($recarga->status !== 'pendiente') {
            return back()->with('warning', 'Esta recarga ya fue procesada.');
        }

        $recarga->update([
            'status'         => 'rechazado',
            'reject_message' => $request->reject_message,
            'notified_at'    => now(),
        ]);

        $usuario = $recarga->user;

        if ($request->has('send_whatsapp') && $usuario->whatsapp) {

            $phone = preg_replace('/[^0-9]/', '', $usuario->whatsapp);

            $mensaje = urlencode(
                "*Recarga rechazada*\n\n" .
                "Monto: S/. {$recarga->monto}\n\n" .
                "{$request->reject_message}"
            );

            return back()
                ->with('info', 'Recarga rechazada.')
                ->with('whatsapp_url', "https://wa.me/{$phone}?text={$mensaje}");
        }

        return back()->with('info', 'Recarga rechazada.');
    }

    public function destroy($id)
    {
        $recarga = Recharge::findOrFail($id);

        // Eliminar imagen física
        if ($recarga->img_cap_pago && File::exists(public_path($recarga->img_cap_pago))) {
            File::delete(public_path($recarga->img_cap_pago));
        }

        // Eliminar registro
        $recarga->delete();

        return back()->with('success', 'Recarga eliminada del historial correctamente.');
    }
}
