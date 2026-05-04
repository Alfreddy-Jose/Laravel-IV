<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSedeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "universidad_id" => "required|exists:universidads,id",
            "nro_sede" => "required|numeric|unique:sedes,nro_sede",
            "nombre_sede" => "required|string",
            "nombre_abreviado" => "required|string|min:3",
            "direccion" => "required|string",
            "municipio_id" => "required|exists:municipios,id_municipio",
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
            "nro_sede.required" => "El campo número sede es requerido",
            "nro_sede.unique" => "El campo número sede ya ha sido registrado",
            "integer" => "El campo :attribute debe ser un número entero"
        ]; 
    }
}
