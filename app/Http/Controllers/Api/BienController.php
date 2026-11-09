<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bien;
use Illuminate\Support\Facades\DB;
use App\Models\ReporteEstado;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BienController extends Controller
{
    /**
     * Listar todos los bienes registrados.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Obtener el pnf_id del usuario autenticado
        $pnfUser = $user->pnf;
        $pnfId = $pnfUser?->id;

        $lapsoId = $request->query('lapso_id')
            ?? $request->header('X-Lapso-Id')
            ?? $request->input('lapso_id');

        // Carga 'ultimoReporte' Y TAMBIÉN la relación 'lapsoAcademico' de ese reporte
        $query = Bien::with(['ultimoReporte' => function ($q) use ($lapsoId) {
            if ($lapsoId) {
                $q->where('lapso_academico_id', $lapsoId);
            }
        }, 'ultimoReporte.lapsoAcademico'])
        // Filtro condicional por PNF del usuario
        ->when($pnfId, function ($q) use ($pnfId) {
            $q->where('pnf_id', $pnfId);
        });

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('codigo_uni', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('serial', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado_operativo') && $request->estado_operativo !== 'todos') {
            $estado = $request->input('estado_operativo');

            if ($estado === 'operativo') {
                $query->where(function ($q) use ($lapsoId) {
                    $q->whereHas('ultimoReporte', function ($sub) use ($lapsoId) {
                        $sub->where('estado_operativo', 'operativo');
                        if ($lapsoId) {
                            $sub->where('lapso_academico_id', $lapsoId);
                        }
                    })->orDoesntHave('ultimoReporte');
                });
            } else {
                $query->whereHas('ultimoReporte', function ($q) use ($estado, $lapsoId) {
                    $q->where('estado_operativo', $estado);
                    if ($lapsoId) {
                        $q->where('lapso_academico_id', $lapsoId);
                    }
                });
            }
        }

        $query->latest();

        if ($request->query('per_page') === 'all') {
            return response()->json($query->get());
        }

        $perPage = $request->query('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    // Método para guardar un Bien/Equipo
    public function store(Request $request)
    {
        $user = auth()->user();

        // Obtener el pnf_id del usuario en sesión
        $pnfUser = $user->pnf;
        $pnfId = $pnfUser?->id ?? $request->input('pnf_id');

        $validated = $request->validate([
            'codigo_uni'         => 'required|string|unique:biens,codigo_uni',
            'nombre'             => 'required|string',
            'descripcion'        => 'nullable|string',
            'imagen'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validación de imagen (máx 2MB)
            'estado_operativo'   => 'nullable|string|in:operativo,dañado,desincorporado',
            'lapso_academico_id' => 'nullable|exists:lapso_academicos,id',
            'pnf_id'             => 'required|exists:pnfs,id',
        ]);

        // Procesar y guardar el archivo de imagen si viene en la petición
        $imagePath = null;
        if ($request->hasFile('imagen')) {
            $imagePath = $request->file('imagen')->store('bienes', 'public');
        }

        return DB::transaction(function () use ($validated, $pnfId, $imagePath) {
            // 1. Crear la ficha del bien asignando el pnf_id e imagen
            $bien = Bien::create([
                'codigo_uni'  => $validated['codigo_uni'],
                'nombre'      => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'imagen'      => $imagePath, // Guardar la ruta relativa de la foto
                'pnf_id'      => $pnfId,
            ]);

            // 2. Si se envió estado y lapso, registrar el estado inicial
            if (!empty($validated['estado_operativo']) && !empty($validated['lapso_academico_id'])) {
                $bien->reportesEstado()->create([
                    'lapso_academico_id' => $validated['lapso_academico_id'],
                    'estado_operativo'   => $validated['estado_operativo'],
                    'fecha'              => now()->toDateString(),
                    'observaciones'      => 'Estado inicial al registrar el bien.',
                ]);
            }

            return response()->json([
                'message' => 'Bien registrado con éxito con su estado inicial.',
                'data'    => $bien->load('ultimoReporte')
            ], 201);
        });
    }

    /**
     * Mostrar la ficha del bien con todo su historial de estados.
     */
    public function show($id)
    {
        $bien = Bien::with(['reportesEstado.lapsoAcademico'])->findOrFail($id);

        return response()->json($bien);
    }

    /**
     * Actualizar los datos estáticos del bien.
     */
    public function update(Request $request, $id)
    {
        $bien = Bien::findOrFail($id);

        // 1. Validar la entrada de datos
        $validated = $request->validate([
            'codigo_uni'         => 'required|string|max:100|unique:biens,codigo_uni,' . $id,
            'nombre'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string',
            'estado_operativo'   => 'required|string|in:operativo,dañado,desincorporado',
            'lapso_academico_id' => 'required|exists:lapso_academicos,id',
        ]);

        return DB::transaction(function () use ($bien, $validated) {
            // 2. Actualizar los datos generales del bien
            $bien->update([
                'codigo_uni'  => $validated['codigo_uni'],
                'nombre'      => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            // 3. Crear o actualizar el reporte de estado para el lapso académico activo
            // updateOrCreate evita duplicar reportes si ya se había generado uno para este lapso
            if (method_exists($bien, 'reportes')) {
                $bien->reportesEstado()->updateOrCreate(
                    ['lapso_academico_id' => $validated['lapso_academico_id']],
                    [
                        'estado_operativo' => $validated['estado_operativo'],
                        'fecha'            => now()->toDateString(),
                        'observaciones'    => 'Actualización de datos generales del bien.',
                    ]
                );
            } else {
                ReporteEstado::updateOrCreate(
                    [
                        'bien_id'            => $bien->id,
                        'lapso_academico_id' => $validated['lapso_academico_id'],
                    ],
                    [
                        'estado_operativo' => $validated['estado_operativo'],
                        'fecha'            => now()->toDateString(),
                        'observaciones'    => 'Actualización de datos generales del bien.',
                    ]
                );
            }

            // 4. Retornar respuesta con el bien actualizado y su relación cargada
            return response()->json([
                'message' => 'Bien actualizado con éxito.',
                'data'    => $bien->fresh()->load(['ultimoReporte.lapsoAcademico']),
            ], 200);
        });
    }

    /**
     * Eliminar un bien del registro.
     */
    public function destroy($id)
    {
        $bien = Bien::findOrFail($id);

        // Si el bien tiene una imagen guardada y existe en el disco público, la eliminamos
        if ($bien->imagen && Storage::disk('public')->exists($bien->imagen)) {
            Storage::disk('public')->delete($bien->imagen);
        }

        // Eliminamos el registro en la BD
        $bien->delete();

        return response()->json([
            'message' => 'Bien y su fotografía fueron eliminados del sistema.',
        ], 200);
    }

    /**
     * Genera el reporte PDF filtrado por PNF y por Estado Operativo (filtro rápido).
     */
    public function generarPdf(Request $request)
    {
        // 1. Obtener usuario autenticado y su PNF
        $user = auth()->user();
        $pnfUser = $user?->pnf;
        $pnfId = $pnfUser?->id;

        // 2. Consulta base
        $query = Bien::select('id', 'codigo_uni', 'nombre', 'descripcion', 'imagen', 'pnf_id')
            ->with(['pnf:id,nombre,abreviado', 'reportesEstado']);

        // 3. Filtrar por el PNF del usuario si aplica
        if ($pnfId) {
            $query->where('pnf_id', $pnfId);
        }

        // 4. Filtrar por el Estado Operativo a través de la relación reportesEstado
        $estadoOperativo = $request->query('estado_operativo') ?? $request->input('estado_operativo');

        if ($estadoOperativo && $estadoOperativo !== 'todos') {
            $query->whereHas('reportesEstado', function ($q) use ($estadoOperativo) {
                $q->where('estado_operativo', $estadoOperativo);
            });
        }

        $bienes = $query->get();

        // 5. Generar PDF
        $pdf = Pdf::loadView('pdf.inventario', compact('bienes', 'pnfUser', 'estadoOperativo'));
        $pdf->setPaper('a4', 'landscape');

        $nombreArchivo = 'INVENTARIO_BIENES_' . strtoupper($estadoOperativo ?? 'TODOS') . '_' . date('Y_m_d_His') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

}
