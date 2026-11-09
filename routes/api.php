<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ConfiguracionPnfController;
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
use App\Http\Controllers\Api\ProyeccionController;
use App\Http\Controllers\Api\TipoDocumentoController;
use App\Http\Controllers\Api\PeriodoAcademicoController;
use App\Http\Controllers\Api\AnoAcademicoController;
use App\Http\Controllers\Api\CensoController;
use App\Http\Controllers\Api\VoceroController;
use App\Http\Controllers\Api\BienController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReporteEstadoController;
use App\Http\Controllers\Api\BitacoraController;
use App\Http\Controllers\Api\EstadisticaInscripcionController;
use App\Http\Controllers\Api\RegistroPerController;
use Illuminate\Support\Facades\Route;

// Ruta para probar
Route::get('/welcom', function () {
    return 'Hello World';
});

// Rutas para generar PDF
Route::get('/secciones/pdf', [SeccionController::class, 'pdf']);

Route::middleware(['auth:sanctum', 'lapso.context'])->group(function () {
    // Rutas de Control de Entregas
    Route::get('/control_entregas', [ControlEntregaController::class, 'index']);
    Route::post('/control_entregas/toggle', [ControlEntregaController::class, 'store']);
    //configuracion del pnf
    Route::get('/configuracion_pnf/{pnfId}', [ConfiguracionPnfController::class, 'show']);
    Route::post('/configuracion_pnf', [ConfiguracionPnfController::class, 'store']);
    // Rutas de Proyecciones
    // Selects para el Wizard
    Route::get('/proyeccion/datos_selects', [ProyeccionController::class, 'obtenerDatosSelects']);
    // Datos para paso 1
    Route::post('/proyeccion/paso1', [ProyeccionController::class, 'prepararPlantillaPaso1']);
    // Guardar paso 1
    Route::post('/proyeccion/guardar_paso1', [ProyeccionController::class, 'store']);
    // Paso 2: Cálculo y Distribución
    Route::get('/proyeccion/{id}/paso2', [ProyeccionController::class, 'obtenerPaso2']);
    Route::post('/proyeccion/guardar_paso2', [ProyeccionController::class, 'storePaso2']);
    // Ruta para exportar en exel la proyección
    Route::post('/exportar_proyeccion', [ProyeccionController::class, 'exportarExcel']);
    // Motor de factibilidad docente: horas requeridas por materia vs. horas disponibles de profesores
    Route::get('/proyeccion/factibilidad_docente/{trayecto_id}', [ProyeccionController::class, 'getFactibilidadDocente']);
    // Ruta para registrar la proyección
    Route::post('/proyecciones/finalizar', [ProyeccionController::class, 'finalizarProyeccion'])
    ->name('proyecciones.finalizar');

    // Rutas para Estadisticas de inscripción

    Route::get('/estadistica_inscripcion', [EstadisticaInscripcionController::class, 'index']);

    // Cargar Excel
    Route::post('/estadistica_inscripcion/importar', [EstadisticaInscripcionController::class, 'importar']);

    Route::post('/estadistica_inscripcion/dividir', [EstadisticaInscripcionController::class, 'dividirSeccion']);

    Route::post('/estadistica_inscripcion/fusionar', [EstadisticaInscripcionController::class, 'fusionarSeccion']);


});

// Rutas protegidas por middleware de Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index']);

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
    Route::put('/user/{user}/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/{user}', [UserController::class, 'update']);
    Route::put('/user/{user}/password', [UserController::class, 'updatePassword']);
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
    Route::get('/proyecciones/pnf/{pnfId}/sedes', [ProyeccionController::class, 'getSedesByPnf']);
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
    Route::get('/lapsos/activos', [LapsoAcademicoController::class, 'indexActivos']);
    Route::get('/lapsos/planificacion', [LapsoAcademicoController::class, 'lapsosPlanificacion']);
    Route::post('/lapso/{lapso}/forzar-inicio', [LapsoAcademicoController::class, 'forzarInicioLapso']);
    Route::post('/lapso/{lapso}/forzar-cierre', [LapsoAcademicoController::class, 'forzarCierreLapso']);
    Route::post('/lapso', [LapsoAcademicoController::class, 'store']);
    Route::get('/lapso/{lapso_academico}', [LapsoAcademicoController::class, 'show']);
    Route::put('/lapso/{lapso_academico}', [LapsoAcademicoController::class, 'update']);
    Route::get('/getDataSelect', [LapsoAcademicoController::class, 'getDataSelect']);
    Route::delete('/lapso/{lapso_academico}', [LapsoAcademicoController::class, 'destroy']);

    // Rutas Para los Años Académicos
    Route::prefix('panel')->group(function () {
        Route::get('/cronograma', [AnoAcademicoController::class, 'indexPanel']);
        Route::post('/ano', [AnoAcademicoController::class, 'store']);
        Route::get('/ano/{id}', [AnoAcademicoController::class, 'show']);
        Route::put('/ano/{id}', [AnoAcademicoController::class, 'update']);
        Route::delete('/ano/{id}', [AnoAcademicoController::class, 'destroy']);
        // Botones de control absoluto
        Route::post('/ano/{id}/forzar-inicio', [AnoAcademicoController::class, 'forzarInicioAno']);
        Route::post('/ano/{id}/forzar-cierre', [AnoAcademicoController::class, 'forzarCierreAno']);
    });

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
    Route::get('/persona/get_secciones', [PersonaController::class, 'getSecciones']);
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
    Route::get('pnf/{pnfId}/unidades_curriculares', [UnidadCurricularController::class, 'getUnidadesByPnf']);
    Route::post('/unidad_curricular', [UnidadCurricularController::class, 'store']);
    Route::get('/unidad_curricular/{unidad_curricular}', [UnidadCurricularController::class, 'show']);
    Route::put('/unidad_curricular/{unidad_curricular}', [UnidadCurricularController::class, 'update']);
    Route::delete('/unidad_curricular/{unidad_curricular}', [UnidadCurricularController::class, 'destroy']);

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

    // Rutas de Historial de Actividades
    Route::get('/historial_actividades', [HistorialActividadesController::class, 'index']);

    // 2. Resource CRUD Estándar
    Route::apiResource('censo', CensoController::class)->names([
        'index'   => 'censo.index',
        'store'   => 'censo.store',
        'show'    => 'censo.show',
        'update'  => 'censo.update',
        'destroy' => 'censo.destroy',
    ]);
    // Para coincidir con getAllCensos que apunta a '/censos'
    Route::get('/censos', [CensoController::class, 'index']);
    Route::get('/censos/getEstudiantes', [CensoController::class, 'getEstudiantes']);
    Route::get('/censos/exportar-excel', [CensoController::class, 'exportarExcel']);

    // CRUD Principal de Voceros
    Route::get('/voceros', [VoceroController::class, 'index']);
    Route::post('/vocero', [VoceroController::class, 'store']);
    Route::get('/vocero/{vocero}', [VoceroController::class, 'show']);
    Route::put('/vocero/{vocero}', [VoceroController::class, 'update']);
    Route::delete('/vocero/{vocero}', [VoceroController::class, 'destroy']);

    // CRUD de Bienes (Ficha Estática)
    Route::apiResource('bienes', BienController::class);

    // Control de Inventario por Lapso
    Route::get('inventario/lapso/{lapsoId}', [ReporteEstadoController::class, 'indexByLapso']);
    Route::post('inventario/reporte', [ReporteEstadoController::class, 'storeOrUpdate']);
    Route::post('inventario/reporte-masivo', [ReporteEstadoController::class, 'storeBatch']);
    Route::get('/inventario/pdf', [BienController::class, 'generarPdf']);

    // Rutas para la biotacora Bitacoras
    Route::get('/bitacora', [BitacoraController::class, 'index']);
    Route::get('/bitacora/{id}', [BitacoraController::class, 'show']);

    // Ruta para descargar la plantilla de estadística de inscripción
    Route::get('/estadistica_inscripcion/descargar_plantilla', [EstadisticaInscripcionController::class, 'descargarPlantilla']);

    Route::prefix('per')->group(function () {
        Route::get('/', [RegistroPerController::class, 'index']);
        Route::get('/pdf', [RegistroPerController::class, 'generarPdf']);
        Route::get('/getDocentes', [RegistroPerController::class, 'getDocentes']);
        // ruta para obetener unidades curriculares segun docente
        Route::get('/getUnidadesCurricularesPorDocente/{docenteId}', [RegistroPerController::class, 'getUnidadesCurricularesPorDocente']);
        Route::post('/', [RegistroPerController::class, 'store']);
        Route::get('/{id}', [RegistroPerController::class, 'show']);
        Route::put('/{id}', [RegistroPerController::class, 'update']);
        Route::delete('/{id}', [RegistroPerController::class, 'destroy']);

    });
});
