<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //Listar todos los usuarios
    public function index()
    {
        try {
            $users = User::with('role:id,name')->get();
            return response()->json($users, 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Buscar usuario por id
    public function show($id)
    {
        try {
            $user = User::with('role:id,name')->find($id);

            if (!$user) {
                return response()->json([
                    'message' => 'Este usuario no existe'
                ], 404);
            }

            return response()->json($user, 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    //Actualizar usuario si es su propia cuenta, admin puede actualizar cualquiera
    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);
    
            if (!$user) {
                return response()->json([
                    'message' => 'Este usuario no existe'
                ], 404);
            }

            $authUser = Auth::user();

            if ($authUser->id !== $user->id && $authUser->role->name !== 'administrador') {
                return response()->json([
                    'message' => 'No tienes permiso para actualizar este usuario'
                ], 403);
            }
    
            $validator = Validator::make($request->all(), [
                'nombre' => 'string|max:255',
                'apellido' => 'string|max:255',
                'password_actual' => 'required_with:password|string|min:6',
                'password' => 'nullable|string|min:6|confirmed',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Verifica la contraseña actual
            if ($request->filled('password')) {
                if (!Hash::check($request->password_actual, $user->password)) {
                    return response()->json([
                        'message' => 'La contraseña actual no es correcta'
                    ], 403);
                }

                $user->password = bcrypt($request->password);
            }

            $user->nombre = $request->nombre ?? $user->nombre;
            $user->apellido = $request->apellido ?? $user->apellido;
    
            $user->save();
    
            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'user' => $user
            ], 200);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}