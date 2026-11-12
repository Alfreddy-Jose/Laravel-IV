<?php

namespace App\Http\Middleware;

use App\Models\LapsoAcademico;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resuelve y publica el contexto del lapso académico de la petición.
 *
 * Orden de resolución:
 *   1. Header `X-Lapso-Id` (lo envía el SPA desde localStorage).
 *   2. Parámetro `lapso_id` (query o body) para clientes que no usan header.
 *   3. Lapso "actual" resuelto en el backend (LapsoAcademico::actual()).
 *
 * El contexto resuelto se publica de dos formas:
 *   - `app('lapso_actual')` para los servicios internos.
 *   - Header normalizado `X-Lapso-Id` en la petición, del que dependen los
 *     scopes globales (p. ej. App\Models\Scopes\LapsoAcademicoScope).
 *
 * Si el sistema todavía no tiene lapsos registrados se deja pasar la petición
 * sin contexto, para no bloquear la creación del primer año/lapso académico.
 */
class SetLapsoContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Las peticiones de CORS (OPTIONS) nunca llevan contexto.
        if ($request->isMethod('OPTIONS')) {
            return $next($request);
        }

        // 2. Resolver el lapso solicitado o, en su defecto, el lapso actual.
        $lapso = $this->resolverLapso($request);

        if (! $lapso) {
            // 3. Sistema sin lapsos: no hay contexto que publicar. Se permite
            //    continuar para poder crear el primer año/lapso académico.
            Log::warning('Sin lapsos académicos disponibles para establecer contexto.', [
                'path' => $request->path(),
            ]);

            return $next($request);
        }

        // 4. Publicar el contexto normalizado.
        app()->instance('lapso_actual', $lapso->id);
        $request->headers->set('X-Lapso-Id', (string) $lapso->id);

        return $next($request);
    }

    /**
     * Devuelve el lapso solicitado por el cliente o el lapso actual del sistema.
     */
    private function resolverLapso(Request $request): ?LapsoAcademico
    {
        $solicitado = $request->header('X-Lapso-Id')
            ?? $request->query('lapso_id')
            ?? $request->input('lapso_id');

        if ($solicitado !== null && $solicitado !== '') {
            if (is_numeric($solicitado)) {
                $lapso = LapsoAcademico::find((int) $solicitado);

                if ($lapso) {
                    return $lapso;
                }

                Log::warning('El lapso solicitado no existe; se usará el lapso actual.', [
                    'lapso_id' => $solicitado,
                    'path' => $request->path(),
                ]);
            } else {
                Log::warning('El contexto de lapso recibido no es numérico; se usará el lapso actual.', [
                    'lapso_id' => $solicitado,
                    'path' => $request->path(),
                ]);
            }
        }

        // Sin contexto válido (o con un ID obsoleto) el backend resuelve el
        // lapso vigente. Esto evita el bloqueo total del SPA cuando el
        // localStorage se pierde o se selecciona un lapso eliminado.
        return LapsoAcademico::actual();
    }
}
