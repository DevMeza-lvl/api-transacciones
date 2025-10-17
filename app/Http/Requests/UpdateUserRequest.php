<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para validación de actualización de usuario.
 *
 * Permite actualizar nombre, correo (único) y saldo inicial.
 *
 * @bodyParam name string optional Nuevo nombre del usuario. Max 255 caracteres. Example: Juan Pérez
 * @bodyParam email string optional Nuevo correo electrónico. Único (exceptuando el actual). Example: juan.perez@example.com
 * @bodyParam saldo_inicial number optional Nuevo saldo inicial. Debe ser no negativo. Example: 1500.50
 * @bodyParam password string optional Nueva contraseña. Mínimo 6 caracteres. Example: NuevaClaveSegura123
 * @response 422 {
 *   "message": "El correo electrónico es obligatorio.",
 *   "errors": {
 *     "email": ["El correo electrónico es obligatorio."]
 *   }
 * }
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user)
            ],
            'saldo_inicial' => 'sometimes|required|numeric|min:0|max:999999.99',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.max' => 'El correo electrónico no puede exceder 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'saldo_inicial.required' => 'El saldo inicial es obligatorio.',
            'saldo_inicial.numeric' => 'El saldo inicial debe ser un valor numérico.',
            'saldo_inicial.min' => 'El saldo inicial no puede ser negativo.',
            'saldo_inicial.max' => 'El saldo inicial no puede exceder 999,999.99.',
        ];
    }
}
