<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\RegistroPer;
use App\Models\UnidadCurricular;

class StoreRegistroPerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estudiante_id' => [
                'required',
                'exists:estudiantes,id',
                function ($attribute, $value, $fail) {
                    // Regla: Máximo 3 PER por estudiante
                    $conteo = RegistroPer::where('estudiante_id', $value)->count();
                    if ($conteo >= 3) {
                        $fail('El estudiante ha alcanzado el límite máximo permitido de 3 PER.');
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
                        $fail('Esta Unidad Curricular no admite la modalidad PER (Proyecto o materia Anual).');
                    }
                },
            ],
            'docente_id' => 'required|exists:docentes,id',
            'calificacion' => 'required|numeric|min:0|max:100',
        ];
    }
}
