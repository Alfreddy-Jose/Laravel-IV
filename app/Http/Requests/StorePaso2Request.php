<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaso2Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'proyeccion_id' => 'required|integer|exists:proyecciones,id',
            'detalles' => 'required|array|min:1',
            'detalles.*.id' => 'required|integer|exists:proyeccion_secciones,id',
            'detalles.*.seccion_destino' => 'required|string|max:100',
            'detalles.*.crear_seccion' => 'required|boolean',
            'detalles.*.tiene_reincorporados' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'proyeccion_id.required' => 'El ID de la proyección es obligatorio.',
            'detalles.required' => 'Debe enviar los detalles de la proyección.',
            'detalles.*.seccion_destino.required' => 'La sección destino es obligatoria.',
        ];
    }
}
