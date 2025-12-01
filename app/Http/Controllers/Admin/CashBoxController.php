<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashBox;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashBoxController extends Controller
{
    /**
     * Mostrar vista principal de cajas
     * - Admin → ve todas las cajas
     * - Employee → solo su caja
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role->name === 'admin') {
            // Admin ve todas las cajas
            $cashBoxes = CashBox::with('employee')->latest()->get();
        } else {
            // Empleado ve SOLO su caja
            $cashBoxes = CashBox::where('user_id', $user->id)->get();
        }

        return view('admin.config.cash.index', compact('cashBoxes', 'user'));
    }

    /**
     * Apertura de caja
     */
    public function open(Request $request)
    {
        $user = Auth::user();

        // Validar si ya tiene una caja abierta
        $existing = CashBox::where('user_id', $user->id)
                           ->where('status', 'open')
                           ->first();

        if ($existing) {
            return back()->with('error', 'Ya tienes una caja abierta.');
        }

        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
        ]);

        CashBox::create([
            'user_id' => $user->id,
            'opening_balance' => $request->opening_balance,
            'current_balance' => $request->opening_balance,
            'status' => 'open'
        ]);

        return back()->with('success', 'Caja abierta correctamente.');
    }

    /**
     * Añadir movimiento (ingreso o egreso)
     */
    public function addMovement(Request $request, $id)
    {
        $user = Auth::user();
        $cashBox = CashBox::findOrFail($id);

        // El employee SOLO puede usar su propia caja
        if ($user->role->name === 'employee' && $cashBox->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.1',
            'description' => 'nullable|string|max:250',
        ]);

        // Actualizar saldo de caja
        if ($request->type === 'income') {
            $cashBox->current_balance += $request->amount;
        } else {
            if ($cashBox->current_balance < $request->amount) {
                return back()->with('error', 'Saldo insuficiente.');
            }
            $cashBox->current_balance -= $request->amount;
        }

        $cashBox->save();

        // Registrar movimiento
        CashMovement::create([
            'cash_box_id' => $cashBox->id,
            'employee_id' => $user->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description
        ]);

        return back()->with('success', 'Movimiento registrado correctamente.');
    }

    /**
     * Cerrar caja
     */
    public function close($id)
    {
        $user = Auth::user();
        $cashBox = CashBox::findOrFail($id);

        // Employee SOLO su caja
        if ($user->role->name === 'employee' && $cashBox->user_id != $user->id) {
            abort(403);
        }

        if ($cashBox->status === 'closed') {
            return back()->with('error', 'La caja ya está cerrada.');
        }

        $cashBox->status = 'closed';
        $cashBox->save();

        return back()->with('success', 'Caja cerrada correctamente.');
    }

    /**
     * Detalle de la caja (movimientos)
     */
    public function show($id)
    {
        $user = Auth::user();
        $cashBox = CashBox::with('movements.employee')->findOrFail($id);

        // Employee solo su caja
        if ($user->role->name === 'employee' && $cashBox->user_id !== $user->id) {
            abort(403);
        }

        return view('admin.cash.show', compact('cashBox', 'user'));
    }
}