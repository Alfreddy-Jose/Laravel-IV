<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetLapsoContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. SI ES UNA PETICIÓN DE CORS (OPTIONS), DEJARLA PASAR INMEDIATAMENTE
        if ($request->isMethod('OPTIONS')) {
            return $next($request);
        }

        // 2. Ahora sí, validar el header para las peticiones reales (GET, POST, etc.)
        $lapsoId = $request->header('X-Lapso-Id');

        if (!$lapsoId) {
            Log::error('El middleware no detectó el header X-Lapso-Id en una petición real.');
            return response()->json(['error' => 'Contexto de lapso académico requerido'], 400);
        }

        // Guardar el contexto
        app()->instance('lapso_actual', $lapsoId);

        return $next($request);
    }
}
