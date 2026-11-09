<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\LapsoAcademico;

class UpdateLapsoAcademicoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $stopOnFirstFailure = true;

    public function rules(): array
    {
        $id = $this->route('lapso_academico'); // Extrae el ID del lapso desde la URL de la API
        $lapso = LapsoAcademico::findOrFail($id);
        $estado = $lapso->estado; // Accesor del modelo

        // REGLA 1: Si el lapso ya está CERRADO, bloqueo histórico absoluto
        if ($estado === 'cerrado') {
            throw ValidationException::withMessages([
                'error' => ['No se puede modificar un lapso académico que ya se encuentra cerrado.']
            ]);
        }

        // REGLA 2: Si el lapso está ACTIVO, SOLO se permite extender la fecha de fin
        if ($estado === 'activo') {
            return [
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            ];
        }

        // REGLA 3: Si está en PLANIFICACIÓN, se permite validar la estructura completa
        if ($estado === 'planificacion') {
            return [
                'nombre_lapso' => 'required|string|unique:lapso_academicos,nombre_lapso,' . $id,
                'ano_academico_id' => 'required|exists:ano_academicos,id',
                'tipo_lapso_id'    => 'required|exists:tipo_lapsos,id',
            ];
        }

        return [];
    }

    public function attributes()
    {
        return [
            "nombre_lapso" => "nombre lapso",
            "ano" => "año",
            "tipo_lapso_id" => "tipo lapso"
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_lapso.unique' => 'Ya existe un lapso académico con este mismo nombre registrado en el sistema.',
        ];
    }
}
