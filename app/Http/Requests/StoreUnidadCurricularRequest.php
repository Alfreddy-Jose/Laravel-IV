<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUnidadCurricularRequest extends FormRequest
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
        $user = auth()->user();

        // Si el usuario no tiene PNF asignado, 'pnf_id' debe ser obligatorio en el request
        $pnfRule = $user->pnf?->id ? 'nullable' : 'required|exists:pnfs,id';

        return [
            "pnf_id"         => $pnfRule,
            "nombre"         => "required|string",
            "descripcion"    => "nullable|string",
            "unidad_credito" => "required|numeric",
            "hora_teorica"   => "required|numeric",
            "hora_practica"  => "numeric|nullable",
            "periodo"        => "required",
            "trimestre_id"   => "required"
        ];
    }

    public function attributes()
    {
        return [
            "pnf_id"         => "PNF",
            "unidad_credito" => "unidades de crédito",
            "hora_teorica"   => "horas teóricas",
            "hora_practica"  => "horas prácticas",
            "trimestre_id"   => "trimestre"
        ];
    }
}
