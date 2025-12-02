<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registrar un usuario
     */
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:120',
            'email' => 'required|string|email|max:120|unique:users',
            'password' => 'required|string|min:6',
            'dni' => 'required|string|size:8|unique:users',
            'phone' => 'nullable|string|max:9',
            'locality' => 'nullable|string|max:150',
        ]);

        // obtener rol advertising_user
        $role = Role::where('name', 'advertising_user')->firstOrFail();

        $user = User::create([
            'role_id'   => $role->id,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'dni'       => $request->dni,
            'phone'     => $request->phone,
            'locality'  => $request->locality,
        ]);

        Auth::login($user);

        return redirect()->route('login')->with('success', 'Cuenta creada correctamente. Ahora inicia sesión.');
    }

    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Intento de login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = Auth::user();

        // Verificar si el usuario está activo
        if (!$user->is_active) {
            Auth::logout(); // Cerrar sesión inmediatamente

            return response()->json([
                'message' => 'Tu cuenta está desactivada. Comunícate con soporte o con el administrador.'
            ], 403);
        }

        // Rol del usuario
        $roleName = $user->role->name;

        // Redirección según rol (por ahora igual)
        $redirect = route('home');

        return response()->json([
            'message'  => 'Inicio de sesión correcto',
            'user'     => $user,
            'role'     => $roleName,
            'redirect' => $redirect
        ]);
    }

    /**
     * Logout de usuario
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
