<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnoAcademicoRequest extends FormRequest
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
            // El año debe ser un número entero de 4 dígitos y único en la tabla
            'ano'          => 'required|integer|digits:4|unique:ano_academicos,ano',
            'fecha_inicio' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        try {
                            $year = \Carbon\Carbon::parse($value)->year;
                            $exists = \App\Models\AnoAcademico::where('ano', $year)->exists();
                            if ($exists) {
                                $fail("Ya existe un año académico registrado para el año {$year}.");
                            }
                        } catch (\Exception $e) {
                            // Dejar que el validador 'date' maneje el formato incorrecto
                            
                        }
                    }
                }
            ],
            // La fecha fin debe ser obligatoriamente posterior a la de inicio
            'fecha_fin'    => 'required|date|after:fecha_inicio',
        ];
    }

    public function messages(): array
    {
        return [
            'ano.unique'         => 'Este año académico ya se encuentra registrado.',
            'fecha_fin.after'    => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ];
    }
}
