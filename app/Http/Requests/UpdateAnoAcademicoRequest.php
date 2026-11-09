<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\AnoAcademico;
use Carbon\Carbon;

class UpdateAnoAcademicoRequest extends FormRequest
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

    /**
     * Obtener las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        $id = $this->route('id'); // Extrae el ID del año desde la URL de la API
        $anoAcademico = AnoAcademico::findOrFail($id);
        $estado = $anoAcademico->estado_real;

        // REGLA 1: Si está cerrado, bloquear cualquier intento de modificación
        if ($estado === 'cerrado') {
            throw ValidationException::withMessages([
                'error' => ['No se puede modificar un año académico que ya se encuentra cerrado.']
            ]);
        }

        // REGLA 2: Si está activo, SOLO se permite modificar la fecha de fin
        if ($estado === 'activo') {
            return [
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            ];
        }

        // REGLA 3: Si está en planificación, validación estructural completa
        if ($estado === 'planificacion') {
            return [
                // REGLA UNIQUE: Verifica la tabla 'ano_academicos', columna 'ano', ignorando este '$id'
                'ano'          => 'required|numeric|digits:4|unique:ano_academicos,ano,' . $id,
                'fecha_inicio' => 'required|date',
                'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            ];
        }

        return [];
    }

    /**
     * Configurar validaciones adicionales posteriores (Hooks de validación).
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $anoAcademico = AnoAcademico::findOrFail($this->route('id'));

            // Validación extra: Si está en planificación pero ya tiene hijos, no dejar cambiar el número de año
            if ($anoAcademico->estado_real === 'planificacion') {
                if ($anoAcademico->lapsos()->exists() && $anoAcademico->ano != $this->ano) {
                    $validator->errors()->add('ano', 'No puedes cambiar el número de año porque ya tiene lapsos académicos registrados.');
                }
            }
        });
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'ano.required'              => 'El campo año es obligatorio.',
            'ano.numeric'               => 'El año debe ser un valor numérico.',
            'ano.digits'                => 'El año debe tener exactamente 4 dígitos.',
            'fecha_inicio.required'     => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date'         => 'La fecha de inicio no es una fecha válida.',
            'fecha_fin.required'        => 'La fecha de fin es obligatoria.',
            'fecha_fin.date'            => 'La fecha de fin no es una fecha válida.',
            'fecha_fin.after_or_equal'  => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'ano.unique' => 'Ya existe un año académico registrado con este mismo año.',
        ];
    }
}
