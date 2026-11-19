<?php

namespace App\Http\Middleware;

use App\Models\LapsoAcademico;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea las peticiones de ESCRITURA cuando el lapso académico resuelto para
 * la petición está cerrado.
 *
 * El frontend ya oculta los botones de creación/edición/eliminación en modo
 * consulta, pero un cliente externo (Postman, scripts) podría intentar mutar
 * datos de un lapso cerrado. Este middleware blinda esa regla en el servidor.
 *
 * El lapso se resuelve con el mismo criterio que SetLapsoContext:
 *   1. Header `X-Lapso-Id` (ya normalizado por el middleware previo).
 *   2. Parámetro `lapso_id` (query o body).
 *   3. Instancia compartida `lapso_actual`.
 */
class EnsureLapsoIsOpen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Reunimos TODAS las referencias a lapso que pueda traer la petición,
        // no solo el header: así no se puede burlar enviando un X-Lapso-Id
        // "abierto" mientras el cuerpo apunta a un lapso cerrado.
        $candidatos = array_filter(
            [
                $request->header('X-Lapso-Id'),
                $request->query('lapso_id'),
                $request->input('lapso_id'),
                $request->input('lapso_academico_id'),
                app()->bound('lapso_actual') ? app('lapso_actual') : null,
            ],
            fn ($valor) => $valor !== null && $valor !== ''
        );

        // Sin contexto de lapso no hay nada que validar (p. ej. el sistema
        // todavía no tiene ningún lapso registrado).
        if (empty($candidatos)) {
            return $next($request);
        }

        foreach (array_unique($candidatos) as $lapsoId) {
            $lapso = LapsoAcademico::find($lapsoId);

            // Un lapso inexistente no debe bloquear la operación.
            if (! $lapso) {
                continue;
            }

            // El accessor `estado` contempla también el cierre heredado del año
            // académico padre (planificacion, cronograma, activo, cerrado).
            if ($lapso->estado === 'cerrado') {
                return response()->json([
                    'message' => 'No se pueden realizar modificaciones en un lapso académico cerrado.',
                ], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
