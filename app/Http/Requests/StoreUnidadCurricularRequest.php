<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUnidadCurricularRequest extends FormRequest
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
            "nombre" => "required|string|unique:unidad_curriculars,nombre",
            "descripcion" => "nullable|string",
            "unidad_credito" => "required|numeric",
            "hora_teorica" => "required|numeric",
            "hora_practica" => "numeric|nullable",
            "periodo" => "required",
            "trimestre_id" => "required"
        ];
    }

    public function attributes()
    {
        return [
            "unidad_credito" => "required|numeric",
            "hora_teorica" => "horas teorícas",
            "hora_practica" => "horas practícas",
            "trimestre_id" => "trimestre"
        ];
    }
}
