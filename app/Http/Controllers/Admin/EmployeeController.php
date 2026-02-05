<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
{
    /**
     * Mostrar lista de empleados
     */
    public function index()
    {
        // TODOS LOS ADMINS
        $admins = User::where('role_id', 1)
            ->orderBy('full_name', 'asc')
            ->get();

        // EMPLEADOS
        $employees = User::where('role_id', 3)
            ->orderBy('full_name', 'asc')
            ->get();

        return view(
            'admin.config.employee.index',
            compact('admins', 'employees')
        );
    }

    public function create()
    {
        return view('admin.config.employee.create');
    }

    public function store(Request $request)
    {
        if ($request->role_id == 1 && auth()->user()->role_id !== 1) {
            abort(403);
        }

        $request->validate([
            'full_name'  => 'required|string|max:150',
            'email'      => 'required|email|unique:users,email',
            'dni'        => 'required|digits:8|unique:users,dni',
            'whatsapp'   => 'nullable|digits:9',
            'call_phone' => 'nullable|digits:9',
            'password'   => 'required|min:6',
            'role_id'    => 'required|in:1,3',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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

            // whatsapp
            'whatsapp.digits' => 'El número de WhatsApp debe tener exactamente 9 dígitos.',

            // call_phone
            'call_phone.digits' => 'El número de llamadas debe tener exactamente 9 dígitos.',

            // password
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe contener al menos 6 caracteres.',
        ]);

        $data = [
            'role_id'    => $request->role_id,
            'full_name'  => $request->full_name,
            'email'      => $request->email,
            'dni'        => $request->dni,
            'locality'   => $request->locality,
            'whatsapp'   => $request->whatsapp,
            'call_phone' => $request->call_phone,
            'address'    => $request->address,
            'password'   => Hash::make($request->password),
            'is_active'  => true
        ];

        // ============================
        // IMAGEN DE PERFIL (IGUAL A CLIENTE)
        // ============================
        if ($request->hasFile('profile_image')) {

            $path = public_path('assets/img/profile-image');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $filename = 'user_' . time() . '.' . $request->profile_image->extension();
            $request->profile_image->move($path, $filename);

            $data['profile_image'] = 'assets/img/profile-image/' . $filename;
        }

        User::create($data);

        return redirect()
            ->route('admin.config.employees')
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
            'full_name'  => 'required|string|max:150',
            'email'      => 'required|email|unique:users,email,' . $employee->id,
            'dni'        => 'required|digits:8|unique:users,dni,' . $employee->id,
            'whatsapp'   => 'nullable|digits:9',
            'call_phone' => 'nullable|digits:9',
            'password'   => 'nullable|min:6',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Si es admin, validar role_id
            'role_id'    => auth()->user()->role_id === 1 ? 'required|in:1,3' : '',
        ], [
            'full_name.required' => 'El nombre completo es obligatorio.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Debes ingresar un correo electrónico válido.',
            'email.unique'       => 'Otro usuario ya está usando este correo.',
            'dni.required'       => 'El DNI es obligatorio.',
            'dni.digits'         => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique'         => 'Otro usuario ya está usando este DNI.',
            'whatsapp.digits'   => 'El número de WhatsApp debe tener exactamente 9 dígitos.',
            'call_phone.digits' => 'El número de llamadas debe tener exactamente 9 dígitos.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'role_id.required'   => 'El rol del usuario es obligatorio.',
            'role_id.in'         => 'El rol seleccionado no es válido.',
        ]);

        $data = $request->only([
            'full_name',
            'email',
            'dni',
            'locality',
            'whatsapp',
            'call_phone',
            'address',
        ]);

        // Solo los admins pueden cambiar el rol
        if (auth()->user()->role_id === 1 && $request->filled('role_id')) {
            $data['role_id'] = $request->role_id;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // ============================
        // IMAGEN DE PERFIL (IGUAL A CLIENTE)
        // ============================
        if ($request->hasFile('profile_image')) {

            $path = public_path('assets/img/profile-image');

            // Crear carpeta si no existe
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Eliminar imagen anterior
            if ($employee->profile_image && File::exists(public_path($employee->profile_image))) {
                File::delete(public_path($employee->profile_image));
            }

            // Guardar nueva imagen
            $filename = 'user_' . $employee->id . '_' . time() . '.' .
                        $request->profile_image->extension();

            $request->profile_image->move($path, $filename);

            $data['profile_image'] = 'assets/img/profile-image/' . $filename;
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
