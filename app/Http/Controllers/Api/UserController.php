<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use function Illuminate\Log\log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::with('roles:id,name')
                ->select('id', 'name', 'lastname', 'email',)
                ->get()
                ->map(function ($user) {
                    // Si solo tiene un rol, puedes tomar el primero
                    $user->rol = $user->roles->pluck('name')->first();
                    return $user;
                });

            // retornar los usuarios
            return response()->json($users);
        } catch (\Throwable $e) {

            return response()->json([
                "message" => "Error al obtener los usuarios",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Crear el usuario
            Log::info('informacion recibida en el backend:', array($request->lastname));
            $user = User::create([
                'name' => $request['name'],
                'lastname' => $request['lastname'],
                'email' => $request['email'],
                'password' => Hash::make($request['password'])
            ]);

            return response()->json([
                "message" => "Usuario creado exitosamente",
                "user" => $user
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                "message" => "Error al crear el usuario",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($user)
    {

        $usuario = User::find($user);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario);


/*         $usuario = User::select('id', 'name', 'lastname', 'email')
            ->findOrFail($user);

        return response()->json($usuario); */
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
