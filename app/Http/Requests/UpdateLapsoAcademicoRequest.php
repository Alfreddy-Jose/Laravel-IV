<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLapsoAcademicoRequest extends FormRequest
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
            // validaciones
            "nombre_lapso" => "required|unique:lapso_academicos,nombre_lapso," . $this->route('lapso_academico')->id,
            "ano" => "required",
            "tipo_lapso_id" => "required|exists:tipo_lapsos,id"
        ];
    }

    public function attributes()
    {
        return [
            "nombre_lapso" => "nombre lapso",
            "ano" => "año",
            "tipo_lapso_id" => "tipo lapso"
        ];
    }
}
