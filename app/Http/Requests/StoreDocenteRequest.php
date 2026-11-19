<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreDocenteRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "persona_id" => "required|exists:personas,id",
            "pnf_id" => "required|exists:pnfs,id",
            "categoria" => "required|string|max:50",
            "fecha_inicio" => "required|date|before_or_equal:today",
            "fecha_fin" => "required|date",
            "dedicacion" => "required|string|max:50",
            "tipo" => "required|string|max:50",
        ];
    }

    public function attributes()
    {
        return [
            "persona_id" => "buscar persona",
            "pnf_id" => "pnf"
        ];
    }


    public function messages() :array
    {
        return [
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'fecha_inicio.before_or_equal' => 'La fecha de inicio no puede ser una fecha futura',
        ];
    }
}
