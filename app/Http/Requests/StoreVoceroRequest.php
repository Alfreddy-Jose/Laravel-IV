<?php

namespace App\Http\Requests;

use App\Models\Persona;
use App\Models\Vocero;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreVoceroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
         * Get the validation rules that apply to the request.
         */
    public function rules(): array
    {
        return [
            'estudiante_id' => [
                'required',
                'integer',
                'exists:estudiantes,id',
                // Evita que un mismo estudiante tenga más de un rol en la misma sección
                Rule::unique('voceros')->where(function ($query) {
                    return $query->where('seccion_id', $this->seccion_id);
                }),
            ],
            'seccion_id' => ['required', 'integer', 'exists:seccions,id'],
            'tipo_vocero' => [
                'required',
                'string',
                Rule::in(['PRINCIPAL', 'SUPLENTE', 'EJECUTIVO', 'PRODUCTIVO']),
                // Unicidad por tipo de vocero en la sección dada
                Rule::unique('voceros')->where(function ($query) {
                    return $query->where('seccion_id', $this->seccion_id);
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_vocero.unique'   => 'Ya existe un vocero asignado como ' . $this->tipo_vocero . ' para esta sección.',
            'estudiante_id.unique' => 'Este estudiante ya posee un cargo de vocería asignado en esta sección.',
            'estudiante_id.exists' => 'El estudiante seleccionado no está registrado.',
            'seccion_id.exists'    => 'La sección seleccionada no existe.',
        ];
    }
}
