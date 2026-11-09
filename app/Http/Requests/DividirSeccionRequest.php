<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DividirSeccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seccion_origen_id'   => ['required', 'integer', 'exists:estadistica_inscripciones,id'],
            'tipo_accion'         => ['required', 'string', 'in:transferir,crear_nueva'],
            'estudiantes_a_mover' => ['required', 'integer', 'min:1'],

            // Requerido si va a transferir a una sección existente
            'seccion_destino_id'  => ['required_if:tipo_accion,transferir', 'nullable', 'integer', 'exists:estadistica_inscripciones,id', 'different:seccion_origen_id'],

            // Requerido si va a crear una sección nueva
            'nuevo_codigo'        => ['required_if:tipo_accion,crear_nueva', 'nullable', 'string', 'max:20'],
        ];
    }
}
