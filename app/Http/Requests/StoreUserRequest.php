<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

    /**
     * Request para validación de creación de usuario.
     *
     * @bodyParam name string required Nombre del usuario.
     * @bodyParam email string required Correo único válido.
     * @bodyParam saldo_inicial number required Saldo inicial no negativo.
     * @bodyParam password string required Contraseña mínima 6 caracteres.
     * 
     * Ejemplo de respuesta
    * @response 422 {
    *   "message": "El nombre es obligatorio.",
    *   "errors": {
    *     "name": ["El nombre es obligatorio."]
    *   }
    * }
     */
class StoreUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'saldo_inicial' => 'required|numeric|min:0',
            'password' => 'required|string|min:6'
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

            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'saldo_inicial' => 'saldo inicial',
            'password' => 'contraseña',
        ];
    }
}
