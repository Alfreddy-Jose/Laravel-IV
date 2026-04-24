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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
