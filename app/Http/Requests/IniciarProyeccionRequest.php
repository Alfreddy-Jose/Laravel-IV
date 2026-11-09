<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IniciarProyeccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Aseguramos que solo usuarios autenticados (coordinadores) puedan hacer esto
        return auth()->check();
    }

    public function rules(): bool|array
    {
        // Obtenemos el PNF del coordinador autenticado
        $pnfId = auth()->user()->pnf_id;

        return [
            'pnf_id' => 'required|integer|exists:pnfs,id',
            'ano_academico_id' => [
                'required',
                'integer',
                'exists:ano_academicos,id',
                //  Regla de Unicidad Compuesta evita duplicar proyecciones para este PNF en este año
                Rule::unique('proyecciones', 'ano_academico_id')->where(function ($query) use ($pnfId) {
                    return $query->where('pnf_id', $pnfId);
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ano_academico_id.required' => 'El año académico es obligatorio.',
            'ano_academico_id.exists' => 'El año académico seleccionado no es válido.',
            'ano_academico_id.unique' => 'Ya existe una proyección (o un borrador activo) para este PNF en el año académico seleccionado.',
        ];
    }
}
