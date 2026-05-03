<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSedeRequest extends FormRequest
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
            "nro_sede" => "required|numeric|unique:sedes,nro_sede" . ($this->sede ? ',' . $this->sede->id : ''),
            "nombre_sede" => "required|string",
            "nombre_abreviado" => "required|string|min:3",
            "direccion" => "required|string",
            "municipio_id" => "required"
        ];
    }

    public function attributes()
    {
        return [
            "nro_sede" => "numero sede",
            "nombre_sede" => " nombre",
            "nombre_abreviado" => "nombre abreviado",
            "municipio_id" => "municipio"
        ];
    }

    public function messages()
    {
        return [
            "required" => "El campo :attribute es requerido",
            "nro_sede.unique" => "El campo número sede ya ha sido registrado",
            "nro_sede.required" => "El campo número sede es requerido",
            "nro_sede.unique" => "El campo número sede ya ha sido registrado",
            "integer" => "El campo :attribute debe ser un número entero"
        ];
    }
}
