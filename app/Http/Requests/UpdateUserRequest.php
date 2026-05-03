<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user');

        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\sáéíóúÁÉÍÓÚñÑ]+$/',
            'lastname' => 'required|string|max:255|regex:/^[a-zA-Z\sáéíóúÁÉÍÓÚñÑ]+$/',
            'email' => [
                'required',
                'email',
                'max:255',
                // Si $userId es null (creación), solo valida unicidad.
                // Si $userId existe (edición), ignora ese ID.
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'rol' => 'required|exists:roles,id',
            // 'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'remove_avatar' => 'sometimes|boolean'
        ];
    }

    public function messages()
    {
        return [
            'rol.exists' => 'El rol seleccionado no es válido.',
            // 'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
        ];
    }
}
