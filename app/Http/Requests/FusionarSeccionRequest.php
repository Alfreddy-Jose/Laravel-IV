<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FusionarSeccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seccion_origen_id'  => ['required', 'integer', 'exists:estadistica_inscripciones,id'],
            'seccion_destino_id' => ['required', 'integer', 'exists:estadistica_inscripciones,id', 'different:seccion_origen_id'],
        ];
    }
}
