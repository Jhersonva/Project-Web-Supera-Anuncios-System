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
        // ADMIN (asumimos role_id = 1)
        $admin = User::where('role_id', 1)->first();

        // EMPLEADOS
        $employees = User::where('role_id', 3)
            ->orderBy('full_name', 'asc')
            ->get();

        return view(
            'admin.config.employee.index',
            compact('admin', 'employees')
        );
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
        ], [
            // full_name
            'full_name.required' => 'El nombre completo es obligatorio.',

            // email
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Debes ingresar un correo electrónico válido.',
            'email.unique'   => 'Este correo ya se encuentra registrado.',

            // dni
            'dni.required' => 'El DNI es obligatorio.',
            'dni.digits'   => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique'   => 'Este DNI ya está registrado en el sistema.',

            // password
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe contener al menos 6 caracteres.',
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
        if ($employee->role_id === 1 && auth()->user()->role_id !== 1) {
            abort(403);
        }

        return view('admin.config.employee.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'full_name' => 'required',
            'email'     => 'required|email|unique:users,email,' . $employee->id,
            'dni'       => 'required|digits:8|unique:users,dni,' . $employee->id,
            'password'  => 'nullable|min:6',
        ], [
            'full_name.required' => 'El nombre completo es obligatorio.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Debes ingresar un correo electrónico válido.',
            'email.unique'   => 'Otro usuario ya está usando este correo.',

            'dni.required' => 'El DNI es obligatorio.',
            'dni.digits'   => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique'   => 'Otro usuario ya está usando este DNI.',

            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // Datos base (SIN password)
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

        // Si se escribió una nueva contraseña → la actualizamos
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        return redirect()
            ->route('admin.config.employees')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function toggle(User $employee)
    {
        $employee->is_active = !$employee->is_active;
        $employee->save();

        return back()->with('success', 'Estado del empleado actualizado.');
    }
}
