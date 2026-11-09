<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Seccion;
use App\Models\Pnf;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Persona::query()->with(['municipio']);

        $user = $request->user();
        $pnfUsuario = $user?->pnf?->id;

        // 1. SI EL USUARIO PERTENECE A UN PNF:
        // Solo se muestran las personas que ya tienen perfil completado en SU PNF.
        if ($pnfUsuario) {
            $query->where(function ($q) use ($pnfUsuario) {
                // Estudiante completado en el PNF del usuario
                $q->whereHas('estudiante.secciones', function ($sub) use ($pnfUsuario) {
                    $sub->where('pnf_id', $pnfUsuario);
                })
                // O Docente completado en el PNF del usuario
                ->orWhereHas('docente', function ($sub) use ($pnfUsuario) {
                    $sub->where('pnf_id', $pnfUsuario);
                });
            });
        }
        // 2. SI ES ADMINISTRADOR GLOBAL (No tiene PNF fijo)
        // Permite filtrar opcionalmente si envía el parámetro ?pnf= desde el frontend
        elseif ($request->filled('pnf')) {
            $pnfParam = $request->pnf;
            $query->where(function ($q) use ($pnfParam) {
                $q->whereHas('estudiante.secciones', fn ($sub) => $sub->where('pnf_id', $pnfParam))
                  ->orWhereHas('docente', fn ($sub) => $sub->where('pnf_id', $pnfParam));
            });
        }

        // 3. Filtros adicionales de la tabla
        if ($request->filled('tipo_persona')) {
            $query->where('tipo_persona', $request->tipo_persona);
        }

        if ($request->filled('grado_inst')) {
            $query->where('grado_inst', $request->grado_inst);
        }

        // Cargar relaciones de perfil para la respuesta
        $personas = $query->with(['docente', 'estudiante.secciones.pnf'])
            ->latest('id')
            ->get();

        return response()->json($personas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        try {
            DB::beginTransaction();

            // 1. Validar los datos generales de la Persona
            // 2. Crear el registro en la tabla personas
            $persona = Persona::create($request->validated());

            // 3. Evaluar si es estudiante para poblar las tablas académicas
            if ($request->tipo_persona === 'ESTUDIANTE') {

                // Creamos el estudiante de forma explícita
                $estudiante = \App\Models\Estudiante::create([
                    'persona_id' => $persona->id
                ]);

                // Verificación de seguridad: si de React no llega "seccion_id",
                // buscamos el campo tal cual como se envíe en el request
                $seccion_id = $request->input('seccion_id');

                if ($seccion_id) {
                    // Sincronizamos de manera directa e impositiva en la base de datos
                    $estudiante->secciones()->attach($seccion_id, [
                        'estatus' => 'regular',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                } else {
                    // Esto te alertará en la consola de React si el ID de la sección está llegando vacío

                    return response()->json([
                        "error" => "La persona se creó, pero no se recibió el ID de la sección desde el formulario."
                    ], 422);
                }
            }


            // Confirmar transacción
            DB::commit();

            // retornando confirmacion a la vista
            return response()->json(["message" => "Persona creada exitosamente"]);


        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        // Cargamos tanto la ubicación como la parte académica de forma anidada
        $persona->load([
            // 1. Carga de Municipio y Estado
            'municipio' => function ($query) {
                $query->select('id_municipio', 'municipio', 'id_estado')
                    ->with(['estado' => function ($query) {
                        $query->select('id_estado', 'estado');
                    }]);
            },

            // 2. AJUSTADO: Carga académica apuntando a la tabla real "seccions"
            'estudiante' => function ($query) {
                $query->with(['secciones' => function ($q) {
                    // Cambiamos "secciones.id" por "seccions.id" y "seccions.nombre"
                    $q->select('seccions.id', 'seccions.nombre');
                }]);
            }
        ]);

        // Enviando datos estructurados al frontend (React)
        return response()->json($persona);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        // Editando registro
        $persona->update($request->validated());

        // 4. Lógica de asignación académica según el tipo de persona
        if ($request->tipo_persona === 'ESTUDIANTE') {

            // Buscamos si ya tiene un perfil de estudiante o lo creamos si es nuevo
            $estudiante = \App\Models\Estudiante::firstOrCreate([
                'persona_id' => $persona->id
            ]);

            // Sincronizamos la sección en la tabla pivote 'estudiante_seccion'.
            // 'sync' es perfecto aquí porque si ya existía una sección la reemplaza,
            // y si no existía, la crea sin duplicar registros.
            $estudiante->secciones()->sync([
                $request->seccion_id => ['estatus' => 'regular']
            ]);

        } else {
            // SI DEJA DE SER ESTUDIANTE:
            // Buscamos si tiene un perfil académico previo y desvinculamos sus secciones
            $estudiante = \App\Models\Estudiante::where('persona_id', $persona->id)->first();
            if ($estudiante) {
                $estudiante->secciones()->detach(); // Elimina la matrícula en la tabla intermedia
                $estudiante->delete();              // Opcional: elimina el perfil de estudiante
            }
        }

        // Enviando respuesta al frontend
        return response()->json(['message' => 'Persona Editada']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        try {
            DB::beginTransaction();

            // Eliminando el registro
            $persona->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Persona eliminada exitosamente'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Código de error de restricción de clave foránea en PostgreSQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la persona porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la persona',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Método para Seleccionar las Secciones
    public function getSecciones(Request $request)
    {
        // Captura el pnf_id si viene en la petición (ej: /api/secciones?pnf_id=2)
        // Si no viene, valdrá null.
        $pnfId = $request->query('pnf_id');

        // Llamamos al scope 'dePnf'. Puedes usar 'with' si necesitas traer relaciones.
        $secciones = Seccion::dePnf($pnfId)
            ->get();

        return response()->json($secciones);
    }


    /*    public function generarPDF(Request $request)
        {
            // Seleccionar las Personas
            $query = Persona::query()->with('municipio');

            if ($request->tipo_persona) {
                $query->where('tipo_persona', $request->tipo_persona);
            }
            if ($request->grado_inst) {
                $query->where('grado_inst', $request->grado_inst);
            }

            $personas = $query->get();
            // Renderiza una vista blade para el PDF
            $pdf = Pdf::loadView('pdf.personas', compact('personas'));

            return $pdf->download('personas.pdf');
        }*/
}
