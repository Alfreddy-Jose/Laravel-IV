<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Coordinador;
use App\Models\Docente;
use App\Models\Pnf;

class StoreCoordinadorRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'docente_id' => [
                'required',
                'exists:docentes,id',
            ],
            'pnf_id' => [
                'required',
                'exists:pnfs,id',
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
            'docente_id.required' => 'El docente es obligatorio',
            'docente_id.exists' => 'El docente seleccionado no existe en el sistema',
            'pnf_id.required' => 'El PNF es obligatorio',
            'pnf_id.exists' => 'El PNF seleccionado no existe en el sistema',
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

            $this->validarDocenteActivo($validator);
            $this->validarCoordinacionActivaDocente($validator);
            $this->validarCoordinadorActivoPNF($validator);
            $this->validarSolapamientoFechas($validator);
        });
    }

    /**
     * Validar que el docente esté activo
     */
    private function validarDocenteActivo(Validator $validator): void
    {
        $docente = Docente::find($this->docente_id);

        if ($docente && isset($docente->activo) && !$docente->activo) {
            $validator->errors()->add(
                'docente_id',
                'El docente seleccionado no está activo en el sistema'
            );
        }
    }

    /**
     * Validar que el docente no tenga una coordinación activa
     */
    private function validarCoordinacionActivaDocente(Validator $validator): void
    {
        $coordinacionActiva = Coordinador::where('docente_id', $this->docente_id)
            ->where('activo', true)
            ->whereNull('fecha_fin')
            ->first();

        if ($coordinacionActiva) {
            $nombrePnf = $coordinacionActiva->pnf->nombre ?? 'desconocido';
            $validator->errors()->add(
                'docente_id',
                "El docente ya tiene una coordinación activa en el PNF: {$nombrePnf}"
            );
        }
    }

    /**
     * Validar que el PNF no tenga un coordinador activo
     */
    private function validarCoordinadorActivoPNF(Validator $validator): void
    {
        $coordinadorActivo = Coordinador::where('pnf_id', $this->pnf_id)
            ->where('activo', true)
            ->whereNull('fecha_fin')
            ->first();

        if ($coordinadorActivo) {
            $nombreDocente = 'desconocido';
            if ($coordinadorActivo->docente && $coordinadorActivo->docente->persona) {
                $nombreDocente = trim(
                    $coordinadorActivo->docente->persona->nombre . ' ' .
                        $coordinadorActivo->docente->persona->apellido
                );
            }

            $validator->errors()->add(
                'pnf_id',
                "El PNF ya tiene un coordinador activo: {$nombreDocente}"
            );
        }
    }

    /**
     * Validar que no haya solapamiento de fechas para el mismo docente
     */
    private function validarSolapamientoFechas(Validator $validator): void
    {
        $fechaFin = $this->fecha_fin ?? now()->addYears(10);

        $solapamiento = Coordinador::where('docente_id', $this->docente_id)
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
                'El docente ya tiene una coordinación registrada en ese período de tiempo'
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
            'docente_id' => 'docente',
            'pnf_id' => 'PNF',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de finalización',
            'activo' => 'estado activo',
        ];
    }
}
