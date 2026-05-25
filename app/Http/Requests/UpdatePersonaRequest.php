<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonaRequest extends FormRequest
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
            "cedula_persona" => "required|numeric|unique:personas,cedula_persona," . $this->route('persona')->id . ",id",
            "nombre" => "required|string",
            "apellido" => "required|string",
            "email" => "email|unique:personas,email," . $this->route('persona')->id . ",id",
            "direccion" => "max:255|nullable",
            "telefono" => [
                'nullable',
                'numeric',
                Rule::unique('personas', 'telefono')->ignore($this->route('persona')?->id)
            ]
        ];
    }

    public function messages()
    {
        return [
            "cedula_persona.unique" => "La cédula ingresada ya se encuentra registrada",
        ];
    }
}
