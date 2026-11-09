<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProyeccionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'ano_academico_id' => 'required|integer|exists:ano_academicos,id',
            // Validamos que venga la lista de secciones y sea un arreglo
            'secciones' => 'required|array|min:1',
            // Validaciones para cada elemento dentro del array de secciones
            'secciones.*.sede_id' => 'required|integer|exists:sedes,id',
            'secciones.*.trayecto_id' => 'required|integer|exists:trayectos,id',
            'secciones.*.seccion_origen' => 'required|string|max:100',
            'secciones.*.grupos_aprobados' => 'required|integer|min:0',
            'secciones.*.grupos_reprobados' => 'required|integer|min:0',
            'pnf_id' => 'required|integer|exists:pnfs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'ano_academico_id.required' => 'El año académico es obligatorio.',
            'secciones.required' => 'Debe enviar al menos una sección para guardar el paso 1.',
            'secciones.*.grupos_aprobados.min' => 'La cantidad de grupos aprobados no puede ser negativa.',
            'secciones.*.grupos_reprobados.min' => 'La cantidad de grupos reprobados no puede ser negativa.',
        ];
    }
}
