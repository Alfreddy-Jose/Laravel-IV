<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\RegistroPer;
use App\Models\UnidadCurricular;

class UpdateRegistroPerRequest extends FormRequest
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
        // Capturamos el ID del registro PER que se está actualizando desde la ruta
        $perId = $this->route('id') ?? $this->route('per');

        return [
            'estudiante_id' => [
                'required',
                'exists:estudiantes,id',
                function ($attribute, $value, $fail) use ($perId) {
                    // Regla: Máximo 3 PER por estudiante (EXCLUYENDO el registro actual)
                    $conteo = RegistroPer::where('estudiante_id', $value)
                        ->where('id', '!=', $perId) // EXCLUSIÓN CLAVE
                        ->count();

                    if ($conteo >= 3) {
                        $fail('El estudiante ya cuenta con 3 registros PER (límite máximo alcanzado).');
                    }
                },
            ],
            'unidad_curricular_id' => [
                'required',
                'exists:unidad_curriculars,id',
                function ($attribute, $value, $fail) {
                    $materia = UnidadCurricular::find($value);

                    // Regla: No permite Proyecto ni materias anuales
                    if ($materia && ($materia->es_proyecto || $materia->periodo === 'ANUAL')) {
                        $fail('Esta Unidad Curricular no admite la modalidad PER (es Proyecto o Anual).');
                    }
                },
            ],
            'docente_id'   => 'required|exists:docentes,id',
            'calificacion' => 'required|numeric|min:0|max:100',
        ];
    }
}
