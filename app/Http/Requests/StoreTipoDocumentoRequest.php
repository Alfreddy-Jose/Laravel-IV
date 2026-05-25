<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150|unique:tipo_documentos,nombre',
            'alcance' => 'required|in:docente,seccion',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un tipo de documento con ese nombre.',
            'alcance.in' => 'El alcance debe ser estrictamente "docente" o "seccion".',
        ];
    }
}
