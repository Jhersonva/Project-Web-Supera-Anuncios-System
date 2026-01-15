<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    /**
     * Registrar un usuario
     */
    public function register(Request $request)
    {
        try {

            $validated = $request->validate([
                'accept_terms' => 'required|accepted',

                'account_type' => 'required|in:person,business',

                'full_name' => 'required_if:account_type,person|max:120',
                'dni'       => 'nullable|required_if:account_type,person|size:8|unique:users,dni',

                'company_reason' => 'nullable|required_if:account_type,business|max:150',
                'ruc'            => 'nullable|required_if:account_type,business|size:11|unique:users,ruc',

                'email'    => 'required|email|max:120|unique:users,email',
                'password' => 'required|string|min:8',

                'phone'    => 'nullable|string|max:9|unique:users,phone',
                'locality' => 'nullable|string|max:150',
            ]);

            $role = Role::where('name', 'advertising_user')->firstOrFail();

            $user = User::create([
                'role_id' => $role->id,
                'account_type' => $validated['account_type'],

                'full_name'      => $validated['full_name'] ?? null,
                'dni'            => $validated['dni'] ?? null,
                'company_reason' => $validated['company_reason'] ?? null,
                'ruc'            => $validated['ruc'] ?? null,

                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone'    => $validated['phone'] ?? null,
                'locality' => $validated['locality'] ?? null,
            ]);

            Auth::login($user);

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'redirect' => route('home')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'errors' => $e->errors()
            ], 422);

        } catch (\Throwable $e) {

            Log::error($e);

            return response()->json([
                'message' => 'Error al crear la cuenta'
            ], 500);
        }
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
        if (!$user->privacy_policy_accepted) {
            return response()->json([
                'message' => 'Debes aceptar los términos',
                'redirect' => route('login'),
                'showPrivacy' => true
            ]);
        }

        return response()->json([
            'message'  => 'Inicio de sesión correcto',
            'redirect' => route('home')
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
