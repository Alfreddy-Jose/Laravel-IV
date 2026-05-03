<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Obtener usuario
        $user = User::where('email', $request->email)->first();

        // verificar credenciales
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Crear Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Obtener nombres de los permisos
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        // Obtener Rol del usuario
        $role = $user->roles->pluck('name')->first();

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            'token' => $token,
            'permissions' => $permissions,
            'role' => $role
        ], 200);
    }

    // Obtener el usuario autenticado
    public function user(Request $request)
    {
        $user = $request->user()->load(['roles']);

        return response()->json([
            'user' => $request->user()
        ]);
    }
}
