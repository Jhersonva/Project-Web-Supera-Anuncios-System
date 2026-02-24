<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyUserMail;
use Illuminate\Support\Facades\Cache;

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
                'dni' => 'nullable|required_if:account_type,person|size:8|unique:users,dni',

                'company_reason' => 'nullable|required_if:account_type,business|max:150',
                'ruc' => 'nullable|required_if:account_type,business|size:11|unique:users,ruc',

                'email' => 'required|email|max:120|unique:users,email',
                'password' => 'required|string|min:8',

                'call_phone' => 'nullable|string|max:9|unique:users,call_phone',
                'locality' => 'nullable|string|max:150',
            ]);

            $token = Str::random(64);

            // guardar datos temporalmente
            Cache::put(
                'register_'.$token,
                $validated,
                now()->addMinutes(30)
            );

            // objeto temporal solo para el mail
            $user = (object) [
                'email' => $validated['email'],
                'full_name' => $validated['full_name'] ?? null,
                'verification_token' => $token,
                'verification_expires_at' => now()->addMinutes(30)
            ];

            Mail::to($validated['email'])->send(new VerifyUserMail($user));

            return response()->json([
                'message' => 'Revisa tu correo para verificar tu cuenta.'
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

    public function verify($token)
    {
        $data = Cache::get('register_'.$token);

        if (!$data) {
            return view('auth.verify', ['status' => 'expired']);
        }

        $role = Role::where('name', 'advertising_user')->firstOrFail();

        $user = User::create([
            'role_id' => $role->id,
            'account_type' => $data['account_type'],

            'full_name' => $data['full_name'] ?? null,
            'dni' => $data['dni'] ?? null,
            'company_reason' => $data['company_reason'] ?? null,
            'ruc' => $data['ruc'] ?? null,

            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'call_phone' => $data['call_phone'] ?? null,
            'locality' => $data['locality'] ?? null,

            'is_verified' => true,
            'verified_at' => now()
        ]);

        Cache::forget('register_'.$token);

        Auth::login($user);

        return view('auth.verify', ['status' => 'success']);
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

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();

            return response()->json([
                'message' => 'Tu cuenta está desactivada.'
            ], 403);
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
