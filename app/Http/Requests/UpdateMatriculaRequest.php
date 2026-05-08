<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMatriculaRequest extends FormRequest
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
            "nombre" => "required|unique:matriculas,nombre," . $this->route('matricula')->id,
        ];
    }

    public function messages()
    {
        return [
            "required" => "El campo :attribute es requerido",
            "numero.unique" => "El campo número ya ha sido registrado",
            "integer" => "El campo :attribute debe ser un número entero"
        ];
    }}
