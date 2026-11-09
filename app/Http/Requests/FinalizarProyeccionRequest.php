<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalizarProyeccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proyeccion_id' => 'required|integer|exists:proyecciones,id',
            'estado'        => 'nullable|string',
        ];
    }
}
