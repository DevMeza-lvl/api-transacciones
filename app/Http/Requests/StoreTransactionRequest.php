<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para validación de creación de transacciones.
 *
 * @bodyParam email_receptor string required Correo electrónico del usuario receptor existente. Example: receptor@example.com
 * @bodyParam monto number required Monto a transferir, entre 0.01 y 5000. Example: 150.50
 * @bodyParam descripcion string Opcional, descripción de la transferencia. Max 500 caracteres.
 *
 * Ejemplo de respuesta
 * @response 422 {
 *   "message": "El monto es obligatorio.",
 *   "errors": {
 *     "monto": ["El monto es obligatorio."]
 *   }
 * }
 */
class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'email_receptor' => 'required|email|exists:users,email',
        'monto' => 'required|numeric|min:0.01|max:5000',
        'descripcion' => 'nullable|string|max:500',
    ];
}
    public function messages(): array
    {
        return [
            'usuario_receptor_id.required' => 'El usuario receptor es obligatorio.',
            'usuario_receptor_id.exists' => 'El usuario receptor seleccionado no existe.',
            'usuario_receptor_id.different' => 'El usuario receptor debe ser diferente al emisor.',

            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un valor numérico.',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'monto.max' => 'El monto no puede exceder 5,000 USD.',

            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email_receptor' => 'correo del usuario receptor',
            'monto' => 'monto',
            'descripcion' => 'descripción',
        ];
    }
}
