<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\SedeController;
use App\Http\Controllers\Api\PnfController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LapsoAcademicoController;
use App\Http\Controllers\Api\MatriculaController;
use App\Http\Controllers\Api\TrayectoController;
use Illuminate\Support\Facades\Route;

// Ruta para probar
Route::get('/welcom', function () {
    return 'Hello World';
});

Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas por middleware de Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Rutas de Usuarios
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/user/create', [UserController::class, 'store']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::put('/user/{user}', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);
    Route::get('/get_roles', [UserController::class, 'getRoles']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rutas de Roles
    Route::get('/roles', [RolesController::class, 'index']);
    Route::post('/roles', [RolesController::class, 'store']);
    Route::put('/rol/{rol}', [RolesController::class, 'update']);
    Route::get('/roles_permisos', [RolesController::class, 'getRolesWithPermissions']);

    // Rutas de Pnfs
    Route::get('/pnfs', [PnfController::class, 'index']);
    Route::post('/pnf', [PnfController::class, 'store']);
    Route::get('/pnf/{pnf}', [PnfController::class, 'show']);
    Route::put('/pnf/{pnf}', [PnfController::class, 'update']);
    Route::delete('/pnf/{pnf}', [PnfController::class, 'destroy']);

    // Rutas de Sedes
/*    Route::get('/sedes', [SedeController::class, 'index']);
    Route::post('/sede', [SedeController::class, 'store']);
    Route::get('/sede/getPnf', [SedeController::class, 'getPnf']);
    Route::get('/sede/getUniversidad', [SedeController::class, 'getUniversidad']);
    Route::get('/sede/getEstados', [SedeController::class, 'getEstados']);
    Route::get('/sede/getMunicipios/{estado}', [SedeController::class, 'getMunicipios']);
    Route::get('/sede/{sede}', [SedeController::class, 'show']);
    Route::put('/sede/{sede}', [SedeController::class, 'update']);
    Route::delete('/sede/{sede}', [SedeController::class, 'destroy']);*/

        // --- RUTAS DE SEDES ---

    // 1. Rutas de Colección (Plural)
    Route::get('/sedes', [SedeController::class, 'index']);

    // 2. Rutas de Utilidad/Datos (Específicas)
    // Siempre van ANTES de las que tienen parámetros dinámicos
    Route::get('/sede/getPnf', [SedeController::class, 'getPnf']);
    Route::get('/sede/getUniversidad', [SedeController::class, 'getUniversidad']);
    Route::get('/sede/getEstados', [SedeController::class, 'getEstados']);
    Route::get('/sede/getMunicipios/{estado}', [SedeController::class, 'getMunicipios']);

    // 3. Rutas de Acción (POST)
    Route::post('/sede', [SedeController::class, 'store']);

    // 4. Rutas con Parámetro Dinámico (ID)
    // Se dejan al final para que no "atrapen" las peticiones de arriba
    Route::get('/sede/{sede}', [SedeController::class, 'show']);
    Route::put('/sede/{sede}', [SedeController::class, 'update']);
    Route::delete('/sede/{sede}', [SedeController::class, 'destroy']);

    // Rutas de los Lapso
    Route::get('/lapsos', [LapsoAcademicoController::class, 'index']);
    // Route::get('/lapsos/activos', [LapsoAcademicoController::class, 'lapsosActivos']);
    Route::post('/lapso', [LapsoAcademicoController::class, 'store']);
    Route::get('/lapso/{lapso_academico}', [LapsoAcademicoController::class, 'show']);
    Route::put('/lapso/{lapso_academico}', [LapsoAcademicoController::class, 'update']);
    Route::get('/get_tipoLapsos', [LapsoAcademicoController::class, 'get_tipoLapsos']);
    Route::delete('/lapso/{lapso_academico}', [LapsoAcademicoController::class, 'destroy']);

    // Rutas de Tipo de Matriculas
    Route::get('/matriculas', [MatriculaController::class, 'index']);
    Route::get('/matricula/{matricula}', [MatriculaController::class, 'show']);
    Route::post('/matricula', [MatriculaController::class, 'store']);
    Route::put('/matricula/{matricula}', [MatriculaController::class, 'update']);
    Route::delete('/matricula/{matricula}', [MatriculaController::class, 'destroy']);

    // Rutas de Trayectos
    Route::get('/trayectos', [TrayectoController::class, 'index']);
    // Route::get('/trayecto/{trayecto}', [TrayectoController::class, 'show']);
    Route::post('/trayecto', [TrayectoController::class, 'store']);
    // Route::put('/trayecto/{trayecto}', [TrayectoController::class, 'update']);
    Route::delete('/trayecto/{trayecto}', [TrayectoController::class, 'destroy']);

});
