<?php

namespace App\Http\Requests;

use App\Models\Persona;
use App\Models\Vocero;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreVoceroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

/**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'persona_id' => [
                'required',
                'exists:personas,id',
            ],
            'seccion_id' => [
                'required',
                'exists:seccions,id',
            ],
            'fecha_inicio' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'fecha_fin' => [
                'nullable',
                'date',
                'after:fecha_inicio',
            ],
            'activo' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'persona_id.required' => 'La persona es obligatoria',
            'persona_id.exists' => 'La persona seleccionada no existe en el sistema',
            'seccion_id.required' => 'La sección es obligatoria',
            'seccion_id.exists' => 'La sección seleccionada no existe en el sistema',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'fecha_inicio.before_or_equal' => 'La fecha de inicio no puede ser una fecha futura',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'activo.boolean' => 'El campo activo debe ser verdadero o falso',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validarPersonaActiva($validator);
            $this->validarVoceriaActivaPersona($validator);
            $this->validarVoceroActivoSeccion($validator);
            $this->validarSolapamientoFechas($validator);
        });
    }

    /**
     * Validar que la persona esté activa
     */
    private function validarPersonaActiva(Validator $validator): void
    {
        $persona = Persona::find($this->persona_id);

        if ($persona && isset($persona->activo) && !$persona->activo) {
            $validator->errors()->add(
                'persona_id',
                'La persona seleccionada no está activa en el sistema'
            );
        }
    }

    /**
     * Validar que la persona no tenga una vocería activa
     */
    private function validarVoceriaActivaPersona(Validator $validator): void
    {
        $voceriaActiva = Vocero::where('persona_id', $this->persona_id)
            ->where('activo', true)
            ->whereNull('fecha_fin')
            ->first();

        if ($voceriaActiva) {
            $nombreSeccion = $voceriaActiva->seccion->nombre ?? 'desconocida';
            $validator->errors()->add(
                'persona_id',
                "La persona ya tiene una vocería activa en la sección: {$nombreSeccion}"
            );
        }
    }

    /**
     * Validar que la Sección no tenga un vocero activo (Sustituye al PNF)
     */
    private function validarVoceroActivoSeccion(Validator $validator): void
    {
        $voceroActivo = Vocero::where('seccion_id', $this->seccion_id)
            ->where('activo', true)
            ->whereNull('fecha_fin')
            ->first();

        if ($voceroActivo) {
            $nombrePersona = 'desconocido';
            if ($voceroActivo->persona) {
                $nombrePersona = trim(
                    $voceroActivo->persona->nombre . ' ' .
                        $voceroActivo->persona->apellido
                );
            }

            $validator->errors()->add(
                'seccion_id',
                "La sección ya tiene un vocero activo: {$nombrePersona}"
            );
        }
    }

    /**
     * Validar que no haya solapamiento de fechas para la misma persona
     */
    private function validarSolapamientoFechas(Validator $validator): void
    {
        $fechaFin = $this->fecha_fin ?? now()->addYears(10);

        $solapamiento = Vocero::where('persona_id', $this->persona_id)
            ->where(function ($query) use ($fechaFin) {
                $query->whereBetween('fecha_inicio', [$this->fecha_inicio, $fechaFin])
                    ->orWhereBetween('fecha_fin', [$this->fecha_inicio, $fechaFin])
                    ->orWhere(function ($q) use ($fechaFin) {
                        $q->where('fecha_inicio', '<=', $this->fecha_inicio)
                            ->whereNull('fecha_fin');
                    });
            })
            ->exists();

        if ($solapamiento) {
            $validator->errors()->add(
                'fecha_inicio',
                'La persona ya tiene una vocería registrada en ese período de tiempo'
            );
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Establecer activo por defecto si no se envía
        if (!$this->has('activo')) {
            $this->merge([
                'activo' => true,
            ]);
        }

        // Si es activo, asegurar que fecha_fin sea null
        if ($this->boolean('activo')) {
            $this->merge([
                'fecha_fin' => null,
            ]);
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'persona_id' => 'persona',
            'seccion_id' => 'sección',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de finalización',
            'activo' => 'estado activo',
        ];
    }
}
