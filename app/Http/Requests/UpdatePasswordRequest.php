<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true; // O pon tu lógica de autorización (ej. auth()->check())
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        // Obtenemos el usuario de la ruta (ej: /user/{usuario}/password) o el autenticado
        $user = $this->route('usuario') ?? $this->route('user') ?? auth()->user();

        return [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!$user || !Hash::check($value, $user->password)) {
                        $fail('La contraseña actual es incorrecta.');
                    }
                }
            ],
            'new_password' => [
                'required',
                'string',
                Password::min(8) // Mínimo 8 caracteres
                    ->letters()  // Al menos una letra
                    ->mixedCase() // Mayúsculas y minúsculas
                    ->numbers()   // Al menos un número
                    ->symbols(),  // Al menos un símbolo
                'different:current_password',
                'confirmed', // Requiere new_password_confirmation
            ],
        ];
    }

    /**
     * Traduce los nombres de los atributos para que aparezcan en español en los mensajes.
     */
    public function attributes(): array
    {
        return [
            'current_password' => 'contraseña actual',
            'new_password' => 'nueva contraseña',
        ];
    }

    /**
     * Mensajes personalizados opcionales en español.
     */
    public function messages(): array
    {
        return [
            'new_password.different' => 'La :attribute debe ser diferente a la contraseña actual.',
            'new_password.confirmed' => 'La confirmación de la :attribute no coincide.',
        ];
    }
}
