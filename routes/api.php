<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ControlEntregaController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\SedeController;
use App\Http\Controllers\Api\PnfController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LapsoAcademicoController;
use App\Http\Controllers\Api\MatriculaController;
use App\Http\Controllers\Api\TrayectoController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\Api\SeccionController;
use App\Http\Controllers\Api\UnidadCurricularController;
use App\Http\Controllers\Api\DocenteController;
use App\Http\Controllers\Api\CoordinadorController;
use App\Http\Controllers\Api\HistorialActividadesController;
use App\Http\Controllers\Api\TipoDocumentoController;
use Illuminate\Support\Facades\Route;

// Ruta para probar
Route::get('/welcom', function () {
    return 'Hello World';
});

// Rutas para generar PDF
Route::get('/secciones/pdf', [SeccionController::class, 'pdf']); // <-- Ruta para generar PDF de secciones

Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas por middleware de Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Obtener todas las notificaciones NO leídas del usuario logueado
    Route::get('/notificaciones', function () {
        return response()->json(auth()->user()->unreadNotifications);
    });

    // Marcar todas las notificaciones del usuario como leídas
    Route::post('/notificaciones/marcar-leidas', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success', 'message' => 'Notificaciones leídas']);
    });
    

    // Rutas de Usuarios
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/{userId}/permisos', [UserController::class, 'getUserPermissions']);
    Route::put('/user/{userId}/permisos', [UserController::class, 'updateUserPermissions']);
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
    Route::get('/lapsos/activos', [LapsoAcademicoController::class, 'lapsosActivos']);
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

    // Rutas de Personas
    Route::get('/personas', [PersonaController::class, 'index']);
    Route::post('/persona', [PersonaController::class, 'store']);
    Route::get('/persona/{persona}', [PersonaController::class, 'show']);
    Route::put('/persona/{persona}', [PersonaController::class, 'update']);
    Route::delete('/persona/{persona}', [PersonaController::class, 'destroy']);

    // Rutas de Secciones
    Route::get('/secciones', [SeccionController::class, 'index']);
    Route::get('/secciones/pnfs/sede/{sedeId}', [SeccionController::class, 'getPnfs']); // <- obtener pnfs segun sede
    Route::post('/seccion', [SeccionController::class, 'store']);
    Route::get('/seccion/getDataSelect', [SeccionController::class, 'getDataSelect']);
    Route::get('/seccion/{seccion}', [SeccionController::class, 'show']);
    Route::put('/seccion/{seccion}', [SeccionController::class, 'update']);
    Route::delete('/seccion/{seccion}', [SeccionController::class, 'destroy']);

    // Rutas De Unidad Curricular
    Route::get('/unidad_curricular', [UnidadCurricularController::class, 'index']);
    Route::get('/get_trimestres', [UnidadCurricularController::class, 'get_trimestres']);
    Route::get('/unidad_curricular/trimestres/{trayecto}', [UnidadCurricularController::class, 'getTrimestresByTrayecto']);
    Route::post('/unidad_curricular', [UnidadCurricularController::class, 'store']);
    Route::get('/unidad_curricular/{unidad_curricular}', [UnidadCurricularController::class, 'show']);
    Route::put('/unidad_curricular/{unidad_curricular}', [UnidadCurricularController::class, 'update']);
    Route::delete('/unidad_curricular/{unidad_curricular}', [ UnidadCurricularController::class, 'destroy']);

    // Rutas de Docentes
    Route::get('/docentes', [DocenteController::class, 'index']);
    Route::get('/docente/getDataSelect', [DocenteController::class, 'getDataSelect']);
    Route::get('/docente/{docente}', [DocenteController::class, 'show']);
    Route::post('/docente', [DocenteController::class, 'store']);
    Route::put('/docente/{docente}', [DocenteController::class, 'update']);
    Route::delete('/docente/{docente}', [DocenteController::class, 'destroy']);

    // Rutas para Coordinadores
    Route::get('/coordinadores', [CoordinadorController::class, 'index']);
    Route::get('/coordinador/getDocentes/{boolean}', [CoordinadorController::class, 'getDocentes']);
    // Route::get('/coordinador/{coordinador}', [CoordinadorController::class, 'show']);
    Route::post('/coordinador', [CoordinadorController::class, 'store']);
    // Route::put('/coordinador/{coordinador}', [CoordinadorController::class, 'update']);
    Route::delete('/coordinador/{coordinador}', [CoordinadorController::class, 'destroy']);

     // Rutas de Tipo de Documento
    Route::post('/documento', [TipoDocumentoController::class, 'store']);
    Route::get('/documentos', [TipoDocumentoController::class, 'index']);
    Route::delete('/documento/{id}', [TipoDocumentoController::class, 'destroy']);

    // Rutas de Control de Entregas
    Route::get('/control_entregas', [ControlEntregaController::class, 'index']);
    Route::post('/control_entregas/toggle', [ControlEntregaController::class, 'store']);

    // Rutas de Historial de Actividades
    Route::post('/historial_actividades', [HistorialActividadesController::class, 'store']);
    Route::get('/historial_actividades', [HistorialActividadesController::class, 'index']);

});

