<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonaRequest extends FormRequest
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
            "cedula_persona" => "required|numeric|unique:personas,cedula_persona",
            "nombre" => "required|string",
            "apellido" => "required|string",
            "email" => "email|unique:personas,email",
            "direccion" => "max:255|nullable",
            "telefono" => "numeric|unique:personas,telefono"
        ];
    }

    public function messages()
    {
        return [
            "cedula_persona.unique" => "La cédula ingresada ya se encuentra registrada",
        ];
    }
}
