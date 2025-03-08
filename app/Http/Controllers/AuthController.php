<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    
    // Registro de usuario
    public function register(Request $request)
    {
        try {
            $request->validate([
                'nombre' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/'],
                'apellido' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/'],
                'correo' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role_id' => 'nullable|exists:roles,id',
            ], [
                'nombre.required' => 'Los nombres son obligatorios.',
                'nombre.regex' => 'El nombre no puede tener números ni caracteres especiales.',
                'apellido.required' => 'Los apellidos son obligatorios.',
                'apellido.regex' => 'El apellido no puede tener números ni caracteres especiales.',
                'correo.required' => 'El correo electrónico es obligatorio.',
                'correo.email' => 'Ingresa un correo electrónico válido.',
                'correo.unique' => 'El correo ingresado ya está registrado. Por favor, intenta con otro.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
                ]);

            $user = User::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id ?? 2,
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'user' => $user->load('role'),
                'token' => $token,
                'role' => $user->role->name
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->validator->errors()->first()
            ], 422);
        }
    }

    // Inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('correo', $request->correo)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'role' => $user->role]);
    }

    // Cierre de sesión
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}