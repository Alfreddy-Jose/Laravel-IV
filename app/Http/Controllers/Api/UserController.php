<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

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

    public function store(StoreUserRequest $request)
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

            // Asignar el rol por ID (no por nombre)
            $role = Role::find($request['rol']);
            if ($role) {
                $user->assignRole($role);
            }

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
    // Metodo para Obtener datos del usuario
    public function show($usuario)
    {
        $user = User::with('roles:id,name')
            ->select('id', 'name', 'lastname', 'email')
            ->findOrFail($usuario);

        // Si solo tiene un rol, puedes tomar el primero
        $user->rol = $user->roles->pluck('id')->first();
        unset($user->roles); // Opcional: elimina la relación si no la necesitas

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $user)
    {
        $usuario = User::findOrFail($user);

        try {
            // Procesar el avatar
  /*          if ($request->has('remove_avatar') && $request->remove_avatar) {
                // Eliminar avatar existente
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                    $user->avatar = null;
                }
            } elseif ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                // Eliminar avatar anterior si existe
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                // Guardar nuevo avatar
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }*/

            // Actualizar usuario
            $usuario->update([
                'name' => $request['name'],
                'lastname' => $request['lastname'],
                'email' => $request['email'],
            ]);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request['password']);
            }

            // Actualizar rol
            $role = Role::find($request['rol']);
            if ($role) {
                $usuario->syncRoles([$role]);
            }

            return response()->json([
                'message' => 'Usuario editado exitosamente',
                'user' => $usuario
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(["Usuario eliminado exitosamente"]);
    }

    // Metodo para obtener los roles
    public function getRoles()
    {   
        $roles = Role::select('id', 'name')->get();
        return response()->json($roles);
    }
}
