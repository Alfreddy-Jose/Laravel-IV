<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HistorialActividades;


class HistorialActividadesController extends Controller
{
    public function index()
    {
        // Traemos las últimas 10 actividades con sus relaciones anidadas
        // Cargamos: el usuario, el control de entrega, el tipo de documento, el docente y su persona.
        $actividades = HistorialActividades::with([
            'user',
            'controlEntrega.docente.persona',
            'controlEntrega.tipoDocumento'
        ])
            ->latest()
            ->limit(10)
            ->get();

        // Mapeamos los datos para enviarle al React un JSON limpio y fácil de leer
        $timeline = $actividades->map(function ($actividad) {
            // Validaciones de seguridad por si algún registro fue eliminado en cascada
            $usuarioNombre = $actividad->user ? $actividad->user->name : 'Usuario';
            $documento = $actividad->controlEntrega && $actividad->controlEntrega->tipoDocumento
                ? $actividad->controlEntrega->tipoDocumento->nombre
                : 'Documento';

            $docentePersona = $actividad->controlEntrega && $actividad->controlEntrega->docente
                ? $actividad->controlEntrega->docente->persona
                : null;

            $docenteNombre = $docentePersona
                ? $docentePersona->nombre . ' ' . $docentePersona->apellido
                : 'Docente';

            return [
                'id' => $actividad->id,
                'usuario' => $usuarioNombre,
                'accion' => $actividad->accion,
                'documento' => $documento,
                'docente' => $docenteNombre,
                'hace_cuanto' => $actividad->created_at->diffForHumans(),
                'fecha_completa' => $actividad->created_at->toIso8601String()
            ];
        });

        return response()->json($timeline, 200);
    }
}
