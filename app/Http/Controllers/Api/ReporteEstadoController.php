<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReporteEstado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Bien;

class ReporteEstadoController extends Controller
{
    /**
     * Obtener el inventario/estatus de bienes filtrado por un Lapso Académico específico.
     */
    public function indexByLapso($lapsoId)
    {
        $reportes = ReporteEstado::with('bien')
            ->where('lapso_academico_id', $lapsoId)
            ->get();

        return response()->json($reportes);
    }

    /**
 * Registrar o actualizar el estado operativo de un bien en un lapso determinado y opcionalmente su foto.
 */
    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'bien_id'            => 'required|exists:biens,id',
            'lapso_academico_id' => 'required|exists:lapso_academicos,id',
            'estado_operativo'   => 'required|in:operativo,dañado,desincorporado',
            'fecha'              => 'required|date',
            'observaciones'      => 'nullable|string',
            'imagen'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // 👈 Validación de imagen
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $bien = Bien::findOrFail($validated['bien_id']);

            // 1. Si se adjuntó una nueva imagen, eliminar la anterior y actualizar la ruta
            if ($request->hasFile('imagen')) {
                if ($bien->imagen && Storage::disk('public')->exists($bien->imagen)) {
                    Storage::disk('public')->delete($bien->imagen);
                }

                $bien->imagen = $request->file('imagen')->store('bienes', 'public');
                $bien->save();
            }

            // 2. updateOrCreate para el reporte del lapso actual
            $reporte = ReporteEstado::updateOrCreate(
                [
                    'bien_id'            => $validated['bien_id'],
                    'lapso_academico_id' => $validated['lapso_academico_id'],
                ],
                [
                    'estado_operativo'   => $validated['estado_operativo'],
                    'fecha'              => $validated['fecha'],
                    'observaciones'      => $validated['observaciones'] ?? null,
                ]
            );

            return response()->json([
                'message' => 'Estatus y/o fotografía del bien actualizados con éxito.',
                'data'    => $reporte->load('bien'),
            ], 200);
        });
    }

    /**
     * Carga masiva de estatus para el inicio/cierre de lapso.
     */
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'lapso_academico_id' => 'required|exists:lapso_academicos,id',
            'fecha'              => 'required|date',
            'bienes'             => 'required|array',
            'bienes.*.bien_id'   => 'required|exists:biens,id',
            'bienes.*.estado_operativo' => 'required|in:operativo,dañado,desincorporado',
            'bienes.*.observaciones'    => 'nullable|string',
        ]);

        foreach ($validated['bienes'] as $item) {
            ReporteEstado::updateOrCreate(
                [
                    'bien_id'            => $item['bien_id'],
                    'lapso_academico_id' => $validated['lapso_academico_id'],
                ],
                [
                    'estado_operativo'   => $item['estado_operativo'],
                    'fecha'              => $validated['fecha'],
                    'observaciones'      => $item['observaciones'] ?? null,
                ]
            );
        }

        return response()->json([
            'message' => 'Inventario del lapso actualizado masivamente con éxito.',
        ]);
    }
}
