<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSeccionRequest extends FormRequest
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
            "lapso_id" => "required",
            "pnf_id" => "required",
            "matricula_id" => "required",
            "trayecto_id" => "required",
            "sede_id" => "required",
        ];
    }

    public function attributes()
    {
        return [
            "lapso_id" => "lapso",
            "pnf_id" => "pnf",
            "matricula_id" => "tipo matricula",
            "trayecto_id" => "trayecto",
            "sede_id" => "sede",
        ];
    }
}
