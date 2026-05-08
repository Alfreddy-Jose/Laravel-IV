<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }


        public function getRolesWithPermissions()
    {
        $roles = Role::with('permissions')->get()->map(function ($role) {
            $groupedPermissions = [];

            foreach ($role->permissions as $permission) {
                // Dividimos el permiso en módulo y acción
                $parts = explode('.', $permission->name);

                if (count($parts) === 2) {
                    $module = $parts[0];
                    $action = $parts[1];

                    if (!isset($groupedPermissions[$module])) {
                        $groupedPermissions[$module] = [];
                    }

                    $groupedPermissions[$module][] = [
                        'full_name' => $permission->name,
                        'action' => $action,
                        'id' => $permission->id
                    ];
                }
            }

            $role->groupedPermissions = $groupedPermissions;
            return $role;
        });

        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:roles,name',
            'permisos' => 'required|array',
            'permisos.*' => 'string|exists:permissions,name',
        ]);

        // Creando rol
        $rol = Role::create(['name' => $request->nombre, 'guard_name' => 'web']);

        // Asignar permisos seleccionados
        $rol->syncPermissions($request->permisos);

        return response()->json(["message" => "Rol Registrado"], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $rol)
    {
        $request->validate([
            'nombre' => 'required|string|unique:roles,name,' . $rol->id,
            'permisos' => 'required|array',
            'permisos.*' => 'string|exists:permissions,name',
        ]);
        // Actualizando rol
        $rol->name = $request->nombre;
        $rol->save();

        // Sincroniza los permisos
        $rol->syncPermissions($request->permisos);

        return response()->json(['message' => 'Rol Editado'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
