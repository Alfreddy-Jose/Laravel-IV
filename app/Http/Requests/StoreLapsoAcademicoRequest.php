<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreLapsoAcademicoRequest extends FormRequest
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
            "nombre_lapso" => "required|unique:lapso_academicos,nombre_lapso",
            'ano_academico_id' => 'required|exists:ano_academicos,id',
            // Aquí añadimos la validación condicional y única
            'tipo_lapso_id'    => [
                'required',
                'exists:tipo_lapsos,id',
                function ($attribute, $value, $fail) {
                    // 1. Validar que no exista YA ese mismo tipo de lapso en el año seleccionado
                    $existeRepetido = DB::table('lapso_academicos')
                        ->where('ano_academico_id', $this->ano_academico_id)
                        ->where('tipo_lapso_id', $value)
                        ->exists();

                    if ($existeRepetido) {
                        $fail('El año académico seleccionado ya tiene registrado un lapso de este tipo.');
                    }
                },
            ],

        ];
    }

    public function attributes()
    {
        return [
            "nombre_lapso" => "nombre lapso",
            "ano_academico_id" => "Año Académico",
            "tipo_lapso_id" => "tipo lapso"
        ];
    }
}
