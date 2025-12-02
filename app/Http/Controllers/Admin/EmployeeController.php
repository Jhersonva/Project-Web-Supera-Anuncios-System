<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Mostrar lista de empleados
     */
    public function index()
    {
        // Solo empleados
        $employees = User::where('role_id', 3)->orderBy('full_name', 'asc')->get();

        return view('admin.config.employee.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.config.employee.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'email'     => 'required|email|unique:users,email',
            'dni'       => 'required|digits:8|unique:users,dni',
            'password'  => 'required|min:6',
        ]);

        User::create([
            'role_id'   => 3,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'dni'       => $request->dni,
            'phone'     => $request->phone,
            'locality'  => $request->locality,
            'whatsapp'  => $request->whatsapp,
            'call_phone'=> $request->call_phone,
            'contact_email'=> $request->contact_email,
            'address'   => $request->address,
            'password'  => Hash::make($request->password),
            'is_active' => true
        ]);

        return redirect()->route('admin.config.employees')
            ->with('success', 'Empleado registrado correctamente.');
    }

    public function edit(User $employee)
    {
        return view('admin.config.employee.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'full_name' => 'required',
            'email'     => 'required|email|unique:users,email,' . $employee->id,
            'dni'       => 'required|digits:8|unique:users,dni,' . $employee->id,
        ]);

        $employee->update($request->all());

        return redirect()->route('admin.config.employees')
            ->with('success', 'Empleado actualizado.');
    }

    public function toggle(User $employee)
    {
        $employee->is_active = !$employee->is_active;
        $employee->save();

        return back()->with('success', 'Estado del empleado actualizado.');
    }
}
